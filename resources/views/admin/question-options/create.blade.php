@extends('layouts.app')
@section('title', 'Create Question Option')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm" style="max-width:900px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create Question Option</h5>
            <a href="{{ route('admin.question-options.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.question-options.store') }}" id="options-form">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Question <span class="text-danger">*</span></label>
                        <select name="question_id" id="question_id" class="form-select" required>
                            <option value="">— Select Question —</option>
                            @foreach($questions as $q)
                                <option value="{{ $q->id }}"
                                    {{ (old('question_id', $preselectedQuestionId) == $q->id) ? 'selected' : '' }}>
                                    Q#{{ $q->id }} — {{ $q->module?->name }} | {{ Str::limit($q->question_header, 50) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Question Type <span class="text-danger">*</span></label>
                        <select name="question_type" id="question_type" class="form-select" required>
                            <option value="">— Select —</option>
                            @foreach(['mcq_single','mcq_multiple','fill_in_blanks','writing','essay','audio','speaking','ielts_speaking','no_question','generic_question','highlighting','reordering'] as $qt)
                                <option value="{{ $qt }}" {{ old('question_type') === $qt ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $qt)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Actual question text (shared across all MCQ options) --}}
                <div class="mb-3 field-actual-question">
                    <label class="form-label fw-semibold">Actual Question Text</label>
                    <textarea name="actual_question" class="form-control" rows="3"
                              placeholder="For fill_in_blanks use [[blank]] as placeholder">{{ old('actual_question') }}</textarea>
                    <div class="form-text field-hint-fill_in_blanks" style="display:none">
                        Use <code>[[blank]]</code> in the text where the input field should appear.
                    </div>
                </div>

                {{-- ═══════════════════════════════════════════════════════
                     MCQ MULTI-OPTION BUILDER (mcq_single / mcq_multiple)
                     Each row → one DB row; actual_question replicated
                ═══════════════════════════════════════════════════════ --}}
                <div class="field-mcq-options" style="display:none">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-semibold mb-0">
                            Options <span class="text-danger">*</span>
                            <small class="text-muted fw-normal ms-1" id="mcq-type-hint"></small>
                        </label>
                        <button type="button" id="add-option-btn" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-plus-circle me-1"></i> Add Option
                        </button>
                    </div>

                    <div class="table-responsive mb-2">
                        <table class="table table-bordered table-sm align-middle" id="mcq-options-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:50px">#</th>
                                    <th>Option Text <span class="text-danger">*</span></th>
                                    <th style="width:100px" class="text-center">Correct?</th>
                                    <th style="width:60px"></th>
                                </tr>
                            </thead>
                            <tbody id="mcq-options-body">
                                {{-- JS adds rows here --}}
                            </tbody>
                        </table>
                    </div>

                    <div class="row mb-3">
                        <div class="col-auto">
                            <label class="form-label fw-semibold">Starting Sort Order</label>
                            <input type="number" name="sort_order_start" class="form-control" style="width:100px"
                                   value="{{ old('sort_order_start', 1) }}" min="0">
                            <div class="form-text">Options will be saved with consecutive sort orders from this value.</div>
                        </div>
                    </div>
                </div>

                {{-- Single option text — non-MCQ types --}}
                <div class="mb-3 field-option-text">
                    <label class="form-label fw-semibold">Option Text</label>
                    <input type="text" name="option_text" class="form-control"
                           value="{{ old('option_text') }}" placeholder="e.g. Option A text">
                </div>

                {{-- Is correct — non-MCQ types (fill_in_blanks etc.) --}}
                <div class="mb-3 field-is-correct">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_correct" id="is_correct" value="1"
                               {{ old('is_correct') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_correct">This is the correct answer</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Correct Answer Explanation</label>
                    <textarea name="correct_answer_explanation" class="form-control" rows="2"
                              placeholder="Optional explanation shown after submission">{{ old('correct_answer_explanation') }}</textarea>
                </div>

                {{-- Media paths --}}
                <div class="mb-3 field-image-path" style="display:none">
                    <label class="form-label fw-semibold">Question Image Path</label>
                    <input type="text" name="question_image_path" class="form-control"
                           value="{{ old('question_image_path') }}" placeholder="/storage/images/...">
                </div>

                <div class="mb-3 field-audio-path" style="display:none">
                    <label class="form-label fw-semibold">Question Audio Path</label>
                    <input type="text" name="question_audio_path" class="form-control"
                           value="{{ old('question_audio_path') }}" placeholder="/storage/audio/...">
                </div>

                {{-- Sort order (non-MCQ only) --}}
                <div class="row mb-3 field-sort-order">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control"
                               value="{{ old('sort_order', 0) }}" min="0">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', '1') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-success">Create Option(s)</button>
                    <a href="{{ route('admin.question-options.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         CSV BULK IMPORT (mcq_single only)
    ═══════════════════════════════════════════════════ --}}
    <div class="card shadow-sm mt-4 csv-import-card" style="display:none">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-file-earmark-arrow-up me-1"></i> CSV Bulk Import
                <span class="badge bg-primary ms-1">mcq_single</span>
            </h5>
            <a href="{{ asset('sample_mcq_import.csv') }}" class="btn btn-sm btn-outline-secondary" download>
                <i class="bi bi-download me-1"></i> Download Sample CSV
            </a>
        </div>
        <div class="card-body">

            @if(session('csv_errors'))
                <div class="alert alert-warning">
                    <strong>Some rows failed:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach(session('csv_errors') as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="alert alert-info py-2 mb-3">
                <strong>Required CSV format (first row = header):</strong><br>
                <code class="d-block mt-1 text-dark bg-light p-2 rounded" style="font-size:0.82rem">
                    "actual_question","number_of_options","correct_option_in_number","correct_answer_explanation","is_active","option1","option2","option3","option4"
                </code>
                <ul class="mb-0 mt-2 small">
                    <li><strong>Wrap every value in double quotes</strong> <code>"..."</code> — required to handle commas inside question/option text.</li>
                    <li><strong>number_of_options</strong> — total number of option columns for that row (min 2). If missing or invalid, the row is skipped.</li>
                    <li><strong>correct_option_in_number</strong> — 1-based position of the correct option. Must be between 1 and number_of_options.</li>
                    <li><strong>is_active</strong> — <code>1</code> / <code>0</code> (or <code>true</code> / <code>false</code>).</li>
                    <li>Option columns must be <strong>option1, option2, … optionN</strong> exactly matching number_of_options. Rows with missing/empty option columns are skipped.</li>
                    <li>Sort order is auto-assigned 1, 2, 3 … per option within each row.</li>
                    <li>Max file size: <strong>{{ number_format(config('csv_import.max_file_size_kb', 10240) / 1024, 0) }} MB</strong></li>
                </ul>
            </div>

            <form method="POST" action="{{ route('admin.question-options.csv-import') }}"
                  enctype="multipart/form-data" id="csv-import-form">
                @csrf

                <div class="row mb-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Question <span class="text-danger">*</span></label>
                        <select name="question_id" id="csv_question_id" class="form-select" required>
                            <option value="">— Select Question —</option>
                            @foreach($questions as $q)
                                <option value="{{ $q->id }}"
                                    {{ old('question_id') == $q->id ? 'selected' : '' }}>
                                    Q#{{ $q->id }} — {{ $q->module?->name }} | {{ Str::limit($q->question_header, 50) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">All imported options will be linked to this question.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">CSV File <span class="text-danger">*</span></label>
                        <input type="file" name="csv_file" id="csv_file" class="form-control"
                               accept=".csv,text/csv" required>
                        <div class="form-text">CSV only — max {{ number_format(config('csv_import.max_file_size_kb', 10240) / 1024, 0) }} MB</div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100" id="csv-submit-btn">
                            <i class="bi bi-upload me-1"></i> Import
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const MCQ_TYPES = ['mcq_single', 'mcq_multiple'];

const typeRules = {
    mcq_single:       { mcqOptions: true,  optionText: false, isCorrect: false, actualQ: true,  image: false, audio: false },
    mcq_multiple:     { mcqOptions: true,  optionText: false, isCorrect: false, actualQ: true,  image: false, audio: false },
    fill_in_blanks:   { mcqOptions: false, optionText: false, isCorrect: true,  actualQ: true,  image: false, audio: false },
    writing:          { mcqOptions: false, optionText: false, isCorrect: false, actualQ: true,  image: false, audio: false },
    essay:            { mcqOptions: false, optionText: false, isCorrect: false, actualQ: true,  image: false, audio: false },
    audio:            { mcqOptions: false, optionText: false, isCorrect: false, actualQ: false, image: false, audio: true  },
    speaking:         { mcqOptions: false, optionText: false, isCorrect: false, actualQ: true,  image: false, audio: true  },
    ielts_speaking:   { mcqOptions: false, optionText: false, isCorrect: false, actualQ: false, image: true,  audio: true  },
    no_question:      { mcqOptions: false, optionText: false, isCorrect: false, actualQ: false, image: false, audio: false },
    generic_question: { mcqOptions: false, optionText: false, isCorrect: false, actualQ: true,  image: false, audio: false },
    highlighting:     { mcqOptions: false, optionText: true,  isCorrect: true,  actualQ: true,  image: false, audio: false },
    reordering:       { mcqOptions: false, optionText: true,  isCorrect: true,  actualQ: true,  image: false, audio: false },
};

function toggle(selector, show) {
    document.querySelectorAll(selector).forEach(el => el.style.display = show ? '' : 'none');
}

function applyTypeRules(type) {
    const r = typeRules[type] || {};
    toggle('.field-mcq-options',    r.mcqOptions);
    toggle('.field-option-text',    r.optionText);
    toggle('.field-is-correct',     r.isCorrect);
    toggle('.field-actual-question',r.actualQ);
    toggle('.field-image-path',     r.image);
    toggle('.field-audio-path',     r.audio);
    toggle('.field-sort-order',     !r.mcqOptions);
    toggle('.csv-import-card',      type === 'mcq_single');
    document.querySelector('.field-hint-fill_in_blanks').style.display = (type === 'fill_in_blanks') ? '' : 'none';

    const hint = document.getElementById('mcq-type-hint');
    if (hint) hint.textContent = type === 'mcq_single' ? '(single correct answer)' : type === 'mcq_multiple' ? '(multiple correct answers allowed)' : '';

    // Ensure at least 2 rows when switching to MCQ
    if (r.mcqOptions && document.getElementById('mcq-options-body').rows.length === 0) {
        addOptionRow();
        addOptionRow();
    }
}

// ── MCQ option row builder ──────────────────────────────────────────────────
let rowIndex = 0;

function addOptionRow(optText = '', isCorrect = false) {
    const tbody = document.getElementById('mcq-options-body');
    const idx   = rowIndex++;
    const tr    = document.createElement('tr');
    tr.dataset.idx = idx;
    tr.innerHTML = `
        <td class="text-center text-muted row-num fw-semibold"></td>
        <td><input type="text" name="options[${idx}][option_text]" class="form-control form-control-sm"
                   value="${escHtml(optText)}" placeholder="Option text" required></td>
        <td class="text-center">
            <input type="checkbox" name="options[${idx}][is_correct]" value="1" class="form-check-input"
                   ${isCorrect ? 'checked' : ''}>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn" title="Remove">
                <i class="bi bi-trash"></i>
            </button>
        </td>`;
    tbody.appendChild(tr);
    refreshRowNumbers();
}

function refreshRowNumbers() {
    document.querySelectorAll('#mcq-options-body tr').forEach((tr, i) => {
        tr.querySelector('.row-num').textContent = i + 1;
    });
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

document.getElementById('add-option-btn').addEventListener('click', () => addOptionRow());

document.getElementById('mcq-options-body').addEventListener('click', function (e) {
    const btn = e.target.closest('.remove-row-btn');
    if (!btn) return;
    const rows = this.querySelectorAll('tr');
    if (rows.length <= 1) { alert('At least one option is required.'); return; }
    btn.closest('tr').remove();
    refreshRowNumbers();
});

// ── mcq_single: enforce only one "correct" checkbox at a time ───────────────
document.getElementById('mcq-options-body').addEventListener('change', function (e) {
    if (!e.target.matches('input[type=checkbox]')) return;
    const type = document.getElementById('question_type').value;
    if (type !== 'mcq_single') return;
    if (e.target.checked) {
        this.querySelectorAll('input[type=checkbox]').forEach(cb => {
            if (cb !== e.target) cb.checked = false;
        });
    }
});

// ── Type change ─────────────────────────────────────────────────────────────
document.getElementById('question_type').addEventListener('change', function () {
    applyTypeRules(this.value);
});

applyTypeRules(document.getElementById('question_type').value);

// ── CSV import: show loading state on submit ─────────────────────────────────
document.getElementById('csv-import-form').addEventListener('submit', function () {
    const btn = document.getElementById('csv-submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Importing…';
});
</script>
@endsection
