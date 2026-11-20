/**
 * Duolingo English Test player.
 *
 * Forward-only, one question at a time, like the real test. Handles per-question
 * and shared group timers, limited-replay audio, inline blanks, highlight capture,
 * multi-stage writing, multi-turn speaking and microphone recording.
 *
 * Responses are collected in the browser only. persistAnswer() is the single place
 * to add the POST to DET_CONFIG.answerUrl when answer saving is implemented.
 */
(function () {
    'use strict';

    const AUTO_ADVANCE_DELAY_MS = 300;
    const LOW_TIME_SEC = 10;

    const overview  = document.getElementById('detOverview');
    const questions = Array.from(document.querySelectorAll('.det-question'));
    const nav       = document.getElementById('detNav');
    const nextLabel = document.getElementById('detNextLabel');
    const finish    = document.getElementById('detFinish');
    const pill      = document.getElementById('detSectionPill');
    const timerEl   = document.getElementById('detTimer');
    const progress  = document.getElementById('detProgress');
    const posEl     = document.getElementById('detPos');

    document.getElementById('detTotal').textContent = questions.length;
    document.getElementById('detAnsweredTotal').textContent = questions.length;

    let current = -1;

    /* ── Timer ─────────────────────────────────────────────────────── */

    const groupDeadlines = {}; // group id => ms timestamp shared across its subtasks
    let timerHandle = null;

    function renderTime(seconds) {
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        timerEl.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        timerEl.classList.toggle('det-timer-low', seconds <= LOW_TIME_SEC);
    }

    function clearTimer() {
        clearInterval(timerHandle);
        timerHandle = null;
        timerEl.textContent = '--:--';
        timerEl.classList.remove('det-timer-low');
    }

    function runUntil(deadline, onExpire) {
        clearInterval(timerHandle);

        const tick = function () {
            const left = Math.max(0, Math.ceil((deadline - Date.now()) / 1000));
            renderTime(left);
            if (left === 0) {
                clearInterval(timerHandle);
                onExpire();
            }
        };

        timerHandle = setInterval(tick, 250);
        tick();
    }

    function runFor(seconds, onExpire) {
        if (seconds > 0) {
            runUntil(Date.now() + seconds * 1000, onExpire);
        } else {
            clearTimer();
        }
    }

    /** Subtasks without their own timer share one deadline per group. */
    function usesGroupTimer(card) {
        return !parseInt(card.dataset.answerTime, 10) && parseInt(card.dataset.groupLimit, 10) > 0;
    }

    function groupExpired(card) {
        const deadline = groupDeadlines[card.dataset.groupId];
        return usesGroupTimer(card) && deadline !== undefined && deadline <= Date.now();
    }

    /* ── Audio with limited replays ────────────────────────────────── */

    function setupAudio(wrap) {
        const audio     = wrap.querySelector('audio');
        const button    = wrap.querySelector('[data-role="audio-play"]');
        const label     = wrap.querySelector('[data-role="audio-label"]');
        const remaining = wrap.querySelector('[data-role="audio-remaining"]');

        const replays  = wrap.dataset.maxReplays;
        const maxPlays = replays === '' || replays === undefined ? Infinity : parseInt(replays, 10) + 1;
        let plays = 0;

        function refresh() {
            const left = maxPlays - plays;
            button.disabled = !audio.paused || left <= 0;
            label.textContent = audio.paused ? (plays ? 'Play again' : 'Play audio') : 'Playing…';
            if (maxPlays !== Infinity) {
                remaining.textContent = 'Plays left: ' + left;
            }
        }

        wrap.playClip = function () {
            if (plays >= maxPlays || !audio.paused) return;
            audio.currentTime = 0;
            // A rejected play (for example, autoplay blocked) does not use up a play.
            audio.play().then(function () {
                plays += 1;
                refresh();
            }).catch(refresh);
            refresh(); // Disable the button straight away, while the clip is still loading.
        };

        button.addEventListener('click', wrap.playClip);
        audio.addEventListener('ended', refresh);
        audio.addEventListener('pause', refresh);
        audio.addEventListener('error', function () {
            button.disabled = true;
            label.textContent = 'Audio unavailable';
        });
        refresh();
    }

    function startAutoplay(root) {
        root.querySelectorAll('[data-role="audio"][data-autoplay]').forEach(function (wrap) {
            if (wrap.offsetParent !== null) wrap.playClip();
        });
    }

    /**
     * Clips preload one question ahead. Preloading the whole test would tie up the
     * browser's connections and delay the clip needed right now. The current
     * question is left alone: changing preload while play() is starting makes
     * Chrome abort that play.
     */
    function preloadAudio(card) {
        if (!card) return;
        card.querySelectorAll('audio').forEach(function (audio) { audio.preload = 'auto'; });
    }

    function stopAudio(root) {
        root.querySelectorAll('audio').forEach(function (audio) { audio.pause(); });
    }

    /* ── Microphone recording ──────────────────────────────────────── */

    let micStream = null;
    let activeRecorder = null;

    function stopRecording() {
        if (activeRecorder && activeRecorder.state === 'recording') {
            activeRecorder.stop();
        }
    }

    function releaseMicrophone() {
        stopRecording();
        if (micStream) {
            micStream.getTracks().forEach(function (track) { track.stop(); });
            micStream = null;
        }
    }

    function setupRecorder(wrap) {
        const startBtn = wrap.querySelector('[data-role="record-start"]');
        const stopBtn  = wrap.querySelector('[data-role="record-stop"]');
        const status   = wrap.querySelector('[data-role="record-status"]');
        const playback = wrap.querySelector('[data-role="playback"]');

        if (!navigator.mediaDevices || !window.MediaRecorder) {
            startBtn.disabled = true;
            status.textContent = 'Recording is not supported in this browser, or the page is not on a secure connection.';
            return;
        }

        startBtn.addEventListener('click', async function () {
            startBtn.disabled = true;
            try {
                micStream = micStream || await navigator.mediaDevices.getUserMedia({ audio: true });
            } catch (err) {
                startBtn.disabled = false;
                status.textContent = 'Microphone access was blocked. Allow it in your browser and try again.';
                return;
            }

            // The student may have moved on while the permission prompt was open.
            if (wrap.offsetParent === null) {
                startBtn.disabled = false;
                return;
            }

            const chunks   = [];
            const recorder = new MediaRecorder(micStream);
            activeRecorder = recorder;

            recorder.addEventListener('dataavailable', function (e) {
                if (e.data.size) chunks.push(e.data);
            });
            recorder.addEventListener('stop', function () {
                const blob = new Blob(chunks, { type: recorder.mimeType });
                wrap.recording = blob;
                playback.src = URL.createObjectURL(blob);
                playback.style.display = '';
                startBtn.disabled = false;
                stopBtn.disabled = true;
                status.textContent = 'Recording captured. Record again to replace it.';
                activeRecorder = null;
            });

            recorder.start();
            stopBtn.disabled = false;
            status.textContent = 'Recording…';
        });

        stopBtn.addEventListener('click', stopRecording);
    }

    /* ── Highlighting ──────────────────────────────────────────────── */

    function renderHighlight(passage, selection) {
        const text = passage.textContent;
        passage.textContent = '';
        if (!selection) {
            passage.textContent = text;
            return;
        }
        const mark = document.createElement('mark');
        mark.textContent = text.slice(selection.start, selection.end);
        passage.append(text.slice(0, selection.start), mark, text.slice(selection.end));
    }

    function setupHighlight(card) {
        const passage = card.querySelector('[data-role="passage"]');
        const preview = card.querySelector('[data-role="highlight-preview"]');
        const clear   = card.querySelector('[data-role="highlight-clear"]');
        const empty   = preview.textContent;

        function capture() {
            const sel = window.getSelection();
            if (!sel || sel.isCollapsed || !sel.rangeCount) return;

            const range = sel.getRangeAt(0);
            if (!passage.contains(range.startContainer) || !passage.contains(range.endContainer)) return;

            const before = document.createRange();
            before.selectNodeContents(passage);
            before.setEnd(range.startContainer, range.startOffset);

            const raw   = range.toString();
            const start = before.toString().length + (raw.length - raw.trimStart().length);
            const value = raw.trim();
            if (!value) return;

            card.highlight = { start: start, end: start + value.length, text: value };
            preview.textContent = value;
            sel.removeAllRanges();
            renderHighlight(passage, card.highlight);
        }

        passage.addEventListener('mouseup', capture);
        passage.addEventListener('touchend', capture);
        clear.addEventListener('click', function () {
            card.highlight = null;
            preview.textContent = empty;
            renderHighlight(passage, null);
        });
    }

    /* ── Multi-step questions ──────────────────────────────────────── */

    /**
     * Interactive Writing stages and Interactive Speaking turns share one flow:
     * each step has its own timer, and Next moves through steps before the question ends.
     */
    const STEP_SELECTOR = {
        interactive_writing: '[data-role="stage"]',
        interactive_speaking: '[data-role="turn"]',
    };

    function stepsOf(card) {
        const selector = STEP_SELECTOR[card.dataset.responseType];
        return selector ? Array.from(card.querySelectorAll(selector)) : [];
    }

    function enterStep(card, index) {
        const steps = stepsOf(card);
        card.stepIndex = index;

        steps.forEach(function (step, i) {
            step.style.display = i === index ? '' : 'none';
            stopAudio(step);
        });

        // A finished writing stage stays visible for reference but cannot be edited.
        if (card.dataset.responseType === 'interactive_writing' && index > 0) {
            for (let i = 0; i < index; i++) {
                steps[i].style.display = '';
                steps[i].querySelector('textarea').readOnly = true;
            }
        }

        const step = steps[index];
        nextLabel.textContent = index < steps.length - 1 ? 'Continue' : 'Next';
        runFor(parseInt(step.dataset.time, 10) || 0, advance);
        startAutoplay(step);
    }

    /* ── Response collection ───────────────────────────────────────── */

    function collect(card) {
        const value = function (selector) {
            const el = card.querySelector(selector);
            return el ? el.value : null;
        };

        switch (card.dataset.responseType) {
            case 'fill_in_blanks': {
                const blanks = {};
                card.querySelectorAll('[data-role="blank"]').forEach(function (input) {
                    if (input.value.trim()) blanks[input.dataset.order] = input.value.trim();
                });
                return Object.keys(blanks).length ? { response_blanks: blanks } : null;
            }
            case 'word_classification': {
                const choice = value('[data-role="word-choice"]:checked');
                return choice ? { response_text: choice } : null;
            }
            case 'mcq_single': {
                const option = value('[data-role="option"]:checked');
                return option ? { selected_option_ids: [parseInt(option, 10)] } : null;
            }
            case 'highlighting':
                return card.highlight ? { response_highlight: card.highlight } : null;
            case 'dictation':
            case 'writing':
            case 'essay': {
                const text = (value('[data-role="text-input"]') || '').trim();
                return text ? { response_text: text } : null;
            }
            case 'interactive_writing': {
                const texts = Array.from(card.querySelectorAll('[data-role="stage-text"]')).map(function (t) { return t.value.trim(); });
                return texts.some(Boolean) ? { response_texts: texts } : null;
            }
            case 'speaking': {
                const wrap = card.querySelector('[data-role="recorder"]');
                return wrap.recording ? { audio_blob: wrap.recording } : null;
            }
            case 'interactive_speaking': {
                const recordings = {};
                stepsOf(card).forEach(function (turn, i) {
                    const wrap = turn.querySelector('[data-role="recorder"]');
                    if (wrap.recording) recordings[i + 1] = wrap.recording;
                });
                return Object.keys(recordings).length ? { audio_blobs: recordings } : null;
            }
            default:
                return null;
        }
    }

    const responses = {}; // mapping id => collected response

    /** Answer saving is not implemented yet. Responses stay in memory for this page view. */
    function persistAnswer(mappingId, response) {
        responses[mappingId] = response;
        document.getElementById('detAnsweredCount').textContent = Object.keys(responses).length;
    }

    /* ── Navigation ────────────────────────────────────────────────── */

    function leave(card) {
        const store = function () {
            const response = collect(card);
            if (response) persistAnswer(card.dataset.mappingId, response);
        };

        stopAudio(card);

        // A recording only becomes available after the recorder's asynchronous stop event.
        if (activeRecorder && activeRecorder.state === 'recording') {
            activeRecorder.addEventListener('stop', function () { setTimeout(store, 0); });
            stopRecording();
        } else {
            store();
        }
    }

    function showFinish() {
        current = questions.length;
        clearTimer();
        releaseMicrophone();
        nav.style.display = 'none';
        progress.style.display = 'none';
        pill.textContent = 'Finished';
        finish.style.display = '';
    }

    function show(index) {
        const card = questions[index];
        current = index;

        questions.forEach(function (q, i) { q.style.display = i === index ? '' : 'none'; });
        pill.textContent = card.dataset.section || '';
        posEl.textContent = index + 1;
        nextLabel.textContent = 'Next';
        window.scrollTo(0, 0);
        preloadAudio(questions[index + 1]);

        const steps = stepsOf(card);
        if (steps.length) {
            enterStep(card, 0);
            return;
        }

        if (usesGroupTimer(card)) {
            const id = card.dataset.groupId;
            if (groupDeadlines[id] === undefined) {
                groupDeadlines[id] = Date.now() + parseInt(card.dataset.groupLimit, 10) * 1000;
            }
            runUntil(groupDeadlines[id], advance);
        } else {
            runFor(parseInt(card.dataset.answerTime, 10) || 0, advance);
        }

        startAutoplay(card);
    }

    /** Move on one step: the next stage or turn if there is one, otherwise the next question. */
    function advance() {
        const card = questions[current];
        if (!card) return;

        const steps = stepsOf(card);
        if (steps.length && card.stepIndex < steps.length - 1) {
            stopRecording();
            enterStep(card, card.stepIndex + 1);
            return;
        }

        leave(card);

        let next = current + 1;
        while (next < questions.length && groupExpired(questions[next])) {
            next += 1;
        }

        if (next < questions.length) {
            show(next);
        } else {
            questions.forEach(function (q) { q.style.display = 'none'; });
            showFinish();
        }
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-det-action]');
        if (!btn) return;

        if (btn.dataset.detAction === 'begin') {
            overview.style.display = 'none';
            nav.style.display = '';
            progress.style.display = '';
            show(0);
        } else if (btn.dataset.detAction === 'next') {
            advance();
        }
    });

    /* ── One-time setup ────────────────────────────────────────────── */

    document.querySelectorAll('[data-role="audio"]').forEach(setupAudio);
    document.querySelectorAll('[data-role="recorder"]').forEach(setupRecorder);
    document.querySelectorAll('[data-role="highlight-preview"]').forEach(function (preview) {
        setupHighlight(preview.closest('.det-question'));
    });

    // Yes/No word tasks move on as soon as the student chooses.
    document.querySelectorAll('[data-role="word-choice"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            const card = radio.closest('.det-question');
            setTimeout(function () {
                if (questions[current] === card) advance();
            }, AUTO_ADVANCE_DELAY_MS);
        });
    });

    function countWords(text) {
        const trimmed = text.trim();
        return trimmed ? trimmed.split(/\s+/).length : 0;
    }

    document.querySelectorAll('textarea[data-word-count]').forEach(function (textarea) {
        const counter = textarea.parentElement.querySelector('[data-role="word-count"]');
        if (!counter) return;
        const update = function () { counter.textContent = countWords(textarea.value); };
        textarea.addEventListener('input', update);
        update();
    });
})();
