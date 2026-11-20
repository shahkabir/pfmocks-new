/**
 * PTE Exam player.
 *
 * Expects a global `PteExamConfig` object (set inline by the blade) with:
 *   { saveUrl, introUrl, csrf, startedAtIso, introSaved, soundCheckUrl }
 *
 * Organized by concern:
 *   - Core:        save helper, elapsed timer, navigation, per-question countdown
 *   - Widgets:     one init function per response_type (initFillBlank, initMultiChoice, …)
 *   - Pre-screens: overview / headset+mic check / self-introduction
 */
(function () {
    'use strict';

    const cfg       = window.PteExamConfig || {};
    const saveUrl   = cfg.saveUrl;
    const csrf      = cfg.csrf;
    const startedAt = new Date(String(cfg.startedAtIso || '').replace(/Z$/, 'Z')).getTime();

    // Tracks the most recently issued save request so "Submit Exam" can wait
    // for it to finish before navigating away (fetch requests are aborted
    // by the browser the instant the page starts unloading).
    let pendingSave = Promise.resolve();

    /* ======================================================================
     * CORE — elapsed timer
     * ==================================================================== */
    function initElapsedTimer() {
        const $timer = $('#timer');
        function tick() {
            const sec = Math.max(0, Math.floor((Date.now() - startedAt) / 1000));
            const h = String(Math.floor(sec / 3600)).padStart(2, '0');
            const m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
            const s = String(sec % 60).padStart(2, '0');
            $timer.text(`${h}:${m}:${s}`);
        }
        setInterval(tick, 1000);
        tick();
    }

    /* ======================================================================
     * CORE — answer save helper (shared by every widget)
     * ==================================================================== */
    function setStatus($card, state, text) {
        $card.find('[data-role=status]').removeClass('saving saved error').addClass(state).text(text);
    }

    /**
     * Appends a value to FormData, handling arrays (indexed) and plain
     * objects (keyed, e.g. fill_blank's { "1": "answer", "2": "answer" })
     * using PHP/Laravel's bracket-notation convention. FormData.append()
     * silently stringifies any other object to "[object Object]", which is
     * what caused fill-in-the-blank answers to fail server-side validation
     * and never save.
     */
    function appendFormValue(fd, key, value) {
        if (Array.isArray(value)) {
            value.forEach(v => fd.append(`${key}[]`, v));
        } else if (value !== null && typeof value === 'object') {
            Object.entries(value).forEach(([k, v]) => fd.append(`${key}[${k}]`, v));
        } else if (value !== null && value !== undefined) {
            fd.append(key, value);
        }
    }

    function postAnswer($card, payload, fileBlob) {
        setStatus($card, 'saving', 'saving…');
        const fd = new FormData();
        fd.append('_token', csrf);
        fd.append('pte_module_wise_question_id', $card.data('mappingId'));
        Object.entries(payload).forEach(([k, v]) => appendFormValue(fd, k, v));
        if (fileBlob) fd.append('audio_blob', fileBlob, 'recording.webm');
        const promise = fetch(saveUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(r => r.json())
            .then(j => { setStatus($card, j.ok ? 'saved' : 'error', j.ok ? 'saved' : 'error'); return j; })
            .catch(() => setStatus($card, 'error', 'error'));
        pendingSave = promise;
        return promise;
    }

    /* ======================================================================
     * WIDGETS — one init function per response_type
     * ==================================================================== */

    /** text_write, and the WFD-style fill_blank fallback (single free-text) */
    function initTextWrite($c) {
        const $ta = $c.find('[data-role=text-input]');
        if (!$ta.length) return;
        const $wc = $c.find('[data-role=word-count]');
        const recount = () => $wc.text(($ta.val().trim().match(/\S+/g) || []).length);
        $ta.on('input', recount).on('blur', () => postAnswer($c, { response_text: $ta.val() }));
        recount();
    }

    /**
     * response_type: single_choice / multi_choice
     * The clickable target is the whole `.pte-option-row` label (not just the
     * tiny radio/checkbox) — clicking anywhere on the row toggles its input
     * natively via the <label>/<input> association. We just keep the
     * `.is-selected` highlight in sync with the underlying input state.
     */
    function initSingleChoice($c) {
        $c.on('change', '[data-role=single-opt]', function () {
            $c.find('[data-role=single-opt]').closest('.pte-option-row').removeClass('is-selected');
            $(this).closest('.pte-option-row').toggleClass('is-selected', this.checked);
            postAnswer($c, { selected_option_ids: [parseInt(this.value, 10)] });
        });
    }

    function initMultiChoice($c) {
        $c.on('change', '[data-role=multi-opt]', function () {
            $(this).closest('.pte-option-row').toggleClass('is-selected', this.checked);
            const ids = $c.find('[data-role=multi-opt]:checked').map((_, el) => parseInt(el.value, 10)).get();
            postAnswer($c, { selected_option_ids: ids });
        });
    }

    /** response_type: select_option (e.g. Select Missing Word) */
    function initSelectOption($c) {
        $c.on('change', '[data-role=select-opt]', function () {
            postAnswer($c, { selected_option_ids: this.value ? [parseInt(this.value, 10)] : [] });
        });
    }

    /** response_type: fill_blank (per-blank inputs/dropdowns) */
    function initFillBlank($c) {
        if (!$c.find('[data-role=blank]').length) return;
        const collect = () => {
            const out = {};
            $c.find('[data-role=blank]').each(function () { out[$(this).data('order')] = $(this).val(); });
            return out;
        };
        $c.on('change blur', '[data-role=blank]', () => postAnswer($c, { response_blanks: collect() }));
    }

    /** response_type: highlight_words — click a word to toggle the yellow mark */
    function initHighlightWords($c) {
        $c.on('click', '[data-role=word-bag] .pte-hi-word', function () {
            $(this).toggleClass('marked');
            const ids = $c.find('[data-role=word-bag] .pte-hi-word.marked')
                          .map((_, el) => parseInt($(el).data('word-id'), 10)).get();
            postAnswer($c, { response_highlight_word_ids: ids });
        });
    }

    /** response_type: reorder — HTML5 drag & drop of segments */
    function initReorder($c) {
        const $list = $c.find('[data-role=reorder-list]');
        if (!$list.length) return;
        let dragEl = null;
        $list.on('dragstart', '.pte-reorder-item', function () { dragEl = this; $(this).addClass('dragging'); });
        $list.on('dragend',   '.pte-reorder-item', function () { $(this).removeClass('dragging'); dragEl = null; });
        $list.on('dragover',  '.pte-reorder-item', function (e) {
            e.preventDefault();
            if (!dragEl || dragEl === this) return;
            const rect = this.getBoundingClientRect();
            const after = e.originalEvent.clientY > rect.top + rect.height / 2;
            this.parentNode.insertBefore(dragEl, after ? this.nextSibling : this);
        });
        $list.on('drop', () => {
            const order = $list.find('.pte-reorder-item').map((_, el) => parseInt($(el).data('segmentId'), 10)).get();
            postAnswer($c, { response_segments_order: order });
        });
    }

    /** response_type: audio_record — manual or timer-driven mic capture */
    function initAudioRecord($c) {
        const $btn   = $c.find('[data-role=rec-toggle]');
        if (!$btn.length) return;
        const $label = $c.find('[data-role=rec-label]');
        const $rtime = $c.find('[data-role=rec-timer]');
        const $play  = $c.find('[data-role=rec-playback]');
        let recorder = null, chunks = [], tick = null, seconds = 0;

        async function startRec() {
            if (recorder) return;
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                recorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });
                chunks = []; seconds = 0; $rtime.text('00:00');
                recorder.ondataavailable = e => { if (e.data.size) chunks.push(e.data); };
                recorder.onstop = () => {
                    const blob = new Blob(chunks, { type: 'audio/webm' });
                    $play.attr('src', URL.createObjectURL(blob));
                    $c.find('[data-role=rec-play],[data-role=rec-play-hint]').show();
                    postAnswer($c, {}, blob);
                    stream.getTracks().forEach(t => t.stop());
                    recorder = null;
                    clearInterval(tick);
                    $label.text('Re-record');
                    $btn.removeClass('btn-danger').addClass('btn-outline-danger');
                };
                recorder.start();
                $label.text('Stop');
                $btn.removeClass('btn-outline-danger').addClass('btn-danger');
                tick = setInterval(() => {
                    seconds++;
                    $rtime.text(String(Math.floor(seconds / 60)).padStart(2, '0') + ':' + String(seconds % 60).padStart(2, '0'));
                }, 1000);
            } catch (err) {
                alert('Microphone access denied or unavailable.');
            }
        }
        function stopRec() { if (recorder) recorder.stop(); }

        $btn.on('click', () => { recorder ? stopRec() : startRec(); });

        // Timed speaking questions run the mic automatically — hide the manual button
        const timed = (parseInt($c.data('anst'), 10) || 0) > 0;
        if (timed) $btn.closest('.mb-2').hide();

        $c.data('recApi', { start: startRec, stop: stopRec, active: () => !!recorder });
    }

    /**
     * Modern, minimal stimulus-audio player: a play/pause button + a volume
     * slider only (no scrubber, no download control). Orthogonal to
     * response_type — a card can have stimulus audio regardless of how the
     * student answers — so this runs for every card that has one, not via
     * the WIDGET_INITIALIZERS dispatch table.
     *
     * Exposes `$c.data('stimAudioEl')` so the question-timer engine can grab
     * the raw <audio> element and drive playback programmatically (auto-play
     * after preparation time, detect when it finishes).
     */
    function initStimulusAudioPlayer($c) {
        const audio = $c.find('[data-role=stimulus-audio]')[0];
        if (!audio) return;
        const $btn      = $c.find('[data-role=stimulus-audio-play]');
        const $vol      = $c.find('[data-role=stimulus-audio-volume]');
        const $fill     = $c.find('[data-role=stimulus-audio-progress]');
        const $time     = $c.find('[data-role=stimulus-audio-time]');

        function setIcon(playing) {
            $btn.toggleClass('is-playing', playing)
                .find('i').attr('class', playing ? 'bi bi-pause-fill' : 'bi bi-play-fill');
        }
        function formatTime(sec) {
            if (!isFinite(sec) || sec < 0) sec = 0;
            const m = Math.floor(sec / 60);
            const s = Math.floor(sec % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        }
        function updateProgress() {
            const dur = audio.duration || 0;
            const pct = dur > 0 ? Math.min(100, (audio.currentTime / dur) * 100) : 0;
            $fill.css('width', pct + '%');
            $time.text(`${formatTime(audio.currentTime)} / ${formatTime(dur)}`);
        }

        audio.addEventListener('play',  () => setIcon(true));
        audio.addEventListener('pause', () => setIcon(false));
        audio.addEventListener('ended', () => { setIcon(false); $btn.removeClass('pte-audio-pulse'); updateProgress(); });
        audio.addEventListener('loadedmetadata', updateProgress);
        audio.addEventListener('timeupdate', updateProgress);
        updateProgress();

        $btn.on('click', () => {
            $btn.removeClass('pte-audio-pulse');
            if (audio.paused) {
                // Replaying after the track already ended must restart from 0 —
                // otherwise the browser fires 'ended' again instantly with no
                // audible playback.
                if (audio.ended || audio.currentTime >= (audio.duration || Infinity)) {
                    audio.currentTime = 0;
                }
                audio.play().catch(() => {});
            } else {
                audio.pause();
            }
        });

        $vol.on('input', function () { audio.volume = parseFloat(this.value); });
        audio.volume = parseFloat($vol.val() || '1');

        $c.data('stimAudioEl', audio);
    }

    /** Dispatch table: response_type -> init function */
    const WIDGET_INITIALIZERS = {
        text_write:       initTextWrite,
        single_choice:    initSingleChoice,
        multi_choice:     initMultiChoice,
        select_option:    initSelectOption,
        fill_blank:       initFillBlank,
        highlight_words:  initHighlightWords,
        reorder:          initReorder,
        audio_record:     initAudioRecord,
    };

    function initQuestionWidgets() {
        // Scoped to #qContainer — pre-screens reuse .pte-question-card for styling only.
        $('#qContainer .pte-question-card').each(function () {
            const $c   = $(this);
            const resp = $c.data('resp');
            const init = WIDGET_INITIALIZERS[resp];
            if (init) init($c);
            // WFD-style fill_blank with no blank rows behaves like free text
            if (resp === 'fill_blank' && !$c.find('[data-role=blank]').length) initTextWrite($c);

            initStimulusAudioPlayer($c);
        });
    }

    /* ======================================================================
     * CORE — circled play/pause for recorded audio (answers + intro)
     * ==================================================================== */
    function initPlayButtons() {
        $(document).on('click', '.pte-play-btn', function () {
            const $btn  = $(this);
            const audio = $btn.closest('div').find('audio')[0];
            if (!audio || !audio.src) return;

            const setPlaying = on => {
                $btn.toggleClass('playing', on)
                    .find('i').attr('class', on ? 'bi bi-stop-fill' : 'bi bi-play-fill');
            };
            audio.onplay  = () => setPlaying(true);
            audio.onpause = audio.onended = () => setPlaying(false);

            if (audio.paused) audio.play();
            else { audio.pause(); audio.currentTime = 0; }
        });
    }

    /* ======================================================================
     * CORE — one-question-at-a-time navigation + prep/answer countdown
     * ==================================================================== */
    function initQuestionNavigation() {
        const $cards  = $('#qContainer .pte-question-card');
        const total   = $cards.length;
        $('#qTotal').text(total);

        const $prev    = $('#prevBtn');
        const $next    = $('#nextBtn');
        const $submit  = $('#submitForm');
        const $pos     = $('#qPos');
        const $secPill = $('#sectionPill');
        const $secLbl  = $('#sectionLabel');

        let current = 0;
        let qTick = null;
        let $activeCard = null;

        function clearQuestionTimer() {
            if (qTick) { clearInterval(qTick); qTick = null; }
        }
        function stopActiveRecording($card) {
            const api = $card && $card.data('recApi');
            if (api && api.active()) api.stop(); // onstop uploads the answer
        }
        function pauseStimulusAudio($card) {
            const audio = $card && $card.data('stimAudioEl');
            if (audio && !audio.paused) audio.pause();
        }

        /**
         * Preparation → (stimulus audio, whenever the question actually has one)
         * → Answer.
         *
         * Gating is based on whether a stimulus <audio> element exists on the
         * card at all — not on the exact stimulus_type string — so it covers
         * both 'audio' (Repeat Sentence, Answer Short Question, …) and
         * 'audio_image' (Re-tell Lecture) alike. The audio auto-plays the
         * instant preparation time ends, and the answer/recording phase (and
         * its countdown / mic) only begins once that audio finishes —
         * matching real PTE behaviour, where you can't respond before you've
         * heard the prompt. Questions with no stimulus audio at all keep the
         * previous prep → answer flow unchanged.
         */
        function runQuestionTimers($card) {
            clearQuestionTimer();
            const prep      = parseInt($card.data('prep'), 10) || 0;
            const ans       = parseInt($card.data('anst'), 10) || 0;
            const isAudio   = $card.data('resp') === 'audio_record';
            const stimAudio = $card.data('stimAudioEl') || null;

            const $wrap = $card.find('[data-role=qtimer]');
            const $lbl  = $card.find('[data-role=qtimer-label]');
            const $secs = $card.find('[data-role=qtimer-secs]');
            const $playBtn = $card.find('[data-role=stimulus-audio-play]');

            $card.removeClass('phase-prep phase-audio phase-answer');
            // The manual play button is only a fallback for blocked autoplay —
            // keep it inert until the auto-play phase actually begins, so an
            // early/curious click during Preparation can't leave the element
            // sitting at `ended` (which would otherwise make the real
            // auto-play call fire 'ended' instantly with no audible playback).
            if (stimAudio) $playBtn.prop('disabled', true);
            if (!prep && !ans && !stimAudio) { $wrap.hide(); return; }
            $wrap.show();

            function phaseAnswer() {
                if (!ans) { $wrap.hide(); return; }
                $card.removeClass('phase-prep phase-audio').addClass('phase-answer');
                if (isAudio) {
                    const api = $card.data('recApi');
                    if (api) api.start();
                    $lbl.text('Recording');
                } else {
                    $lbl.text('Time Left');
                }
                let left = ans;
                $secs.text(left);
                qTick = setInterval(() => {
                    left--;
                    $secs.text(Math.max(0, left));
                    if (left <= 0) {
                        clearQuestionTimer();
                        if (isAudio) stopActiveRecording($card);
                        setTimeout(() => {
                            if (current < total - 1) { current++; showCurrent(); }
                        }, isAudio ? 800 : 300);
                    }
                }, 1000);
            }

            /** Auto-play the stimulus audio, show a live countdown of its remaining time,
             *  then hand off to phaseAnswer() once it finishes playing. */
            function phaseStimulusAudio() {
                if (!stimAudio) { phaseAnswer(); return; }

                $card.addClass('phase-audio');
                $lbl.text('Listen Carefully');
                $secs.text('');
                $playBtn.prop('disabled', false);

                // Guard against replaying a track that already reached the end
                // (e.g. a stray manual click) — without this, .play() on an
                // already-`ended` element fires 'ended' again instantly with
                // no audible playback, which looks exactly like "audio never
                // played but the answer phase started anyway".
                if (stimAudio.ended || stimAudio.currentTime >= (stimAudio.duration || Infinity)) {
                    stimAudio.currentTime = 0;
                }

                function tickRemaining() {
                    const dur = stimAudio.duration;
                    if (!isFinite(dur) || dur <= 0) return;
                    $secs.text(Math.max(0, Math.ceil(dur - stimAudio.currentTime)));
                }

                function onEnded() {
                    clearQuestionTimer();
                    stimAudio.removeEventListener('ended', onEnded);
                    $playBtn.prop('disabled', true);
                    phaseAnswer();
                }
                stimAudio.addEventListener('ended', onEnded);
                qTick = setInterval(tickRemaining, 250);

                const playPromise = stimAudio.play();
                if (playPromise && playPromise.catch) {
                    playPromise.catch(() => {
                        // Autoplay blocked by the browser — prompt the student to press
                        // play manually; the same 'ended' listener still fires afterwards.
                        $lbl.text('Press play to continue');
                        $playBtn.addClass('pte-audio-pulse');
                    });
                }
            }

            function phasePrep() {
                if (prep <= 0) { phaseStimulusAudio(); return; }
                $card.addClass('phase-prep');
                $lbl.text('Preparation');
                let left = prep;
                $secs.text(left);
                qTick = setInterval(() => {
                    left--;
                    $secs.text(Math.max(0, left));
                    if (left <= 0) { clearQuestionTimer(); phaseStimulusAudio(); }
                }, 1000);
            }

            phasePrep();
        }

        function showCurrent() {
            clearQuestionTimer();
            stopActiveRecording($activeCard);
            pauseStimulusAudio($activeCard);

            $cards.hide();
            if (total === 0) { $next.hide(); return; }
            const $c = $cards.eq(current).show();
            $activeCard = $c;
            $pos.text(current + 1);

            const secName = $c.data('sectionName') || '';
            const secTag  = $c.data('sectionTag')  || '';
            $secPill.text(secTag ? `${secTag} · ${secName}` : secName || '—');
            $secLbl.text(secName || '—');

            $prev.prop('disabled', current === 0);
            if (current === total - 1) { $next.hide(); $submit.show(); }
            else                        { $next.show(); $submit.hide(); }

            runQuestionTimers($c);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        $prev.on('click', () => { if (current > 0)         { current--; showCurrent(); } });
        $next.on('click', () => { if (current < total - 1) { current++; showCurrent(); } });

        /**
         * "Submit Exam" is a real <form> — a native submit navigates the
         * browser away immediately, which aborts any save request still
         * in flight. Here we: (1) force the currently focused field on the
         * active card to blur (firing its save handler), then (2) wait for
         * the resulting request — and any earlier one still pending —
         * before letting the form actually submit.
         */
        $submit.on('submit', function (e) {
            e.preventDefault();
            const formEl = this;

            if ($activeCard && $activeCard.length) {
                const focused = document.activeElement;
                if (focused && $activeCard.get(0).contains(focused)) {
                    $(focused).trigger('blur');
                }
            }

            const $btn = $(formEl).find('button[type=submit]');
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Submitting…');

            Promise.resolve(pendingSave).finally(() => {
                formEl.submit(); // native submit — bypasses this jQuery handler, no loop
            });
        });

        return { show: showCurrent };
    }

    /* ======================================================================
     * PRE-SCREENS — overview, headset+mic check, self-introduction
     * ==================================================================== */
    function initHeadsetCheck() {
        const $play = $('#hsPlayBtn'), $stop = $('#hsStopBtn'), $st = $('#hsStatus');
        const testAudio = new Audio(cfg.soundCheckUrl);
        let osc = null, ctx = null;

        function setStatus(txt, cls) { $st.text(txt).removeClass('bg-secondary bg-success bg-primary').addClass(cls); }
        function stopAll() {
            testAudio.pause(); testAudio.currentTime = 0;
            if (osc) { try { osc.stop(); } catch (e) {} osc = null; }
            if (ctx) { ctx.close().catch(() => {}); ctx = null; }
            setStatus('Stopped', 'bg-secondary');
            $play.prop('disabled', false); $stop.prop('disabled', true);
        }
        function playBeep() {
            ctx = new (window.AudioContext || window.webkitAudioContext)();
            osc = ctx.createOscillator();
            const gain = ctx.createGain();
            gain.gain.value = 0.25;
            osc.frequency.value = 440;
            osc.connect(gain).connect(ctx.destination);
            osc.start();
            setTimeout(stopAll, 3000);
        }
        $play.on('click', () => {
            setStatus('Playing', 'bg-success');
            $play.prop('disabled', true); $stop.prop('disabled', false);
            testAudio.play().catch(playBeep);
        });
        testAudio.addEventListener('ended', stopAll);
        $stop.on('click', stopAll);
    }

    function initMicCheck() {
        const $rec = $('#mcRecBtn'), $stop = $('#mcStopBtn'), $play = $('#mcPlayBtn');
        const $st = $('#mcStatus'), $tm = $('#mcTimer'), player = document.getElementById('mcPlayback');
        let recorder = null, chunks = [], tick = null, sec = 0;

        function setStatus(txt, cls) { $st.text(txt).removeClass('bg-secondary bg-danger bg-success').addClass(cls); }

        $rec.on('click', async () => {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                recorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });
                chunks = []; sec = 0; $tm.text('00:00');
                recorder.ondataavailable = e => { if (e.data.size) chunks.push(e.data); };
                recorder.onstop = () => {
                    player.src = URL.createObjectURL(new Blob(chunks, { type: 'audio/webm' }));
                    stream.getTracks().forEach(t => t.stop());
                    clearInterval(tick);
                    setStatus('Recorded', 'bg-success');
                    $rec.prop('disabled', false); $stop.prop('disabled', true); $play.prop('disabled', false);
                    recorder = null;
                };
                recorder.start();
                setStatus('Recording', 'bg-danger');
                $rec.prop('disabled', true); $stop.prop('disabled', false); $play.prop('disabled', true);
                tick = setInterval(() => {
                    sec++;
                    $tm.text(String(Math.floor(sec / 60)).padStart(2, '0') + ':' + String(sec % 60).padStart(2, '0'));
                }, 1000);
            } catch (e) { alert('Microphone access denied or unavailable.'); }
        });
        $stop.on('click', () => { if (recorder) recorder.stop(); });
        $play.on('click', () => { player.play(); });
    }

    function initIntroRecorder(onSaved) {
        const INTRO_SECONDS = 25;
        const $rec = $('#inRecBtn'), $st = $('#inStatus'), $cd = $('#inCountdown');
        const player = document.getElementById('inPlayback');
        let recorder = null, chunks = [], tick = null, left = INTRO_SECONDS;

        function setStatus(txt, cls) { $st.text(txt).removeClass('bg-secondary bg-danger bg-warning bg-success text-dark').addClass(cls); }

        function uploadIntro(blob) {
            setStatus('Uploading…', 'bg-warning text-dark');
            const fd = new FormData();
            fd.append('_token', csrf);
            fd.append('audio_blob', blob, 'intro.webm');
            fetch(cfg.introUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
                .then(r => r.json())
                .then(j => {
                    if (j.ok) { setStatus('Saved', 'bg-success'); onSaved(); }
                    else setStatus('Upload failed', 'bg-danger');
                })
                .catch(() => setStatus('Upload failed', 'bg-danger'));
        }

        $rec.on('click', async () => {
            if (recorder) { recorder.stop(); return; }
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                recorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });
                chunks = []; left = INTRO_SECONDS; $cd.text(left + 's');
                recorder.ondataavailable = e => { if (e.data.size) chunks.push(e.data); };
                recorder.onstop = () => {
                    const blob = new Blob(chunks, { type: 'audio/webm' });
                    player.src = URL.createObjectURL(blob);
                    $('#inPlayBtn, #inPlayHint').show();
                    stream.getTracks().forEach(t => t.stop());
                    clearInterval(tick);
                    $rec.html('<i class="bi bi-arrow-repeat me-1"></i>Re-record').removeClass('btn-danger').addClass('btn-outline-danger');
                    recorder = null;
                    uploadIntro(blob);
                };
                recorder.start();
                setStatus('Recording', 'bg-danger');
                $rec.html('<i class="bi bi-stop-fill me-1"></i>Stop');
                tick = setInterval(() => {
                    left--;
                    $cd.text(Math.max(0, left) + 's');
                    if (left <= 0 && recorder) recorder.stop();
                }, 1000);
            } catch (e) { alert('Microphone access denied or unavailable.'); }
        });
    }

    function initPreScreens(onStartExam) {
        const preSteps   = $('.pte-pre-screen');
        const preLabels  = ['Test Overview', 'Headset & Microphone Check', 'Introduction to Yourself'];
        const $preFooter = $('#preFooter');
        const $preBack   = $('#preBackBtn');
        const $preNext   = $('#preNextBtn');
        const $secPill   = $('#sectionPill');
        let introSaved   = !!cfg.introSaved;
        let preStep = 0;

        function showPreScreen(n) {
            preStep = n;
            preSteps.hide();
            preSteps.filter(`[data-pre-step=${n}]`).show();
            $('#preStepLabel').text(`Step ${n + 1} of ${preSteps.length} · ${preLabels[n]}`);
            $secPill.text(preLabels[n]);
            $preBack.prop('disabled', n === 0);
            if (n === preSteps.length - 1) {
                $preNext.html('Start Exam <i class="bi bi-play-fill ms-1"></i>').prop('disabled', !introSaved);
            } else {
                $preNext.html('Next <i class="bi bi-arrow-right ms-1"></i>').prop('disabled', false);
            }
            $preFooter.show();
            $('#examFooter, #qHeaderRow').hide();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function startExam() {
            preSteps.hide();
            $preFooter.hide();
            $('#examLoader').css('display', 'flex');
            setTimeout(() => {
                $('#examLoader').hide();
                $('#qHeaderRow').show();
                $('#examFooter').css('display', 'flex');
                onStartExam();
            }, 2200);
        }

        $preBack.on('click', () => { if (preStep > 0) showPreScreen(preStep - 1); });
        $preNext.on('click', () => {
            if (preStep < preSteps.length - 1) showPreScreen(preStep + 1);
            else startExam();
        });

        initHeadsetCheck();
        initMicCheck();
        initIntroRecorder(() => {
            introSaved = true;
            if (preStep === preSteps.length - 1) $preNext.prop('disabled', false);
        });

        if (introSaved) startExam();
        else            showPreScreen(0);
    }

    /* ======================================================================
     * Entry point
     * ==================================================================== */
    $(function () {
        initElapsedTimer();
        initPlayButtons();
        initQuestionWidgets();
        const nav = initQuestionNavigation();
        initPreScreens(() => nav.show());
    });
})();
