@include('layouts.header')

@section('title', 'PTE Exam — ' . $module->name)

<link rel="stylesheet" href="{{ asset('css/pte-exam.css') }}">

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

{{-- ── Full-screen loader shown just before the exam begins ── --}}
<div id="examLoader">
    <div class="spinner-border text-primary" style="width:3.5rem;height:3.5rem;"></div>
    <div class="mt-3 fs-5 fw-semibold text-primary">Take a deep breath!</div>
    <div class="text-muted">We are preparing your exam.</div>
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
                    <tr>
                        <td class="text-center fw-semibold">0</td>
                        <td class="text-center fw-semibold">Self-Introduction</td>
                        <td class="text-center"></td>
                        <td class="text-center">25 sec</td>
                    </tr>
                    @foreach($structure as $i => $pm)
                        @php
                            $cnt = $pm->moduleQuestions->count();
                            $totalQ  += $cnt;
                            $totalMin += (int) ($pm->section?->time_allowed_minutes ?? 0);
                        @endphp
                        <tr>
                            <td class="text-center fw-semibold">{{ $i + 1}}</td>
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
            <div class="d-flex align-items-center gap-2 mt-2">
                <audio id="inPlayback" style="display:none;"></audio>
                <button type="button" class="pte-play-btn" id="inPlayBtn" style="display:none;">
                    <i class="bi bi-play-fill"></i>
                </button>
                <span class="small text-muted" id="inPlayHint" style="display:none;">Play your introduction</span>
            </div>
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
    {{-- {{ dd(get_defined_vars()) }} --}}
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
                 data-stim="{{ $stim }}"
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
                    <div class="pte-audio-player mb-3" data-role="stimulus-audio-wrap">
                        <audio data-role="stimulus-audio" src="{{ asset(ltrim($q->audio_url, '/')) }}" preload="metadata"></audio>
                        <button type="button" class="pte-audio-play" data-role="stimulus-audio-play" aria-label="Play audio">
                            <i class="bi bi-play-fill"></i>
                        </button>
                        <div class="pte-audio-progress-wrap">
                            <div class="pte-audio-progress-track">
                                <div class="pte-audio-progress-fill" data-role="stimulus-audio-progress"></div>
                            </div>
                            <div class="pte-audio-time" data-role="stimulus-audio-time">0:00 / 0:00</div>
                        </div>
                        <div class="pte-audio-volume">
                            <i class="bi bi-volume-up-fill"></i>
                            <input type="range" min="0" max="1" step="0.05" value="1"
                                   data-role="stimulus-audio-volume" aria-label="Volume">
                        </div>
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
                        <div class="d-flex align-items-center gap-2 mt-2" data-role="rec-play-wrap">
                            <audio data-role="rec-playback"
                                   @if($saved?->response_audio_url) src="{{ asset(ltrim($saved->response_audio_url, '/')) }}" @endif
                                   style="display:none;"></audio>
                            <button type="button" class="pte-play-btn" data-role="rec-play"
                                    style="{{ $saved?->response_audio_url ? '' : 'display:none;' }}">
                                <i class="bi bi-play-fill"></i>
                            </button>
                            <span class="small text-muted" data-role="rec-play-hint"
                                  style="{{ $saved?->response_audio_url ? '' : 'display:none;' }}">Play your recording</span>
                        </div>
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
                        <div class="pte-option-list" data-role="single-opt-list">
                            @foreach($opts as $opt)
                                @php $isChecked = (int)$picked === (int)$opt->id; @endphp
                                <label class="pte-option-row {{ $isChecked ? 'is-selected' : '' }}">
                                    <input class="pte-option-input" type="radio"
                                           name="opt-{{ $mwq->id }}"
                                           data-role="single-opt"
                                           value="{{ $opt->id }}"
                                           {{ $isChecked ? 'checked' : '' }}>
                                    <span class="pte-option-text">{{ $opt->option_text }}</span>
                                </label>
                            @endforeach
                        </div>
                        @break

                    @case('multi_choice')
                        @php $picks = $saved?->selected_option_ids ?? []; @endphp
                        <div class="pte-option-list" data-role="multi-opt-list">
                            @foreach($opts as $opt)
                                @php $isChecked = in_array((int)$opt->id, array_map('intval',$picks), true); @endphp
                                <label class="pte-option-row {{ $isChecked ? 'is-selected' : '' }}">
                                    <input class="pte-option-input" type="checkbox"
                                           data-role="multi-opt"
                                           value="{{ $opt->id }}"
                                           {{ $isChecked ? 'checked' : '' }}>
                                    <span class="pte-option-text">{{ $opt->option_text }}</span>
                                </label>
                            @endforeach
                        </div>
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
    <div class="pte-footer" id="examFooter" style="display:none;">
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
    window.PteExamConfig = {
        saveUrl:       @json(route('pte.exam.answer', $attempt->id)),
        introUrl:      @json(route('pte.exam.intro', $attempt->id)),
        csrf:          @json(csrf_token()),
        startedAtIso:  @json(optional($attempt->started_at)->toIso8601String()),
        introSaved:    @json((bool) $attempt->intro_audio_url),
        soundCheckUrl: @json(asset('data/audio/pte/sound-check.mp3')),
    };
</script>
<script src="{{ asset('js/pte-exam.js') }}"></script>
