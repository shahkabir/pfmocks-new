@extends('layouts.app')
@section('title', 'Create Question')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm" style="max-width:860px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create Question</h5>
            <a href="{{ route('admin.questions.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.questions.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Exam → Module cascade --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Exam</label>
                        <select id="exam_id" class="form-select">
                            <option value="">— Filter by Exam —</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Module <span class="text-danger">*</span></label>
                        <select name="module_id" id="module_id" class="form-select" required>
                            <option value="">— Select Module —</option>
                            @foreach($modules as $module)
                                <option value="{{ $module->id }}" {{ old('module_id') == $module->id ? 'selected' : '' }}>
                                    {{ $module->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Question Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="">— Select —</option>
                            @foreach(['mcq_single','mcq_multiple','text','essay','audio','speaking'] as $t)
                                <option value="{{ $t }}" {{ old('type') === $t ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $t)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Marks</label>
                        <input type="number" name="marks" class="form-control"
                               value="{{ old('marks', 1) }}" min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control"
                               value="{{ old('sort_order', 0) }}" min="0">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Question Header / Title</label>
                    <textarea name="question_header" id="question_header" class="form-control wysiwyg-sm">{{ old('question_header') }}</textarea>
                    <div class="form-text">Shown as a heading above this question's group.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Passage Instruction</label>
                    <textarea name="passage_instruction" id="passage_instruction" class="form-control wysiwyg-md"
                              placeholder="e.g. Read the passage and answer questions 1–10.">{{ old('passage_instruction') }}</textarea>
                    <div class="form-text">Instruction shown above the passage (optional).</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Passage / Source Text</label>
                    <textarea name="passage" id="passage" class="form-control wysiwyg-lg"
                              placeholder="Paste reading passage or listening script here (optional)">{{ old('passage') }}</textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Audio File</label>
                        <input type="file" name="audio_file" class="form-control"
                               accept="audio/*">
                        <div class="form-text">Optional. Allowed: {{ implode(', ', config('upload_audio.mimes')) }}.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Image File</label>
                        <input type="file" name="image_file" class="form-control"
                               accept="image/*">
                        <div class="form-text">Optional. Allowed: {{ implode(', ', config('upload_image.mimes')) }} (max {{ config('upload_image.max_size_kb') }} KB).</div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-success">Create Question</button>
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
    moduleSelect.innerHTML = '<option value="">Loading…</option>';

    if (!examId) {
        moduleSelect.innerHTML = '<option value="">— Select Module —</option>';
        return;
    }

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
        placeholder: 'e.g. General Knowledge — Part 1',
        height: 150,
        toolbar: toolbarMin,
        callbacks: {
            onInit: function () {
                // Remove required so browser doesn't block on hidden textarea
                $('#question_header').removeAttr('required');
            }
        }
    });

    $('#passage').summernote({
        placeholder: 'Paste reading passage or listening script here…',
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
