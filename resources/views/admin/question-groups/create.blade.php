@extends('layouts.app')
@section('title', 'Create Question Group')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm" style="max-width:780px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create Question Group</h5>
            <a href="{{ route('admin.question-groups.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.question-groups.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Question <span class="text-danger">*</span></label>
                    <select name="question_id" id="question_id" class="form-select" required>
                        <option value="">— Select Question —</option>
                        @foreach($questions as $q)
                            <option value="{{ $q->id }}"
                                {{ (old('question_id', $preselectedQuestionId) == $q->id) ? 'selected' : '' }}>
                                Q#{{ $q->id }} — {{ $q->module?->name }} | {{ Str::limit($q->question_header, 60) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Each question can have only one group. Group organises options into parts & blocks.</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Part Number</label>
                        <input type="number" name="part_number" class="form-control"
                               value="{{ old('part_number', 1) }}" min="1" max="10"
                               placeholder="1">
                        <div class="form-text">IELTS Part 1–4 or section number.</div>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label fw-semibold">Part Audio URL</label>
                        <input type="text" name="part_audio_url" class="form-control"
                               value="{{ old('part_audio_url') }}" placeholder="https://...">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Part Image URL</label>
                    <input type="text" name="part_image_url" class="form-control"
                           value="{{ old('part_image_url') }}" placeholder="https://...">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Grouped Option IDs</label>
                    <div class="form-text mb-2">Select the question options that belong to this group.
                        First select a question above, then the options will load.</div>
                    <select name="question_options_group_ids[]" id="options_select"
                            class="form-select" multiple size="6">
                        @foreach($options as $opt)
                            <option value="{{ $opt->id }}"
                                {{ in_array($opt->id, old('question_options_group_ids', [])) ? 'selected' : '' }}>
                                #{{ $opt->id }} [{{ $opt->question_type }}] — {{ Str::limit($opt->actual_question ?: $opt->option_text, 70) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Hold Ctrl/Cmd to select multiple.</div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-success">Create Group</button>
                    <a href="{{ route('admin.question-groups.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('question_id').addEventListener('change', function () {
    const qId = this.value;
    const sel = document.getElementById('options_select');
    sel.innerHTML = '<option disabled>Loading…</option>';

    if (!qId) { sel.innerHTML = ''; return; }

    fetch('{{ route("admin.ajax.options-by-question", ":id") }}'.replace(':id', qId))
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '';
            data.forEach(o => {
                const label = `#${o.id} [${o.question_type}] — ${(o.actual_question || o.option_text || '').substring(0, 70)}`;
                sel.innerHTML += `<option value="${o.id}">${label}</option>`;
            });
        });
});
</script>
@endsection
