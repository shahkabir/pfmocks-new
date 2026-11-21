/**
 * Duolingo English Test player.
 *
 * Forward-only, one question at a time, like the real test. Handles per-question
 * and shared group timers, limited-replay audio, inline blanks, highlight capture,
 * multi-stage writing, multi-turn speaking and microphone recording.
 *
 * Every response is saved to the server as soon as it is given, with a short
 * notification. The current position, timers and audio plays are saved too, so a
 * refresh or a later visit resumes in the same place.
 */
(function () {
    'use strict';

    const CFG = window.DET_CONFIG;

    const AUTO_ADVANCE_DELAY_MS = 300;
    const LOW_TIME_SEC          = 10;
    const TEXT_SAVE_DELAY_MS    = 1500;
    const BLANK_SAVE_DELAY_MS   = 1000;
    const STATE_SAVE_EVERY_MS   = 10000;
    const SAVE_RETRIES          = 3;
    const TOAST_MS              = 1800;

    const overview  = document.getElementById('detOverview');
    const questions = Array.from(document.querySelectorAll('.det-question'));
    const nav       = document.getElementById('detNav');
    const nextLabel = document.getElementById('detNextLabel');
    const finish    = document.getElementById('detFinish');
    const pill      = document.getElementById('detSectionPill');
    const timerEl   = document.getElementById('detTimer');
    const progress  = document.getElementById('detProgress');
    const posEl     = document.getElementById('detPos');
    const toasts    = document.getElementById('detToasts');
    const submitBtn = document.getElementById('detSubmitButton');
    const pendingEl = document.getElementById('detPendingSaves');

    document.getElementById('detTotal').textContent = questions.length;
    document.getElementById('detAnsweredTotal').textContent = questions.length;

    let current  = -1;
    let finished = false;

    /* ── Notifications ─────────────────────────────────────────────── */

    let toastEl = null;
    let toastTimer = null;

    /** One notification at a time: a new message replaces the previous one. */
    function notify(message, kind) {
        if (!toastEl) {
            toastEl = document.createElement('div');
            toastEl.setAttribute('role', 'status');
            toasts.appendChild(toastEl);
        }
        toastEl.className = 'det-toast det-toast-' + (kind || 'success');
        toastEl.innerHTML = '';
        const icon = document.createElement('i');
        icon.className = 'bi me-2 ' + (kind === 'error' ? 'bi-exclamation-triangle-fill' : 'bi-cloud-check-fill');
        toastEl.append(icon, message);
        toastEl.classList.add('show');

        clearTimeout(toastTimer);
        if (kind !== 'error') {
            toastTimer = setTimeout(function () { toastEl.classList.remove('show'); }, TOAST_MS);
        }
    }

    /* ── Server requests ───────────────────────────────────────────── */

    let pendingSaves = 0;

    function updatePending(delta) {
        pendingSaves += delta;
        if (submitBtn) submitBtn.disabled = pendingSaves > 0;
        if (pendingEl) pendingEl.style.display = pendingSaves > 0 ? '' : 'none';
    }

    const wait = function (ms) { return new Promise(function (r) { setTimeout(r, ms); }); };

    /** POST with retries for network and server errors. Resolves with the JSON reply. */
    async function post(url, body) {
        let lastError = null;
        for (let attempt = 0; attempt < SAVE_RETRIES; attempt++) {
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CFG.csrf, 'Accept': 'application/json' },
                    body: body,
                    credentials: 'same-origin',
                });
                if (res.ok) return res.json();

                const data = await res.json().catch(function () { return {}; });
                if (res.status === 419 || res.status === 401) {
                    throw Object.assign(new Error('Your session has expired. Reload the page to continue; your saved answers are kept.'), { final: true });
                }
                if (res.status < 500) {
                    throw Object.assign(new Error(data.message || 'This answer could not be saved.'), { final: true });
                }
                lastError = new Error('The server could not save your answer.');
            } catch (err) {
                if (err.final) throw err;
                lastError = err;
            }
            await wait(1500 * (attempt + 1));
        }
        throw lastError || new Error('Could not save your answer.');
    }

    /* ── Answer saving ─────────────────────────────────────────────── */

    const answered   = new Set(CFG.answeredIds || []);
    const lastSent   = new Map();     // card => JSON of the last saved payload
    const queues     = new Map();     // card => promise chain, so saves for one question stay in order
    const debounces  = new Map();     // card => timeout id

    function refreshAnsweredCount() {
        document.getElementById('detAnsweredCount').textContent = answered.size;
    }

    /** Runs saves for one question one after another. */
    function enqueue(card, task) {
        updatePending(1);
        const run = (queues.get(card) || Promise.resolve())
            .then(task)
            .then(function () {
                answered.add(card.dataset.mappingId);
                refreshAnsweredCount();
                notify('Answer saved');
            })
            .catch(function (err) { notify(err.message || 'Could not save your answer.', 'error'); })
            .finally(function () { updatePending(-1); });
        queues.set(card, run);
        return run;
    }

    /** The response of a typed or selected question, including empty values. */
    function payloadOf(card) {
        const value = function (selector) {
            const el = card.querySelector(selector);
            return el ? el.value : null;
        };

        switch (card.dataset.responseType) {
            case 'fill_in_blanks': {
                const blanks = {};
                card.querySelectorAll('[data-role="blank"]').forEach(function (input) {
                    blanks[input.dataset.order] = input.value.trim();
                });
                return { response_blanks: blanks };
            }
            case 'word_classification':
                return { response_text: value('[data-role="word-choice"]:checked') };
            case 'mcq_single': {
                const option = value('[data-role="option"]:checked');
                return { selected_option_ids: option ? [parseInt(option, 10)] : [] };
            }
            case 'highlighting':
                return { response_highlight: card.highlight || null };
            case 'dictation':
            case 'writing':
            case 'essay':
                return { response_text: value('[data-role="text-input"]') || '' };
            case 'interactive_writing':
                return {
                    response_texts: Array.from(card.querySelectorAll('[data-role="stage-text"]'))
                        .map(function (t) { return t.value; }),
                };
            default:
                return null; // Recordings are saved separately when they stop.
        }
    }

    /** Saves the question's response now, if it changed since the last save. */
    function saveNow(card) {
        clearTimeout(debounces.get(card));
        debounces.delete(card);

        const payload = payloadOf(card);
        if (!payload) return Promise.resolve();

        const json = JSON.stringify(payload);
        if (lastSent.get(card) === json) return Promise.resolve();
        lastSent.set(card, json);

        return enqueue(card, function () {
            const body = new FormData();
            body.append('mapping_id', card.dataset.mappingId);
            body.append('payload', json);
            return post(CFG.answerUrl, body);
        });
    }

    function saveSoon(card, delay) {
        clearTimeout(debounces.get(card));
        debounces.set(card, setTimeout(function () { saveNow(card); }, delay));
    }

    function saveRecording(card, blob, turn) {
        const ext = (blob.type.match(/audio\/(webm|ogg|mp4|wav)/) || [null, 'webm'])[1];
        return enqueue(card, function () {
            const body = new FormData();
            body.append('mapping_id', card.dataset.mappingId);
            body.append('audio_blob', blob, 'recording.' + ext);
            if (turn) body.append('turn', turn);
            return post(CFG.answerUrl, body);
        });
    }

    /* ── Exam state (position, timers, plays) ──────────────────────── */

    let currentDeadline = null; // ms timestamp of the running question or step timer
    let stateTimer = null;

    function remainingSec(deadline) {
        return Math.max(0, Math.ceil((deadline - Date.now()) / 1000));
    }

    function stateSnapshot() {
        const card = finished ? questions[questions.length - 1] : questions[current];
        if (!card) return null;

        const groups = {};
        Object.keys(groupDeadlines).forEach(function (id) { groups[id] = remainingSec(groupDeadlines[id]); });

        const plays = Array.from(card.querySelectorAll('[data-role="audio"]')).map(function (w) { return w.getPlays(); });

        return {
            mapping_id: parseInt(card.dataset.mappingId, 10),
            step: card.stepIndex || 0,
            remaining: currentDeadline ? remainingSec(currentDeadline) : null,
            groups: groups,
            plays: plays,
            finished: finished,
            saved_at: Date.now(),
        };
    }

    /*
     * The state is also kept in the browser. A request sent while the page unloads can
     * reach the server after the reloaded page has already been served, so on resume
     * the newer of the two copies wins.
     */
    const LOCAL_KEY = 'det_state_' + CFG.attemptId;

    function storeLocally(state) {
        try { localStorage.setItem(LOCAL_KEY, JSON.stringify(state)); } catch (e) { /* Storage may be disabled. */ }
    }

    function newestState() {
        let local = null;
        try { local = JSON.parse(localStorage.getItem(LOCAL_KEY) || 'null'); } catch (e) { local = null; }
        const server = CFG.state;
        if (!local) return server;
        if (!server) return local;
        return (local.saved_at || 0) > (server.saved_at || 0) ? local : server;
    }

    function saveState() {
        const state = stateSnapshot();
        if (!state) return;
        storeLocally(state);
        fetch(CFG.stateUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CFG.csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ state: state }),
            credentials: 'same-origin',
            keepalive: true,
        }).catch(function () { /* The next checkpoint will retry. */ });
    }

    /** Leaving the page: flush typed answers and the position with requests that outlive the page. */
    function saveOnExit() {
        if (current < 0) return;
        questions.forEach(function (card) { if (debounces.has(card)) saveNow(card); });

        const state = stateSnapshot();
        if (state) storeLocally(state);
        if (state && navigator.sendBeacon) {
            const body = new FormData();
            body.append('_token', CFG.csrf);
            body.append('state', JSON.stringify(state));
            navigator.sendBeacon(CFG.stateUrl, body);
        }
    }

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'hidden') saveOnExit();
    });
    window.addEventListener('pagehide', saveOnExit);

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
        currentDeadline = null;
        timerEl.textContent = '--:--';
        timerEl.classList.remove('det-timer-low');
    }

    function runUntil(deadline, onExpire) {
        clearInterval(timerHandle);
        currentDeadline = deadline;

        const tick = function () {
            const left = remainingSec(deadline);
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
                remaining.textContent = 'Plays left: ' + Math.max(0, left);
            }
        }

        wrap.getPlays = function () { return plays; };
        wrap.setPlays = function (n) { plays = Math.max(0, parseInt(n, 10) || 0); refresh(); };

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

    /** Autoplay only clips that have not been played yet, so a resumed question does not use up a play. */
    function startAutoplay(root) {
        root.querySelectorAll('[data-role="audio"][data-autoplay]').forEach(function (wrap) {
            if (wrap.offsetParent !== null && wrap.getPlays() === 0) wrap.playClip();
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
        const card     = wrap.closest('.det-question');
        const turnEl   = wrap.closest('[data-role="turn"]');
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
                status.textContent = 'Saving your recording…';
                activeRecorder = null;

                const turn = turnEl ? stepsOf(card).indexOf(turnEl) + 1 : null;
                saveRecording(card, blob, turn).then(function () {
                    status.textContent = 'Recording saved. Record again to replace it.';
                });
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
        const empty   = preview.dataset.empty;

        // Restore a saved highlight, re-finding the text if the offsets no longer match.
        if (preview.dataset.saved) {
            try {
                const saved = JSON.parse(preview.dataset.saved);
                const text  = passage.textContent;
                if (text.slice(saved.start, saved.end) !== saved.text) {
                    saved.start = text.indexOf(saved.text);
                    saved.end   = saved.start + saved.text.length;
                }
                if (saved.start >= 0) {
                    card.highlight = saved;
                    renderHighlight(passage, saved);
                }
            } catch (e) { /* Ignore an unreadable saved value. */ }
        }

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
            saveNow(card);
        }

        passage.addEventListener('mouseup', capture);
        passage.addEventListener('touchend', capture);
        clear.addEventListener('click', function () {
            card.highlight = null;
            preview.textContent = empty;
            renderHighlight(passage, null);
            saveNow(card);
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

    /** @param {?number} remaining seconds left when resuming, otherwise the step's full time */
    function enterStep(card, index, remaining) {
        const steps = stepsOf(card);
        index = Math.min(index, steps.length - 1);
        card.stepIndex = index;

        steps.forEach(function (step, i) {
            step.style.display = i === index ? '' : 'none';
            stopAudio(step);
        });

        // A finished writing stage stays visible for reference but cannot be edited.
        if (card.dataset.responseType === 'interactive_writing' && index > 0) {
            saveNow(card);
            for (let i = 0; i < index; i++) {
                steps[i].style.display = '';
                steps[i].querySelector('textarea').readOnly = true;
            }
        }

        const step = steps[index];
        nextLabel.textContent = index < steps.length - 1 ? 'Continue' : 'Next';

        const seconds = remaining !== undefined && remaining !== null ? remaining : (parseInt(step.dataset.time, 10) || 0);
        if (remaining === 0) {
            setTimeout(advance, 0);
            return;
        }
        runFor(seconds, advance);
        startAutoplay(step);
        saveState();
    }

    /* ── Navigation ────────────────────────────────────────────────── */

    function leave(card) {
        stopAudio(card);
        // A recording in progress is saved by the recorder's stop handler.
        stopRecording();
        saveNow(card);
    }

    function showFinish() {
        current = questions.length;
        finished = true;
        clearTimer();
        releaseMicrophone();
        clearInterval(stateTimer);
        questions.forEach(function (q) { q.style.display = 'none'; });
        overview.style.display = 'none';
        nav.style.display = 'none';
        progress.style.display = 'none';
        pill.textContent = 'Finished';
        refreshAnsweredCount();
        finish.style.display = '';
        saveState();
    }

    /**
     * @param {number} index
     * @param {{step?: number, remaining?: ?number, plays?: number[]}} [resume]
     */
    function show(index, resume) {
        const card = questions[index];
        current = index;
        resume = resume || {};

        questions.forEach(function (q, i) { q.style.display = i === index ? '' : 'none'; });
        pill.textContent = card.dataset.section || '';
        posEl.textContent = index + 1;
        nextLabel.textContent = 'Next';
        window.scrollTo(0, 0);
        preloadAudio(questions[index + 1]);

        (resume.plays || []).forEach(function (n, i) {
            const wrap = card.querySelectorAll('[data-role="audio"]')[i];
            if (wrap) wrap.setPlays(n);
        });

        const steps = stepsOf(card);
        if (steps.length) {
            enterStep(card, resume.step || 0, resume.remaining);
            return;
        }

        if (usesGroupTimer(card)) {
            const id = card.dataset.groupId;
            if (groupDeadlines[id] === undefined) {
                groupDeadlines[id] = Date.now() + parseInt(card.dataset.groupLimit, 10) * 1000;
            }
            runUntil(groupDeadlines[id], advance);
        } else if (resume.remaining === 0 && parseInt(card.dataset.answerTime, 10) > 0) {
            setTimeout(advance, 0); // Its time ran out before the student left.
            return;
        } else {
            const own = parseInt(card.dataset.answerTime, 10) || 0;
            runFor(resume.remaining !== undefined && resume.remaining !== null ? resume.remaining : own, advance);
        }

        startAutoplay(card);
        saveState();
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
            showFinish();
        }
    }

    function begin() {
        overview.style.display = 'none';
        nav.style.display = '';
        progress.style.display = '';
        stateTimer = setInterval(saveState, STATE_SAVE_EVERY_MS);

        const state = newestState();
        const index = state ? questions.findIndex(function (q) { return q.dataset.mappingId === String(state.mapping_id); }) : -1;

        if (index < 0) {
            show(0);
            return;
        }

        Object.keys(state.groups || {}).forEach(function (id) {
            groupDeadlines[id] = Date.now() + state.groups[id] * 1000;
        });

        if (state.finished) {
            showFinish();
            return;
        }

        show(index, { step: state.step, remaining: state.remaining, plays: state.plays });
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-det-action]');
        if (!btn) return;

        if (btn.dataset.detAction === 'begin') {
            begin();
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

    // What is already on the page counts as saved, so untouched questions send nothing.
    questions.forEach(function (card) {
        const payload = payloadOf(card);
        if (payload) lastSent.set(card, JSON.stringify(payload));
    });

    // Choices save at once; Yes/No word tasks then move on by themselves.
    questions.forEach(function (card) {
        card.querySelectorAll('[data-role="option"]').forEach(function (radio) {
            radio.addEventListener('change', function () { saveNow(card); });
        });
        card.querySelectorAll('[data-role="word-choice"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                saveNow(card);
                setTimeout(function () {
                    if (questions[current] === card) advance();
                }, AUTO_ADVANCE_DELAY_MS);
            });
        });
        card.querySelectorAll('[data-role="blank"]').forEach(function (input) {
            input.addEventListener('input', function () { saveSoon(card, BLANK_SAVE_DELAY_MS); });
            input.addEventListener('blur', function () { saveNow(card); });
        });
        card.querySelectorAll('[data-role="text-input"], [data-role="stage-text"]').forEach(function (textarea) {
            textarea.addEventListener('input', function () { saveSoon(card, TEXT_SAVE_DELAY_MS); });
            textarea.addEventListener('blur', function () { saveNow(card); });
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

    // A position saved only in this browser still means the student is resuming.
    const resumeState = newestState();
    const beginBtn    = document.querySelector('[data-det-action="begin"]');
    if (resumeState && resumeState.mapping_id && beginBtn && !CFG.state) {
        beginBtn.firstChild.textContent = 'Resume test ';
    }

    // The local copy is no longer needed once the test is submitted.
    const submitForm = document.getElementById('detSubmitForm');
    if (submitForm) {
        submitForm.addEventListener('submit', function () {
            try { localStorage.removeItem(LOCAL_KEY); } catch (e) { /* ignore */ }
        });
    }

    refreshAnsweredCount();
})();
