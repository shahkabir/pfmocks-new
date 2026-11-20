{{-- Interactive Writing — two connected timed stages; Part 2 stays hidden until Part 1 ends. Vars: $q, $saved --}}
@php $texts = $saved?->response_texts ?? []; @endphp

@foreach($q->turns as $i => $turn)
    <div class="det-stage" data-role="stage" data-time="{{ $turn->response_time_sec }}" style="display:none;">
        <div class="fw-semibold mb-1">Part {{ $turn->turn_order }} of {{ $q->turns->count() }}</div>
        <div class="mb-2">{{ $turn->prompt_text }}</div>
        <textarea class="form-control" rows="9" data-role="stage-text" data-word-count spellcheck="false"
                  placeholder="Write your response">{{ $texts[$i] ?? '' }}</textarea>
        <div class="small text-muted text-end mt-1">Words: <span data-role="word-count">0</span></div>
    </div>
@endforeach
