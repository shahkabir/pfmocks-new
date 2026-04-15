@extends('layouts.app')
@section('title', 'Edit Question Option')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm" style="max-width:860px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Option #{{ $option->id }}</h5>
            <a href="{{ route('admin.question-options.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.question-options.update', $option->id) }}">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Question <span class="text-danger">*</span></label>
                        <select name="question_id" class="form-select" required>
                            @foreach($questions as $q)
                                <option value="{{ $q->id }}"
                                    {{ old('question_id', $option->question_id) == $q->id ? 'selected' : '' }}>
                                    Q#{{ $q->id }} — {{ $q->module?->name }} | {{ Str::limit($q->question_header, 50) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Question Type <span class="text-danger">*</span></label>
                        <select name="question_type" id="question_type" class="form-select" required>
                            @foreach(['mcq_single','mcq_multiple','fill_in_blanks','writing','essay','audio','speaking','ielts_speaking','no_question','generic_question','highlighting','reordering'] as $qt)
                                <option value="{{ $qt }}"
                                    {{ old('question_type', $option->question_type) === $qt ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $qt)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3 field-actual-question">
                    <label class="form-label fw-semibold">Actual Question Text</label>
                    <textarea name="actual_question" class="form-control" rows="3"
                              placeholder="For fill_in_blanks use [[blank]]">{{ old('actual_question', $option->actual_question) }}</textarea>
                    <div class="form-text field-hint-fill_in_blanks" style="display:none">
                        Use <code>[[blank]]</code> where the input should appear.
                    </div>
                </div>

                <div class="mb-3 field-option-text">
                    <label class="form-label fw-semibold">Option Text</label>
                    <input type="text" name="option_text" class="form-control"
                           value="{{ old('option_text', $option->option_text) }}">
                </div>

                <div class="mb-3 field-is-correct">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_correct" id="is_correct" value="1"
                               {{ old('is_correct', $option->is_correct) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_correct">Correct answer</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Correct Answer Explanation</label>
                    <textarea name="correct_answer_explanation" class="form-control" rows="2">{{ old('correct_answer_explanation', $option->correct_answer_explanation) }}</textarea>
                </div>

                <div class="mb-3 field-listening-line" style="display:none">
                    <label class="form-label fw-semibold">Listening Question Line Reference</label>
                    <input type="text" name="ielts_listening_question_line" class="form-control"
                           value="{{ old('ielts_listening_question_line', $option->ielts_listening_question_line) }}">
                </div>

                <div class="mb-3 field-image-path" style="display:none">
                    <label class="form-label fw-semibold">Question Image Path</label>
                    <input type="text" name="question_image_path" class="form-control"
                           value="{{ old('question_image_path', $option->question_image_path) }}">
                </div>

                <div class="mb-3 field-audio-path" style="display:none">
                    <label class="form-label fw-semibold">Question Audio Path</label>
                    <input type="text" name="question_audio_path" class="form-control"
                           value="{{ old('question_audio_path', $option->question_audio_path) }}">
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control"
                               value="{{ old('sort_order', $option->sort_order) }}" min="0">
                    </div>
                    <div class="col-md-4 d-flex align-items-end pb-1">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                                   {{ old('is_active', $option->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('admin.question-options.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const typeRules = {
    mcq_single:       { optionText: true,  isCorrect: true,  actualQ: true,  listening: false, image: false, audio: false },
    mcq_multiple:     { optionText: true,  isCorrect: true,  actualQ: true,  listening: false, image: false, audio: false },
    fill_in_blanks:   { optionText: false, isCorrect: true,  actualQ: true,  listening: false, image: false, audio: false },
    writing:          { optionText: false, isCorrect: false, actualQ: true,  listening: false, image: false, audio: false },
    essay:            { optionText: false, isCorrect: false, actualQ: true,  listening: false, image: false, audio: false },
    audio:            { optionText: false, isCorrect: false, actualQ: false, listening: false, image: false, audio: true  },
    speaking:         { optionText: false, isCorrect: false, actualQ: true,  listening: false, image: false, audio: true  },
    ielts_speaking:   { optionText: false, isCorrect: false, actualQ: false, listening: false, image: true,  audio: true  },
    no_question:      { optionText: false, isCorrect: false, actualQ: false, listening: true,  image: false, audio: false },
    generic_question: { optionText: false, isCorrect: false, actualQ: true,  listening: false, image: false, audio: false },
    highlighting:     { optionText: true,  isCorrect: true,  actualQ: true,  listening: false, image: false, audio: false },
    reordering:       { optionText: true,  isCorrect: true,  actualQ: true,  listening: false, image: false, audio: false },
};
function toggle(selector, show) {
    document.querySelectorAll(selector).forEach(el => el.style.display = show ? '' : 'none');
}
function applyTypeRules(type) {
    const r = typeRules[type] || {};
    toggle('.field-option-text',    r.optionText);
    toggle('.field-is-correct',     r.isCorrect);
    toggle('.field-actual-question',r.actualQ);
    toggle('.field-listening-line', r.listening);
    toggle('.field-image-path',     r.image);
    toggle('.field-audio-path',     r.audio);
    document.querySelector('.field-hint-fill_in_blanks').style.display = (type === 'fill_in_blanks') ? '' : 'none';
}
document.getElementById('question_type').addEventListener('change', function () { applyTypeRules(this.value); });
applyTypeRules(document.getElementById('question_type').value);
</script>
@endsection
