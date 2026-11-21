{{-- Exam listing action for a Duolingo module, based on the student's latest attempt. Vars: $module --}}
@php
    $latest = \App\Models\Duolingo\DuolingoAttempt::where('user_id', auth()->id())
        ->where('module_id', $module->id)
        ->latest('id')
        ->first();
@endphp

<div class="d-flex flex-wrap justify-content-end align-items-center gap-1">
    @if($latest?->isInProgress())
        <a href="{{ route('exam.start', $module->id) }}" target="_blank" class="btn btn-buy">
            <i class="bi bi-play-fill"></i> Resume
        </a>
    @else
        @if($latest?->isPublished())
            <a href="{{ route('duolingo.exam.result', $latest->id) }}" target="_blank" class="chip chip-completed text-decoration-none">
                <i class="bi bi-bar-chart-fill"></i>Score {{ $latest->overall_score ?? '—' }}
            </a>
        @elseif($latest)
            <a href="{{ route('duolingo.exam.result', $latest->id) }}" target="_blank" class="chip chip-pending text-decoration-none">
                <i class="bi bi-hourglass-split"></i>Under evaluation
            </a>
        @endif
        <a href="{{ route('exam.start', $module->id) }}" target="_blank" class="btn btn-buy">
            <i class="bi bi-play-fill"></i> {{ $latest ? 'Retake' : 'Start' }}
        </a>
    @endif
</div>
