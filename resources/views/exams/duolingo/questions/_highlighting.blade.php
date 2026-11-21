{{-- Interactive Reading — Highlight the Answer. Selection is captured from the passage above. Vars: $q, $saved --}}
@php $hl = $saved?->response_highlight; @endphp

@if($q->question_text)
    <div class="fw-semibold mb-2">{{ $q->question_text }}</div>
@endif

<div class="border rounded p-2 bg-light small d-flex justify-content-between align-items-center gap-2">
    <div>
        <span class="text-muted">Your highlight:</span>
        <span data-role="highlight-preview" data-empty="Nothing selected yet."
              data-saved="{{ $hl ? json_encode($hl) : '' }}">{{ $hl['text'] ?? 'Nothing selected yet.' }}</span>
    </div>
    <button type="button" class="btn btn-sm btn-outline-secondary" data-role="highlight-clear">Clear</button>
</div>
