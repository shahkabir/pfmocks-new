@include('layouts.header')

@section('title', 'PTE Exam — ' . $module->name)

<style>
    body { background:#f4f6f9; margin:0; }
</style>
<style>
    .pte-bar {
        position: sticky; top: 0; z-index: 1020;
        background:#0d6efd; color:#fff;
        display:flex; align-items:center; padding:10px 18px;
        box-shadow:0 2px 6px rgba(0,0,0,.1);
        width:100%;
    }
    .pte-bar .left  { width:30%; }
    .pte-bar .mid   { width:40%; text-align:center; font-weight:700; font-size:1.1rem; letter-spacing:1px; font-variant-numeric:tabular-nums; }
    .pte-bar .right { width:30%; text-align:right; }

    .pte-section-pill   { background:rgba(255,255,255,.18); padding:4px 10px; border-radius:14px; font-size:.78rem; letter-spacing:.4px; }
    .pte-question-card  { border:1px solid #dee2e6; border-radius:10px; padding:24px; margin-top:18px; background:#fff; }
    .pte-question-meta  { font-size:.82rem; color:#6c757d; }
    .pte-stimulus-img   { max-width:100%; max-height:320px; border:1px solid #e9ecef; border-radius:6px; }
    .pte-reorder-item   { cursor:grab; }
    .pte-reorder-item.dragging { opacity:.5; }
    .pte-hi-word        { padding:2px 4px; cursor:pointer; border-radius:3px; user-select:none; }
    .pte-hi-word.marked { background:#ffe69c; }
    .pte-save-pill      { font-size:.72rem; padding:.18rem .5rem; border-radius:10px; }
    .pte-save-pill.saving { background:#fff3cd; color:#856404; }
    .pte-save-pill.saved  { background:#d1e7dd; color:#0a3622; }
    .pte-save-pill.error  { background:#f8d7da; color:#842029; }
    .pte-rec-btn        { min-width:140px; }

    /* ── Per-question rounded countdown ── */
    .pte-qtimer-wrap { display:flex; justify-content:center; margin-top:24px; }
    .pte-qtimer {
        width:120px; height:120px; border-radius:50%;
        border:6px solid #0d6efd; background:#fff;
        display:flex; flex-direction:column; align-items:center; justify-content:center;
        box-shadow:0 2px 10px rgba(0,0,0,.08);
        transition:border-color .3s;
    }
    .phase-prep   .pte-qtimer { border-color:#ffc107; }
    .phase-answer .pte-qtimer { border-color:#dc3545; }
    .pte-qtimer-secs  { font-size:2rem; font-weight:700; line-height:1.1; font-variant-numeric:tabular-nums; }
    .pte-qtimer-label { font-size:.7rem; text-transform:uppercase; letter-spacing:.6px; color:#6c757d; }

    .pte-footer {
        position: sticky; bottom:0; z-index:1015;
        background:#f8f9fa; border-top:1px solid #dee2e6;
        padding:12px 18px; margin-top:20px;
        display:flex; justify-content:space-between; align-items:center;
    }
</style>

{{-- ── Top bar with section / timer / exit ──────────────────────────── --}}
<div class="pte-bar">
    <div class="left">
        <span class="pte-section-pill" id="sectionPill">—</span>
    </div>
    <div class="mid">
        <i class="bi bi-stopwatch me-1"></i>
        <span id="timer">00:00:00</span>
    </div>
    <div class="right">
        <a href="{{ route('dashboard.student') }}"
           class="btn btn-light btn-sm"
           onclick="return confirm('Exit the exam? Your saved answers remain — you can resume later from your dashboard.');">
            <i class="bi bi-box-arrow-right me-1"></i>Exit
        </a>
    </div>
</div>

<div class="container-fluid mt-2">
    <div class="d-flex justify-content-between align-items-center mb-1" id="qHeaderRow" style="display:none;">
        <div class="small text-muted">
            <strong>{{ $module->name }}</strong> · Attempt #{{ $attempt->id }}
        </div>
        <div class="small text-muted">
            Question <span id="qPos">0</span> of <span id="qTotal">0</span>
        </div>
    </div>

    @if($structure->isEmpty())
        <div class="alert alert-warning">No PTE sections are mapped to this module. Ask the admin to assemble the mock test.</div>
    @endif

    {{-- ════════════════ PRE-SCREEN 1 · Exam overview ════════════════ --}}
    <div class="pte-pre-screen" data-pre-step="0" style="display:none;">
        <div class="pte-question-card">
            <h4 class="mb-3"><i class="bi bi-clipboard-data me-2 text-primary"></i>{{ $module->name }} — Test Overview</h4>
            <p class="text-muted">This test contains the following parts. Once you begin, questions are presented one at a time.</p>

            <table class="table table-bordered align-middle" style="max-width:720px;">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px;">Part</th>
                        <th>Section</th>
                        <th class="text-center">Questions</th>
                        <th class="text-center">Time Allowed</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalQ = 0; $totalMin = 0; @endphp
                    @foreach($structure as $i => $pm)
                        @php
                            $cnt = $pm->moduleQuestions->count();
                            $totalQ  += $cnt;
                            $totalMin += (int) ($pm->section?->time_allowed_minutes ?? 0);
                        @endphp
                        <tr>
                            <td class="text-center fw-semibold">{{ $i + 1 }}</td>
                            <td>{{ $pm->section?->name }} <span class="badge bg-info text-dark ms-1">{{ $pm->section?->tag }}</span></td>
                            <td class="text-center">{{ $cnt }}</td>
                            <td class="text-center">{{ $pm->section?->time_allowed_minutes ? $pm->section->time_allowed_minutes . ' min' : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr class="fw-bold">
                        <td colspan="2" class="text-end">Total</td>
                        <td class="text-center">{{ $totalQ }}</td>
                        <td class="text-center">{{ $module->duration_minutes ?? $totalMin }} min</td>
                    </tr>
                </tfoot>
            </table>

            <div class="alert alert-warning small" style="max-width:720px;">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Total duration: <strong>{{ $module->duration_minutes ?? $totalMin }} minutes</strong>.
                Answers save automatically as you move through the test.
            </div>
        </div>
    </div>

    {{-- ════════════════ PRE-SCREEN 2 · Headset & microphone check ════════════════ --}}
    <div class="pte-pre-screen" data-pre-step="1" style="display:none;">
        <div class="pte-question-card">
            <h4 class="mb-3"><i class="bi bi-headphones me-2 text-primary"></i>Headset Check</h4>
            <p>Check that your headset is working correctly.</p>
            <ul>
                <li>Put your headset on and adjust it so that it fits comfortably over your ears.</li>
                <li>When you are ready, click on the <strong>[Play]</strong> button. You will hear a short recording.</li>
                <li>If you do not hear anything in your headphones while the status reads <strong>[Playing]</strong>, raise your hand to get the attention of the Test Administrator.</li>
            </ul>

            <div class="d-flex align-items-center gap-3 my-3">
                <button type="button" id="hsPlayBtn" class="btn btn-primary" style="min-width:110px;">
                    <i class="bi bi-play-fill me-1"></i>Play
                </button>
                <button type="button" id="hsStopBtn" class="btn btn-outline-secondary" disabled style="min-width:110px;">
                    <i class="bi bi-stop-fill me-1"></i>Stop
                </button>
                <span class="badge bg-secondary" id="hsStatus">Idle</span>
            </div>
            <div class="alert alert-light small border">
                <ul class="mb-0">
                    <li>During the test you will not have [Play] and [Stop] buttons. The audio recording will start playing automatically.</li>
                    <li>Please do not remove your headset. You should wear it throughout the test.</li>
                </ul>
            </div>

            <hr class="my-4">

            <h4 class="mb-3"><i class="bi bi-mic me-2 text-primary"></i>Microphone Check</h4>
            <p>This is an opportunity to check that your microphone is working correctly.</p>
            <ul>
                <li>Make sure your headset is on and the microphone is in the downward position near your mouth.</li>
                <li>When you are ready, click on the <strong>Record</strong> button and say <em>"Testing, testing, one, two, three"</em> into the microphone.</li>
                <li>After you have spoken, click on the <strong>Stop</strong> button. Your recording is now complete.</li>
                <li>Now click on the <strong>Playback</strong> button. You should clearly hear yourself speaking.</li>
                <li>If you can not hear your voice clearly, please raise your hand.</li>
            </ul>

            <div class="d-flex align-items-center gap-3 my-3 flex-wrap">
                <button type="button" id="mcRecBtn" class="btn btn-danger" style="min-width:110px;">
                    <i class="bi bi-record-circle me-1"></i>Record
                </button>
                <button type="button" id="mcStopBtn" class="btn btn-outline-secondary" disabled style="min-width:110px;">
                    <i class="bi bi-stop-fill me-1"></i>Stop
                </button>
                <button type="button" id="mcPlayBtn" class="btn btn-outline-primary" disabled style="min-width:110px;">
                    <i class="bi bi-play-fill me-1"></i>Playback
                </button>
                <span class="badge bg-secondary" id="mcStatus">Idle</span>
                <span class="small text-muted" id="mcTimer">00:00</span>
            </div>
            <audio id="mcPlayback" style="display:none;"></audio>
        </div>
    </div>

    {{-- ════════════════ PRE-SCREEN 3 · Introduction to yourself ════════════════ --}}
    <div class="pte-pre-screen" data-pre-step="2" style="display:none;">
        <div class="pte-question-card">
            <h4 class="mb-3"><i class="bi bi-person-video2 me-2 text-primary"></i>Introduction to Yourself</h4>
            <p>
                Please introduce yourself. You have <strong>25 seconds</strong> to record your introduction.
                For example, you could talk about your study or work background, your interests, and why you are taking this test.
            </p>
            <div class="alert alert-light small border" style="max-width:720px;">
                This recording is not scored, but it is sent together with your test.
            </div>

            <div class="d-flex align-items-center gap-3 my-3 flex-wrap">
                <button type="button" id="inRecBtn" class="btn btn-danger" style="min-width:150px;">
                    <i class="bi bi-record-circle me-1"></i>Start Recording
                </button>
                <span class="badge bg-secondary" id="inStatus">Idle</span>
                <span class="fw-bold" id="inCountdown" style="font-variant-numeric:tabular-nums;">25s</span>
            </div>
            <audio id="inPlayback" controls style="display:none;max-width:480px;width:100%;"></audio>
            <div class="small text-muted mt-2" id="inHint">Recording stops automatically after 25 seconds.</div>
        </div>
    </div>

    {{-- ── Pre-screen navigation ── --}}
    <div class="pte-footer" id="preFooter" style="display:none;">
        <button id="preBackBtn" type="button" class="btn btn-outline-secondary" disabled>
            <i class="bi bi-arrow-left me-1"></i>Back
        </button>
        <div class="text-muted small" id="preStepLabel"></div>
        <button id="preNextBtn" type="button" class="btn btn-primary">
            Next <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </div>

    {{-- All question cards are rendered hidden; JS reveals one at a time --}}
    <div id="qContainer">
    @php $globalIndex = 0; @endphp
    @foreach($structure as $pm)
        @php $section = $pm->section; @endphp
        @foreach($pm->moduleQuestions as $idx => $mwq)
            @php
                $globalIndex++;
                $q       = $mwq->question;
                $st      = $q?->subType;
                $resp    = $st?->response_type;
                $stim    = $st?->stimulus_type;
                $saved   = $answers[$mwq->id] ?? null;
                $opts    = $q?->options ?? collect();
                $blanks  = $q?->blanks ?? collect();
                $segs    = $q?->segments ?? collect();
                $words   = $q?->highlightWords ?? collect();
            @endphp

            @php
                $effPrep = (int) ($q?->preparation_time_sec ?? $st?->preparation_time_sec_default ?? 0);
                $effAns  = (int) ($q?->answer_time_sec ?? $st?->answer_time_sec_default ?? 0);
            @endphp
            <div class="pte-question-card"
                 data-q-index="{{ $globalIndex }}"
                 data-mapping-id="{{ $mwq->id }}"
                 data-resp="{{ $resp }}"
                 data-prep="{{ $effPrep }}"
                 data-anst="{{ $effAns }}"
                 data-section-name="{{ $section?->name }}"
                 data-section-tag="{{ $section?->tag }}"
                 style="display:none;">

                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="badge bg-info text-dark">{{ $st?->tag }}</span>
                        <strong class="ms-1">Q{{ $globalIndex }}.</strong>
                        <code class="ms-1 small">{{ $q?->question_granular_id }}</code>
                        <div class="pte-question-meta">{{ $st?->name }} · Marks: {{ $q?->marks }}</div>
                    </div>
                    <span class="pte-save-pill" data-role="status">unsaved</span>
                </div>

                {{-- ── Stimulus (image / audio) ─────────────────────────── --}}
                @if($q?->image_url && in_array($stim, ['image','audio_image']))
                    <div class="mb-3">
                        <img src="{{ asset(ltrim($q->image_url, '/')) }}"
                             alt="{{ $q->image_alt_text }}" class="pte-stimulus-img">
                    </div>
                @endif
                @if($q?->audio_url && in_array($stim, ['audio','audio_image']))
                    <div class="mb-3">
                        <audio controls src="{{ asset(ltrim($q->audio_url, '/')) }}" style="width:100%;"></audio>
                    </div>
                @endif

                {{-- ── Prompt / passage ─────────────────────────────────── --}}
                @if($q?->question_text)
                    <div class="mb-3">{!! nl2br(e($q->question_text)) !!}</div>
                @endif

                {{-- ── Response widget by sub-type response_type ────────── --}}
                @switch($resp)

                    @case('audio_record')
                        <div class="mb-2">
                            <button type="button" class="btn btn-outline-danger pte-rec-btn" data-role="rec-toggle">
                                <i class="bi bi-mic-fill me-1"></i><span data-role="rec-label">Start Recording</span>
                            </button>
                            <span class="ms-2 small text-muted" data-role="rec-timer">00:00</span>
                        </div>
                        @if($saved?->response_audio_url)
                            <audio data-role="rec-playback" controls src="{{ asset(ltrim($saved->response_audio_url, '/')) }}" style="width:100%;"></audio>
                        @else
                            <audio data-role="rec-playback" controls style="display:none;width:100%;"></audio>
                        @endif
                        @break

                    @case('text_write')
                        <textarea data-role="text-input" class="form-control"
                                  rows="6" placeholder="Type your response here…">{{ $saved?->response_text }}</textarea>
                        @if($q?->min_word_count || $q?->max_word_count)
                            <div class="small text-muted mt-1">
                                Word count: <span data-role="word-count">0</span>
                                @if($q->min_word_count) · Min {{ $q->min_word_count }} @endif
                                @if($q->max_word_count) · Max {{ $q->max_word_count }} @endif
                            </div>
                        @endif
                        @break

                    @case('single_choice')
                        @php $picked = $saved?->selected_option_ids[0] ?? null; @endphp
                        @foreach($opts as $opt)
                            <div class="form-check">
                                <input class="form-check-input" type="radio"
                                       name="opt-{{ $mwq->id }}"
                                       data-role="single-opt"
                                       value="{{ $opt->id }}"
                                       {{ (int)$picked === (int)$opt->id ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $opt->option_text }}</label>
                            </div>
                        @endforeach
                        @break

                    @case('multi_choice')
                        @php $picks = $saved?->selected_option_ids ?? []; @endphp
                        @foreach($opts as $opt)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       data-role="multi-opt"
                                       value="{{ $opt->id }}"
                                       {{ in_array((int)$opt->id, array_map('intval',$picks), true) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $opt->option_text }}</label>
                            </div>
                        @endforeach
                        @break

                    @case('select_option')
                        @php $picked = $saved?->selected_option_ids[0] ?? null; @endphp
                        <select data-role="select-opt" class="form-select" style="max-width:320px;">
                            <option value="">— Select —</option>
                            @foreach($opts as $opt)
                                <option value="{{ $opt->id }}" {{ (int)$picked === (int)$opt->id ? 'selected' : '' }}>
                                    {{ $opt->option_text }}
                                </option>
                            @endforeach
                        </select>
                        @break

                    @case('fill_blank')
                        @php $savedBlanks = (array) ($saved?->response_blanks ?? []); @endphp
                        @foreach($blanks as $b)
                            <div class="row mb-2">
                                <div class="col-md-3 fw-semibold text-end">Blank {{ $b->blank_order }}</div>
                                <div class="col-md-9">
                                    @php $val = $savedBlanks[(string) $b->blank_order] ?? ''; @endphp
                                    @if(!empty($b->dropdown_options))
                                        <select data-role="blank" data-order="{{ $b->blank_order }}" class="form-select">
                                            <option value="">— Select —</option>
                                            @foreach($b->dropdown_options as $opt)
                                                <option value="{{ $opt }}" {{ $val === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input data-role="blank" data-order="{{ $b->blank_order }}"
                                               type="text" class="form-control"
                                               value="{{ $val }}" placeholder="Your answer">
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        @if($blanks->isEmpty())
                            <textarea data-role="text-input" class="form-control" rows="3"
                                      placeholder="Type the sentence you heard…">{{ $saved?->response_text }}</textarea>
                        @endif
                        @break

                    @case('reorder')
                        @php
                            $savedOrder = $saved?->response_segments_order;
                            $segMap     = $segs->keyBy('id');
                            $ordered    = $savedOrder
                                ? collect($savedOrder)->map(fn ($id) => $segMap->get($id))->filter()
                                : $segs->shuffle();
                        @endphp
                        <div class="alert alert-light small">Drag to reorder. Top = first sentence.</div>
                        <ul class="list-group" data-role="reorder-list">
                            @foreach($ordered as $seg)
                                <li class="list-group-item pte-reorder-item" draggable="true" data-segment-id="{{ $seg->id }}">
                                    <i class="bi bi-grip-vertical me-2 text-muted"></i>{{ $seg->segment_text }}
                                </li>
                            @endforeach
                        </ul>
                        @break

                    @case('highlight_words')
                        @php $markedIds = array_map('intval', (array) ($saved?->response_highlight_word_ids ?? [])); @endphp
                        <div class="alert alert-light small">Click each word that differs from what the speaker says.</div>
                        <div data-role="word-bag" style="line-height:2;">
                            @foreach($words as $w)
                                <span class="pte-hi-word {{ in_array((int)$w->id, $markedIds, true) ? 'marked' : '' }}"
                                      data-word-id="{{ $w->id }}">{{ $w->word_text }}</span>
                            @endforeach
                        </div>
                        @break

                    @default
                        <em class="text-muted">No response widget mapped for "{{ $resp }}" yet.</em>
                @endswitch

                {{-- Centered rounded countdown (preparation → answer) --}}
                <div class="pte-qtimer-wrap" data-role="qtimer" style="display:none;">
                    <div class="pte-qtimer">
                        <div class="pte-qtimer-secs"  data-role="qtimer-secs">0</div>
                        <div class="pte-qtimer-label" data-role="qtimer-label">Preparation</div>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach
    </div>

    {{-- ── Footer navigation ───────────────────────────────────────────── --}}
    <div class="pte-footer" id="examFooter" style="display:none;"
        <button id="prevBtn" type="button" class="btn btn-outline-secondary" disabled>
            <i class="bi bi-arrow-left me-1"></i>Previous
        </button>

        <div class="text-muted small">
            Section: <strong id="sectionLabel">—</strong>
        </div>

        <div>
            <button id="nextBtn" type="button" class="btn btn-primary">
                Next <i class="bi bi-arrow-right ms-1"></i>
            </button>
            <form id="submitForm" method="POST" action="{{ route('pte.exam.submit', $attempt->id) }}"
                  style="display:none;"
                  onsubmit="return confirm('Submit this PTE attempt? You will not be able to change answers afterwards.');">
                @csrf
                <button class="btn btn-success">
                    <i class="bi bi-check2-circle me-1"></i>Submit Exam
                </button>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    const saveUrl   = '{{ route("pte.exam.answer", $attempt->id) }}';
    const csrf      = '{{ csrf_token() }}';
    const startedAt = new Date('{{ $attempt->started_at?->toIso8601String() }}'.replace(/Z$/,'Z')).getTime();

    // ─── Top-bar timer (elapsed) ───────────────────────────────────────
    const $timer = $('#timer');
    function tickTimer() {
        const sec = Math.max(0, Math.floor((Date.now() - startedAt) / 1000));
        const h = String(Math.floor(sec / 3600)).padStart(2, '0');
        const m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
        const s = String(sec % 60).padStart(2, '0');
        $timer.text(`${h}:${m}:${s}`);
    }
    setInterval(tickTimer, 1000); tickTimer();

    // ─── Save helper ───────────────────────────────────────────────────
    function setStatus($card, state, text) {
        $card.find('[data-role=status]').removeClass('saving saved error').addClass(state).text(text);
    }
    function postAnswer($card, payload, fileBlob) {
        setStatus($card, 'saving', 'saving…');
        const fd = new FormData();
        fd.append('_token', csrf);
        fd.append('pte_module_wise_question_id', $card.data('mappingId'));
        Object.entries(payload).forEach(([k, v]) => {
            if (Array.isArray(v)) v.forEach(x => fd.append(`${k}[]`, x));
            else if (v !== null && v !== undefined) fd.append(k, v);
        });
        if (fileBlob) fd.append('audio_blob', fileBlob, 'recording.webm');
        return fetch(saveUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(r => r.json())
            .then(j => { setStatus($card, j.ok ? 'saved' : 'error', j.ok ? 'saved' : 'error'); return j; })
            .catch(() => setStatus($card, 'error', 'error'));
    }

    // ─── Per-card response widgets ─────────────────────────────────────
    $('.pte-question-card').each(function () {
        const $c   = $(this);
        const resp = $c.data('resp');

        if (resp === 'text_write' || (resp === 'fill_blank' && $c.find('[data-role=blank]').length === 0)) {
            const $ta = $c.find('[data-role=text-input]');
            const $wc = $c.find('[data-role=word-count]');
            const recount = () => $wc.text(($ta.val().trim().match(/\S+/g) || []).length);
            $ta.on('input', recount).on('blur', () => postAnswer($c, { response_text: $ta.val() }));
            recount();
        }
        if (resp === 'single_choice') {
            $c.on('change', '[data-role=single-opt]', function () {
                postAnswer($c, { selected_option_ids: [parseInt(this.value, 10)] });
            });
        }
        if (resp === 'select_option') {
            $c.on('change', '[data-role=select-opt]', function () {
                postAnswer($c, { selected_option_ids: this.value ? [parseInt(this.value, 10)] : [] });
            });
        }
        if (resp === 'multi_choice') {
            $c.on('change', '[data-role=multi-opt]', function () {
                const ids = $c.find('[data-role=multi-opt]:checked').map((_, el) => parseInt(el.value, 10)).get();
                postAnswer($c, { selected_option_ids: ids });
            });
        }
        if (resp === 'fill_blank' && $c.find('[data-role=blank]').length) {
            const collect = () => {
                const out = {};
                $c.find('[data-role=blank]').each(function () { out[$(this).data('order')] = $(this).val(); });
                return out;
            };
            $c.on('change blur', '[data-role=blank]', () => postAnswer($c, { response_blanks: collect() }));
        }
        if (resp === 'highlight_words') {
            $c.on('click', '[data-role=word-bag] .pte-hi-word', function () {
                $(this).toggleClass('marked');
                const ids = $c.find('[data-role=word-bag] .pte-hi-word.marked')
                              .map((_, el) => parseInt($(el).data('word-id'), 10)).get();
                postAnswer($c, { response_highlight_word_ids: ids });
            });
        }
        if (resp === 'reorder') {
            const $list = $c.find('[data-role=reorder-list]');
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
        if (resp === 'audio_record') {
            const $btn   = $c.find('[data-role=rec-toggle]');
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
                        $play.attr('src', URL.createObjectURL(blob)).show();
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
                        $rtime.text(String(Math.floor(seconds / 60)).padStart(2,'0') + ':' + String(seconds % 60).padStart(2,'0'));
                    }, 1000);
                } catch (err) {
                    alert('Microphone access denied or unavailable.');
                }
            }
            function stopRec() { if (recorder) recorder.stop(); }

            $btn.on('click', () => { recorder ? stopRec() : startRec(); });

            // Timed questions run the mic automatically — hide the manual button
            const timed = (parseInt($c.data('anst'), 10) || 0) > 0;
            if (timed) $btn.closest('.mb-2').hide();

            $c.data('recApi', { start: startRec, stop: stopRec, active: () => !!recorder });
        }
    });

    // ─── One-at-a-time navigation ──────────────────────────────────────
    const $cards    = $('.pte-question-card');
    const total     = $cards.length;
    $('#qTotal').text(total);

    const $prev   = $('#prevBtn');
    const $next   = $('#nextBtn');
    const $submit = $('#submitForm');
    const $pos    = $('#qPos');
    const $secPill= $('#sectionPill');
    const $secLbl = $('#sectionLabel');

    let current = 0; // 0-based

    // ─── Per-question prep/answer countdown engine ─────────────────────
    let qTick = null;
    let $activeCard = null;

    function clearQuestionTimer() {
        if (qTick) { clearInterval(qTick); qTick = null; }
    }

    function stopActiveRecording($card) {
        const api = $card && $card.data('recApi');
        if (api && api.active()) api.stop(); // onstop uploads the answer
    }

    function runQuestionTimers($card) {
        clearQuestionTimer();
        const prep    = parseInt($card.data('prep'), 10) || 0;
        const ans     = parseInt($card.data('anst'), 10) || 0;
        const isAudio = $card.data('resp') === 'audio_record';
        const $wrap   = $card.find('[data-role=qtimer]');
        const $lbl    = $card.find('[data-role=qtimer-label]');
        const $secs   = $card.find('[data-role=qtimer-secs]');

        $card.removeClass('phase-prep phase-answer');
        if (!prep && !ans) { $wrap.hide(); return; }
        $wrap.show();

        function phaseAnswer() {
            if (!ans) { $wrap.hide(); return; }
            $card.removeClass('phase-prep').addClass('phase-answer');
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
                    // Time up → advance automatically (recording upload happens onstop)
                    setTimeout(() => {
                        if (current < total - 1) { current++; showCurrent(); }
                    }, isAudio ? 800 : 300);
                }
            }, 1000);
        }

        if (prep > 0) {
            $card.addClass('phase-prep');
            $lbl.text('Preparation');
            let left = prep;
            $secs.text(left);
            qTick = setInterval(() => {
                left--;
                $secs.text(Math.max(0, left));
                if (left <= 0) { clearQuestionTimer(); phaseAnswer(); }
            }, 1000);
        } else {
            phaseAnswer();
        }
    }

    function showCurrent() {
        // Tear down the outgoing question first (stops mic + timers)
        clearQuestionTimer();
        stopActiveRecording($activeCard);

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

    // ─── Pre-exam screens (overview → headset/mic check → introduction) ──
    const preSteps   = $('.pte-pre-screen');
    const preLabels  = ['Test Overview', 'Headset & Microphone Check', 'Introduction to Yourself'];
    const $preFooter = $('#preFooter');
    const $preBack   = $('#preBackBtn');
    const $preNext   = $('#preNextBtn');
    const introSaved = @json((bool) $attempt->intro_audio_url);
    let preStep = 0;

    function showPreScreen(n) {
        preStep = n;
        preSteps.hide();
        preSteps.filter(`[data-pre-step=${n}]`).show();
        $('#preStepLabel').text(`Step ${n + 1} of ${preSteps.length} · ${preLabels[n]}`);
        $secPill.text(preLabels[n]);
        $preBack.prop('disabled', n === 0);
        // On the intro screen, Next becomes "Start Exam" and needs a saved recording
        if (n === preSteps.length - 1) {
            $preNext.html('Start Exam <i class="bi bi-play-fill ms-1"></i>')
                    .prop('disabled', !introSaved && !window.pteIntroUploaded);
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
        $('#qHeaderRow').show();
        $('#examFooter').css('display', 'flex');
        showCurrent();
    }

    $preBack.on('click', () => { if (preStep > 0) showPreScreen(preStep - 1); });
    $preNext.on('click', () => {
        if (preStep < preSteps.length - 1) showPreScreen(preStep + 1);
        else startExam();
    });

    // ── Headset check: play/stop a short recording (oscillator fallback) ──
    (function () {
        const $play = $('#hsPlayBtn'), $stop = $('#hsStopBtn'), $st = $('#hsStatus');
        const testAudio = new Audio('{{ asset("data/audio/pte/sound-check.mp3") }}');
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
            // Fallback tone sequence when the sound-check file is missing
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
    })();

    // ── Microphone check: record / stop / playback (local only) ──────────
    (function () {
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
    })();

    // ── Introduction: 25s auto-stop recording, uploaded to the attempt ───
    (function () {
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
            fetch('{{ route("pte.exam.intro", $attempt->id) }}', { method: 'POST', body: fd, credentials: 'same-origin' })
                .then(r => r.json())
                .then(j => {
                    if (j.ok) {
                        setStatus('Saved', 'bg-success');
                        window.pteIntroUploaded = true;
                        if (preStep === preSteps.length - 1) $preNext.prop('disabled', false);
                    } else setStatus('Upload failed', 'bg-danger');
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
                    player.style.display = 'block';
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
    })();

    // Entry point: resume attempts (intro already saved) skip straight to questions
    if (introSaved) startExam();
    else            showPreScreen(0);
})();
</script>
