@extends('layouts.app')
@section('title', 'Edit Question')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm" style="max-width:860px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Question #{{ $question->id }}</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.question-options.create', ['question_id' => $question->id]) }}"
                   class="btn btn-sm btn-outline-success">+ Add Option</a>
                <a href="{{ route('admin.question-groups.create', ['question_id' => $question->id]) }}"
                   class="btn btn-sm btn-outline-info">+ Add Group</a>
                <a href="{{ route('admin.questions.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
            </div>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.questions.update', $question->id) }}">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Exam</label>
                        <select id="exam_id" class="form-select">
                            <option value="">— Filter by Exam —</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}"
                                    {{ $question->module?->exam_id == $exam->id ? 'selected' : '' }}>
                                    {{ $exam->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Module <span class="text-danger">*</span></label>
                        <select name="module_id" id="module_id" class="form-select" required>
                            @foreach($modules as $m)
                                <option value="{{ $m->id }}"
                                    {{ old('module_id', $question->module_id) == $m->id ? 'selected' : '' }}>
                                    {{ $m->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Question Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            @foreach(['mcq_single','mcq_multiple','text','essay','audio','speaking'] as $t)
                                <option value="{{ $t }}"
                                    {{ old('type', $question->type) === $t ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $t)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Marks</label>
                        <input type="number" name="marks" class="form-control"
                               value="{{ old('marks', $question->marks) }}" min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control"
                               value="{{ old('sort_order', $question->sort_order) }}" min="0">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Question Header / Title</label>
                    <textarea name="question_header" id="question_header" class="form-control wysiwyg-sm">{!! old('question_header', $question->question_header) !!}</textarea>
                    <div class="form-text">Shown as a heading above this question's group.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Passage Instruction</label>
                    <textarea name="passage_instruction" id="passage_instruction" class="form-control wysiwyg-md">{!! old('passage_instruction', $question->passage_instruction) !!}</textarea>
                    <div class="form-text">Instruction shown above the passage (optional).</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Passage / Source Text</label>
                    <textarea name="passage" id="passage" class="form-control wysiwyg-lg">{!! old('passage', $question->passage) !!}</textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Audio URL</label>
                        <input type="text" name="audio_url" class="form-control"
                               value="{{ old('audio_url', $question->audio_url) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Image URL</label>
                        <input type="text" name="image_url" class="form-control"
                               value="{{ old('image_url', $question->image_url) }}">
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('exam_id').addEventListener('change', function () {
    const examId = this.value;
    const moduleSelect = document.getElementById('module_id');
    if (!examId) return;
    fetch('{{ route("admin.ajax.modules-by-exam", ":id") }}'.replace(':id', examId))
        .then(r => r.json())
        .then(data => {
            moduleSelect.innerHTML = '<option value="">— Select Module —</option>';
            data.forEach(m => {
                moduleSelect.innerHTML += `<option value="${m.id}">${m.name} (${m.module_type})</option>`;
            });
        });
});
</script>
@endsection

@push('page_scripts')
<script>
$(function () {
    var toolbarFull = [
        ['style',  ['style']],
        ['font',   ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
        ['para',   ['ul', 'ol', 'paragraph']],
        ['table',  ['table']],
        ['insert', ['link']],
        ['view',   ['codeview', 'fullscreen']],
    ];

    var toolbarMin = [
        ['font',   ['bold', 'italic', 'underline']],
        ['para',   ['ul', 'ol']],
        ['insert', ['link']],
        ['view',   ['codeview']],
    ];

    $('#question_header').summernote({
        height: 150,
        toolbar: toolbarMin,
    });

    $('#passage').summernote({
        height: 320,
        toolbar: toolbarFull,
    });

    $('#passage_instruction').summernote({
        placeholder: 'e.g. Read the passage and answer questions 1–10.',
        height: 180,
        toolbar: toolbarMin,
    });
});
</script>
@endpush
