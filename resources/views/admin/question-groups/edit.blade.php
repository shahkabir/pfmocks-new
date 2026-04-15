@extends('layouts.app')
@section('title', 'Edit Question Group')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm" style="max-width:780px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Group #{{ $group->id }}</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.question-group-blocks.create', ['group_id' => $group->id]) }}"
                   class="btn btn-sm btn-outline-success">+ Add Block</a>
                <a href="{{ route('admin.question-groups.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
            </div>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.question-groups.update', $group->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Question <span class="text-danger">*</span></label>
                    <select name="question_id" id="question_id" class="form-select" required>
                        @foreach($questions as $q)
                            <option value="{{ $q->id }}"
                                {{ old('question_id', $group->question_id) == $q->id ? 'selected' : '' }}>
                                Q#{{ $q->id }} — {{ $q->module?->name }} | {{ Str::limit($q->question_header, 60) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Part Number</label>
                        <input type="number" name="part_number" class="form-control"
                               value="{{ old('part_number', $group->part_number) }}" min="1" max="10">
                    </div>
                    <div class="col-md-9">
                        <label class="form-label fw-semibold">Part Audio URL</label>
                        <input type="text" name="part_audio_url" class="form-control"
                               value="{{ old('part_audio_url', $group->part_audio_url) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Part Image URL</label>
                    <input type="text" name="part_image_url" class="form-control"
                           value="{{ old('part_image_url', $group->part_image_url) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Grouped Option IDs</label>
                    <select name="question_options_group_ids[]" id="options_select"
                            class="form-select" multiple size="6">
                        @foreach($options as $opt)
                            <option value="{{ $opt->id }}"
                                {{ in_array($opt->id, old('question_options_group_ids', $selectedIds)) ? 'selected' : '' }}>
                                #{{ $opt->id }} [{{ $opt->question_type }}] — {{ Str::limit($opt->actual_question ?: $opt->option_text, 70) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Hold Ctrl/Cmd to multi-select.</div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
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
    if (!qId) return;
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
