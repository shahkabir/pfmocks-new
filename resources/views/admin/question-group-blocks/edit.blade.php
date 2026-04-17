@extends('layouts.app')
@section('title', 'Edit Question Group Block')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm" style="max-width:780px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Block #{{ $block->id }}</h5>
            <a href="{{ route('admin.question-group-blocks.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.question-group-blocks.update', $block->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Question Group <span class="text-danger">*</span></label>
                    <select name="question_group_id" id="group_id" class="form-select" required>
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}"
                                {{ old('question_group_id', $block->question_group_id) == $g->id ? 'selected' : '' }}>
                                Group #{{ $g->id }} — Q#{{ $g->question_id }}
                                ({{ $g->question?->module?->name ?? '?' }}) Part {{ $g->part_number }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Instruction Text <span class="text-danger">*</span></label>
                    <textarea name="instruction_text" id="instruction_text" class="form-control">{!! old('instruction_text', $block->instruction_text) !!}</textarea>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-semibold mb-0">Question Option IDs (in this block)</label>
                        <div class="form-check form-check-inline mb-0">
                            <input class="form-check-input" type="checkbox" id="select_all_options">
                            <label class="form-check-label small fw-semibold" for="select_all_options">Select All</label>
                        </div>
                    </div>
                    <select name="question_option_ids[]" id="options_select"
                            class="form-select" multiple size="8">
                        @foreach($options as $opt)
                            <option value="{{ $opt->id }}"
                                {{ in_array($opt->id, old('question_option_ids', $selectedIds)) ? 'selected' : '' }}>
                                #{{ $opt->id }} [{{ $opt->question_type }}] — {{ Str::limit($opt->actual_question ?: $opt->option_text, 70) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Hold Ctrl/Cmd to select multiple, or use Select All above.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control"
                           value="{{ old('sort_order', $block->sort_order) }}" min="0" style="max-width:140px">
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('admin.question-group-blocks.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const optionsSel   = document.getElementById('options_select');
const selectAllChk = document.getElementById('select_all_options');

document.getElementById('group_id').addEventListener('change', function () {
    const gId = this.value;
    if (!gId) return;
    fetch('{{ route("admin.ajax.options-by-group", ":id") }}'.replace(':id', gId))
        .then(r => r.json())
        .then(data => {
            const prevSelected = Array.from(optionsSel.selectedOptions).map(o => parseInt(o.value));
            optionsSel.innerHTML = '';
            data.forEach(o => {
                const label = `#${o.id} [${o.question_type}] — ${(o.actual_question || o.option_text || '').substring(0, 70)}`;
                const selected = prevSelected.includes(o.id) ? 'selected' : '';
                optionsSel.innerHTML += `<option value="${o.id}" ${selected}>${label}</option>`;
            });
            syncSelectAllState();
        });
});

selectAllChk.addEventListener('change', function () {
    Array.from(optionsSel.options).forEach(o => o.selected = this.checked);
});

optionsSel.addEventListener('change', syncSelectAllState);

function syncSelectAllState() {
    const opts = Array.from(optionsSel.options);
    selectAllChk.indeterminate = opts.some(o => o.selected) && !opts.every(o => o.selected);
    selectAllChk.checked = opts.length > 0 && opts.every(o => o.selected);
}

syncSelectAllState();
</script>
@endsection

@push('page_scripts')
<script>
$(function () {
    $('#instruction_text').summernote({
        height: 200,
        toolbar: [
            ['font',   ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['para',   ['ul', 'ol', 'paragraph']],
            ['table',  ['table']],
            ['insert', ['link']],
            ['view',   ['codeview', 'fullscreen']],
        ],
    });
});
</script>
@endpush
