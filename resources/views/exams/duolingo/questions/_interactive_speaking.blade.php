{{-- Interactive Speaking — one timed turn at a time, no preparation. Vars: $q, $saved --}}
@php $recordings = $saved?->response_audio_urls ?? []; @endphp

@if($q->question_text)
    <div class="small text-muted mb-2">{{ $q->question_text }}</div>
@endif

@forelse($q->turns as $turn)
    <div class="det-turn" data-role="turn" data-time="{{ $turn->response_time_sec ?? $q->answerTimeSec() }}" style="display:none;">
        <div class="small text-muted mb-1">Turn {{ $turn->turn_order }} of {{ $q->turns->count() }}</div>
        <div class="fw-semibold">{{ $turn->speaker ?? 'Interviewer' }}</div>
        @if($turn->prompt_text)<div class="mb-2">{{ $turn->prompt_text }}</div>@endif
        @if($turn->audio_url)
            <div class="mb-3">
                @include('exams.duolingo.partials._audio', ['src' => $turn->audio_url, 'maxReplays' => 1, 'autoplay' => true])
            </div>
        @endif
        @include('exams.duolingo.partials._recorder', ['savedUrl' => $recordings[(string) $turn->turn_order] ?? null])
    </div>
@empty
    <div class="text-muted">No conversation turns configured.</div>
@endforelse
