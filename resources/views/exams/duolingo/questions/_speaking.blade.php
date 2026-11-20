{{-- Speak About the Photo · Read, Then Speak · Listen, Then Speak · Speaking Sample. Vars: $q, $saved --}}
@if($q->question_text)
    <div class="det-passage mb-3">{{ $q->question_text }}</div>
@endif

@include('exams.duolingo.partials._recorder', ['savedUrl' => $saved?->response_audio_url])
