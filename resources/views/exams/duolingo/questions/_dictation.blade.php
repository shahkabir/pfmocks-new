{{-- Listen and Type — type the sentence you hear. Vars: $saved --}}
<label class="form-label fw-semibold">Type the statement that you hear.</label>
<textarea class="form-control" rows="3" data-role="text-input" spellcheck="false"
          placeholder="Your response">{{ $saved?->response_text }}</textarea>
