{{-- Read and Select · Listen and Select — is this a real English word? Vars: $q, $type, $mwq, $saved --}}
@php $val = $saved?->response_text; @endphp

@if($q->question_text && $type->stimulus_type === 'word')
    <div class="det-word text-center my-4">{{ $q->question_text }}</div>
@endif

<p class="text-center fw-semibold">Is this a real English word?</p>
<div class="d-flex justify-content-center gap-3">
    @foreach(['yes' => 'Yes', 'no' => 'No'] as $v => $label)
        <input type="radio" class="btn-check" name="det_wc_{{ $mwq->id }}" id="det_wc_{{ $mwq->id }}_{{ $v }}"
               value="{{ $v }}" data-role="word-choice" @checked($val === $v)>
        <label class="btn btn-outline-success px-5" for="det_wc_{{ $mwq->id }}_{{ $v }}">{{ $label }}</label>
    @endforeach
</div>
