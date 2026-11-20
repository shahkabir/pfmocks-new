{{-- Interactive Reading / Listening subtasks — single option. Vars: $q, $mwq, $saved --}}
@php $selected = $saved?->selected_option_ids ?? []; @endphp

@if($q->question_text)
    <div class="fw-semibold mb-2">{!! nl2br(e($q->question_text)) !!}</div>
@endif

<div class="list-group">
    @foreach($q->options as $opt)
        <label class="list-group-item d-flex gap-2">
            <input class="form-check-input" type="radio" name="det_mcq_{{ $mwq->id }}"
                   value="{{ $opt->id }}" data-role="option" @checked(in_array($opt->id, $selected))>
            <span>{{ $opt->option_text }}</span>
        </label>
    @endforeach
</div>
