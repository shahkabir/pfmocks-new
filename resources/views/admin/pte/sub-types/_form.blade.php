{{-- Shared form fields used by create/edit. Expects $subType (nullable), $sections --}}
@php $st = $subType ?? null; @endphp

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Section <span class="text-danger">*</span></label>
        <select name="pte_section_id" class="form-select" required>
            <option value="">— Select Section —</option>
            @foreach($sections as $s)
                <option value="{{ $s->id }}"
                    {{ old('pte_section_id', $st?->pte_section_id) == $s->id ? 'selected' : '' }}>
                    {{ $s->name }} ({{ $s->tag }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Tag <span class="text-danger">*</span></label>
        <input type="text" name="tag" class="form-control text-uppercase"
               value="{{ old('tag', $st?->tag) }}" maxlength="5" required>
        <div class="form-text">A-Z / 0-9, ≤5 chars.</div>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Display Order</label>
        <input type="number" name="display_order" class="form-control"
               value="{{ old('display_order', $st?->display_order) }}" min="0" max="255">
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control"
           value="{{ old('name', $st?->name) }}" required>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Response Type <span class="text-danger">*</span></label>
        <select name="response_type" class="form-select" required>
            <option value="">— Select —</option>
            @foreach(['audio_record','text_write','single_choice','multi_choice','fill_blank','reorder','highlight_words','select_option'] as $t)
                <option value="{{ $t }}" {{ old('response_type', $st?->response_type) === $t ? 'selected' : '' }}>
                    {{ ucwords(str_replace('_', ' ', $t)) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Stimulus Type <span class="text-danger">*</span></label>
        <select name="stimulus_type" class="form-select" required>
            <option value="">— Select —</option>
            @foreach(['text','audio','image','audio_image','none'] as $t)
                <option value="{{ $t }}" {{ old('stimulus_type', $st?->stimulus_type) === $t ? 'selected' : '' }}>
                    {{ ucwords(str_replace('_', ' ', $t)) }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-3">
        <label class="form-label fw-semibold">Prep Time (sec)</label>
        <input type="number" name="preparation_time_sec_default" class="form-control"
               value="{{ old('preparation_time_sec_default', $st?->preparation_time_sec_default ?? 0) }}" min="0">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Answer Time (sec)</label>
        <input type="number" name="answer_time_sec_default" class="form-control"
               value="{{ old('answer_time_sec_default', $st?->answer_time_sec_default) }}" min="0">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Marks (default)</label>
        <input type="number" name="marks_default" class="form-control"
               value="{{ old('marks_default', $st?->marks_default) }}" min="0" max="255">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Typical Count</label>
        <input type="number" name="typical_count_in_exam" class="form-control"
               value="{{ old('typical_count_in_exam', $st?->typical_count_in_exam) }}" min="0" max="255">
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Instructions</label>
    <textarea name="instructions" class="form-control" rows="3">{{ old('instructions', $st?->instructions) }}</textarea>
</div>

<div class="mb-4">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
               {{ old('is_active', $st?->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Active</label>
    </div>
</div>
