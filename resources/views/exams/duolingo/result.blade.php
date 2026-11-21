@include('layouts.header')

@section('title', 'Duolingo English Test — Result')

<link rel="stylesheet" href="{{ asset('css/duolingo-exam.css') }}">

@php
    $skills = [
        'reading'   => ['label' => 'Reading',   'score' => $attempt->reading_score],
        'writing'   => ['label' => 'Writing',   'score' => $attempt->writing_score],
        'listening' => ['label' => 'Listening', 'score' => $attempt->listening_score],
        'speaking'  => ['label' => 'Speaking',  'score' => $attempt->speaking_score],
    ];
    $integrated = [
        'Literacy'      => ['score' => $attempt->literacy_score,      'from' => 'Reading + Writing'],
        'Comprehension' => ['score' => $attempt->comprehension_score, 'from' => 'Reading + Listening'],
        'Conversation'  => ['score' => $attempt->conversation_score,  'from' => 'Listening + Speaking'],
        'Production'    => ['score' => $attempt->production_score,    'from' => 'Speaking + Writing'],
    ];
    $raw = $attempt->raw_scores ?? [];
    $fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 2), '0'), '.');
@endphp

<div class="container py-4 det-container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($attempt->isInProgress())
        {{-- ── Not submitted yet ───────────────────────────────────── --}}
        <div class="det-card text-center">
            <h4 class="mb-2">This test is still in progress</h4>
            <p class="text-muted">Your answers are saved. You can continue where you left off.</p>
            <a href="{{ route('duolingo.exam.play', $attempt->id) }}" class="btn btn-success">Resume test</a>
        </div>

    @elseif(!$attempt->isPublished())
        {{-- ── Waiting for the evaluator ───────────────────────────── --}}
        <div class="det-card text-center">
            <div class="text-muted small">{{ $module->name }} · Attempt #{{ $attempt->id }}</div>
            <div class="my-3"><i class="bi bi-hourglass-split text-warning" style="font-size:3rem;"></i></div>
            <h4 class="mb-2">Your test is being evaluated</h4>
            <p class="text-muted mb-1">
                Reading and listening answers have been marked. An evaluator is now reviewing your writing and speaking responses.
            </p>
            <p class="text-muted">Your final score will be published here after the evaluation, and we will email you when it is ready.</p>
            <div class="small text-muted">
                You answered {{ $answeredCount }} of {{ $questionCount }} questions · submitted {{ $attempt->ended_at?->format('d M Y, H:i') }}
            </div>
        </div>

    @else
        {{-- ── Published ───────────────────────────────────────────── --}}
        <div class="det-card text-center">
            <div class="text-muted small">{{ $module->name }} · Attempt #{{ $attempt->id }}</div>
            <div class="det-overall my-2">{{ $attempt->overall_score ?? '—' }}</div>
            <div class="text-muted">Estimated overall score (10–160)</div>
            <div class="small text-muted mt-1">Published {{ $attempt->published_at?->format('d M Y, H:i') }}</div>
        </div>

        <div class="det-card">
            <h5 class="mb-3">Individual subscores</h5>
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Skill</th><th class="text-center">Raw performance</th><th class="text-center">Estimated score</th></tr>
                </thead>
                <tbody>
                    @foreach($skills as $key => $s)
                        @php $r = $raw[$key] ?? null; @endphp
                        <tr>
                            <td>{{ $s['label'] }}</td>
                            <td class="text-center">
                                @if($r && $r['max'] > 0)
                                    {{ $fmt($r['earned']) }} / {{ $fmt($r['max']) }} marks ({{ $fmt($r['percent']) }}%)
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center fw-semibold">{{ $s['score'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="det-card">
            <h5 class="mb-3">Integrated subscores</h5>
            <div class="row g-3">
                @foreach($integrated as $name => $i)
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-3 text-center h-100">
                            <div class="fs-4 fw-bold">{{ $i['score'] !== null ? $fmt($i['score']) : '—' }}</div>
                            <div class="fw-semibold">{{ $name }}</div>
                            <div class="small text-muted">{{ $i['from'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if($evaluation?->feedback_text)
            <div class="det-card">
                <h5 class="mb-2">Evaluator feedback</h5>
                <div style="white-space:pre-wrap;">{{ $evaluation->feedback_text }}</div>
            </div>
        @endif

        <div class="det-card">
            <h5 class="mb-3">Question by question</h5>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Question type</th><th>Your answer</th><th>Correct answer</th><th class="text-center">Result</th></tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $n => $row)
                            @include('exams.duolingo.partials._result_row', ['n' => $n + 1, 'row' => $row])
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <p class="small text-muted">
        Raw performance and estimated scores are shown separately. Estimated scores are a PerfectMocks
        practice estimate, not an official Duolingo English Test score.
    </p>

    <a href="{{ route('exams.show', ['examName' => 'duolingo']) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Duolingo tests
    </a>
</div>
