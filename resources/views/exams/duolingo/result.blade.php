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
@endphp

<div class="container py-4 det-container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="det-card text-center">
        <div class="text-muted small">{{ $module->name }} · Attempt #{{ $attempt->id }}</div>
        <div class="det-overall my-2">{{ $attempt->overall_score ?? '—' }}</div>
        <div class="text-muted">Estimated overall score (10–160)</div>
        @if($attempt->status !== \App\Models\Duolingo\DuolingoAttempt::STATUS_COMPLETED)
            <div class="alert alert-info mt-3 mb-0">This attempt has not been submitted yet.</div>
        @endif
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
                                {{ $r['earned'] }} / {{ $r['max'] }} ({{ $r['percent'] }}%)
                            @else
                                <span class="text-muted">Not evaluated</span>
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
                        <div class="fs-4 fw-bold">{{ $i['score'] !== null ? rtrim(rtrim(number_format($i['score'], 1), '0'), '.') : '—' }}</div>
                        <div class="fw-semibold">{{ $name }}</div>
                        <div class="small text-muted">{{ $i['from'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <p class="small text-muted">
        Raw performance and estimated scores are shown separately. Estimated scores are a PerfectMocks
        practice estimate, not an official Duolingo English Test score.
    </p>

    <a href="{{ route('dashboard.student') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to dashboard
    </a>
</div>
