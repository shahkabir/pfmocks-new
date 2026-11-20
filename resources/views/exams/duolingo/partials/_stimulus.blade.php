{{-- Shared stimulus: group passage / scenario, image, audio. Vars: $q, $type --}}
@php
    $group = $q->group;
    // Early Interactive Reading steps show a partial passage so they cannot reveal their own answer.
    $passage = $q->config['passage_override'] ?? $group?->passage_text;
    $autoplay = in_array($type->tag, ['LSL', 'ILLR'], true);
@endphp

@if($group)
    <div class="det-group mb-3">
        @if($group->scenario)<div class="small text-muted mb-2">{{ $group->scenario }}</div>@endif
        {{-- Text is rendered on one line: highlight offsets are measured against this element's text. --}}
        @if($passage)<div class="det-passage {{ $type->response_type === 'highlighting' ? 'det-selectable' : '' }}" data-role="passage">{{ $passage }}</div>@endif
    </div>
@endif

@if($q->image_url)
    <div class="mb-3 text-center">
        <img src="{{ asset(ltrim($q->image_url, '/')) }}" alt="{{ $q->image_alt_text }}" class="img-fluid rounded det-image">
    </div>
@endif

@if($q->audio_url)
    <div class="mb-3">
        @include('exams.duolingo.partials._audio', [
            'src' => $q->audio_url, 'maxReplays' => $q->maxReplays(), 'autoplay' => $autoplay,
        ])
    </div>
@endif
