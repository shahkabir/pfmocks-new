@include('layouts.header')

@section('title', 'Duolingo English Test — ' . $module->name)

<link rel="stylesheet" href="{{ asset('css/duolingo-exam.css') }}">

{{-- ── Top bar ──────────────────────────────────────────────────────── --}}
<div class="det-bar">
    <div class="left">
        <span class="det-section-pill" id="detSectionPill">Overview</span>
    </div>
    <div class="mid">
        <i class="bi bi-stopwatch me-1"></i>
        <span id="detTimer">--:--</span>
    </div>
    <div class="right">
        <a href="{{ route('dashboard.student') }}" class="btn btn-light btn-sm"
           onclick="return confirm('Exit the exam? Answers are not saved yet, so this attempt will restart from the beginning.');">
            <i class="bi bi-box-arrow-right me-1"></i>Exit
        </a>
    </div>
</div>

<div class="container py-3 det-container">
    <div class="d-flex justify-content-between small text-muted mb-2">
        <div><strong>{{ $module->name }}</strong> · Attempt #{{ $attempt->id }}</div>
        <div id="detProgress" style="display:none;">Question <span id="detPos">0</span> of <span id="detTotal">0</span></div>
    </div>

    @if($structure->isEmpty())
        <div class="alert alert-warning">
            No Duolingo sections are mapped to this module. Ask the admin to assemble the mock test.
        </div>
    @endif

    {{-- ════════════════ Overview ════════════════ --}}
    <div class="det-card" id="detOverview">
        <h4 class="mb-3"><i class="bi bi-clipboard-data me-2 text-success"></i>{{ $module->name }}</h4>
        <p class="text-muted">
            Questions appear one at a time and most have their own timer. You cannot go back to an
            earlier question, and when the time runs out the test moves on automatically.
        </p>
        <table class="table table-bordered align-middle" style="max-width:640px;">
            <thead class="table-light">
                <tr><th>Section</th><th class="text-center">Questions</th><th class="text-center">Time</th></tr>
            </thead>
            <tbody>
                @foreach($structure as $dm)
                    <tr>
                        <td>{{ $dm->section?->name }}</td>
                        <td class="text-center">{{ $dm->moduleQuestions->count() }}</td>
                        <td class="text-center">{{ $dm->section?->time_allowed_minutes ? $dm->section->time_allowed_minutes . ' min' : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p class="small text-muted">
            <i class="bi bi-mic me-1"></i>The speaking section needs your microphone, and listening tasks need audio.
        </p>
        <button type="button" class="btn btn-success" data-det-action="begin" @disabled($structure->isEmpty())>
            Begin <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </div>

    {{-- ════════════════ Questions ════════════════ --}}
    @foreach($structure as $dm)
        @foreach($dm->moduleQuestions as $mwq)
            @php
                $q     = $mwq->question;
                $type  = $q?->type;
                $saved = $answers[$mwq->id] ?? null;
            @endphp
            @continue(!$q || !$type)

            <div class="det-card det-question" style="display:none;"
                 data-mapping-id="{{ $mwq->id }}"
                 data-response-type="{{ $type->response_type }}"
                 data-section="{{ $dm->section?->name }}"
                 data-answer-time="{{ $q->answerTimeSec() }}"
                 data-group-id="{{ $q->duolingo_question_group_id }}"
                 data-group-limit="{{ $q->group?->time_limit_sec }}">

                <div class="mb-2">
                    <span class="badge bg-light text-dark border">{{ $type->name }}</span>
                </div>

                @if($type->instructions)
                    <p class="det-instructions">{{ $type->instructions }}</p>
                @endif

                @include('exams.duolingo.partials._stimulus', ['q' => $q, 'type' => $type])

                @includeIf('exams.duolingo.questions._' . $type->response_type, [
                    'q' => $q, 'type' => $type, 'mwq' => $mwq, 'saved' => $saved,
                ])
            </div>
        @endforeach
    @endforeach

    {{-- ════════════════ Navigation ════════════════ --}}
    <div class="text-end mt-3" id="detNav" style="display:none;">
        <button type="button" class="btn btn-success" data-det-action="next">
            <span id="detNextLabel">Next</span><i class="bi bi-arrow-right ms-1"></i>
        </button>
    </div>

    {{-- ════════════════ Finish ════════════════ --}}
    <div class="det-card text-center" id="detFinish" style="display:none;">
        <h4 class="mb-2"><i class="bi bi-flag me-2 text-success"></i>You have reached the end of the test</h4>
        <p class="text-muted mb-1">You responded to <strong id="detAnsweredCount">0</strong> of <span id="detAnsweredTotal">0</span> questions.</p>
        <p class="small text-muted">Answer saving is not available yet, so this attempt will not be scored.</p>
        <form method="POST" action="{{ route('duolingo.exam.submit', $attempt->id) }}"
              onsubmit="return confirm('Submit the test?');">
            @csrf
            <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i>Submit test</button>
        </form>
    </div>
</div>

<script>
    window.DET_CONFIG = {
        answerUrl: @json(route('duolingo.exam.answer', $attempt->id)),
        csrf:      @json(csrf_token()),
    };
</script>
<script src="{{ asset('js/duolingo-exam.js') }}"></script>
