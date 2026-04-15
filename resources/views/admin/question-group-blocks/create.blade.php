@extends('layouts.app')
@section('title', 'Create Question Group Block')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm" style="max-width:780px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create Question Group Block</h5>
            <a href="{{ route('admin.question-group-blocks.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.question-group-blocks.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Question Group <span class="text-danger">*</span></label>
                    <select name="question_group_id" id="group_id" class="form-select" required>
                        <option value="">— Select Group —</option>
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}"
                                {{ (old('question_group_id', $preselectedGroupId) == $g->id) ? 'selected' : '' }}>
                                Group #{{ $g->id }} — Q#{{ $g->question_id }}
                                ({{ $g->question?->module?->name ?? '?' }}) Part {{ $g->part_number }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Blocks are sub-sections inside a group, each with its own instruction.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Instruction Text <span class="text-danger">*</span></label>
                    <textarea name="instruction_text" class="form-control" rows="3" required
                              placeholder="e.g. Questions 1–5: Choose ONE letter A–D.">{{ old('instruction_text') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Question Option IDs (in this block)</label>
                    <div class="form-text mb-2">Select the options that appear under this block's instruction.
                        Options load after you choose a group above.</div>
                    <select name="question_option_ids[]" id="options_select"
                            class="form-select" multiple size="8">
                        @foreach($options as $opt)
                            <option value="{{ $opt->id }}"
                                {{ in_array($opt->id, old('question_option_ids', [])) ? 'selected' : '' }}>
                                #{{ $opt->id }} [{{ $opt->question_type }}] — {{ Str::limit($opt->actual_question ?: $opt->option_text, 70) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Hold Ctrl/Cmd to select multiple. Order matters for display.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control"
                           value="{{ old('sort_order', 0) }}" min="0" style="max-width:140px">
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-success">Create Block</button>
                    <a href="{{ route('admin.question-group-blocks.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('group_id').addEventListener('change', function () {
    const gId = this.value;
    const sel = document.getElementById('options_select');
    sel.innerHTML = '<option disabled>Loading…</option>';

    if (!gId) { sel.innerHTML = ''; return; }

    fetch('{{ route("admin.ajax.options-by-group", ":id") }}'.replace(':id', gId))
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '';
            data.forEach(o => {
                const label = `#${o.id} [${o.question_type}] — ${(o.actual_question || o.option_text || '').substring(0, 70)}`;
                sel.innerHTML += `<option value="${o.id}">${label}</option>`;
            });
        });
});

// Auto-load options if group pre-selected
const preselected = document.getElementById('group_id').value;
if (preselected) document.getElementById('group_id').dispatchEvent(new Event('change'));
</script>
@endsection
