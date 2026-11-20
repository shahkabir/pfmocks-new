{{-- Write About the Photo · Summarize the Conversation. Vars: $q, $saved --}}
@if($q->question_text)
    <div class="fw-semibold mb-2">{{ $q->question_text }}</div>
@endif

<textarea class="form-control" rows="6" data-role="text-input" data-word-count spellcheck="false"
          placeholder="Write your response">{{ $saved?->response_text }}</textarea>
<div class="small text-muted text-end mt-1">
    Words: <span data-role="word-count">0</span>
    @if($q->min_word_count) · min {{ $q->min_word_count }} @endif
    @if($q->max_word_count) · max {{ $q->max_word_count }} @endif
</div>
