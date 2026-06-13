{{-- Shared form for Question Bank. Expects $question (nullable) and $subTypes --}}
@php $q = $question ?? null; @endphp

<div class="row mb-3">
    <div class="col-md-8">
        <label class="form-label fw-semibold">Sub-type <span class="text-danger">*</span></label>
        <select name="pte_sub_type_id" class="form-select" required>
            <option value="">— Select Sub-type —</option>
            @foreach($subTypes as $st)
                <option value="{{ $st->id }}"
                    {{ old('pte_sub_type_id', $q?->pte_sub_type_id) == $st->id ? 'selected' : '' }}>
                    {{ $st->section?->name }} — {{ $st->name }} ({{ $st->tag }})
                </option>
            @endforeach
        </select>
        @if($q)
            <div class="form-text">Granular ID: <code>{{ $q->question_granular_id }}</code> (auto-generated)</div>
        @else
            <div class="form-text">Granular ID will be generated like <code>RA0001</code> from the sub-type tag.</div>
        @endif
    </div>
    <div class="col-md-2">
        <label class="form-label fw-semibold">Marks</label>
        <input type="number" name="marks" class="form-control"
               value="{{ old('marks', $q?->marks ?? 1) }}" min="0" max="255">
    </div>
    <div class="col-md-2">
        <label class="form-label fw-semibold">Difficulty</label>
        <select name="difficulty" class="form-select">
            @foreach(['easy','medium','hard'] as $d)
                <option value="{{ $d }}" {{ old('difficulty', $q?->difficulty ?? 'medium') === $d ? 'selected' : '' }}>{{ ucfirst($d) }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Question Text / Passage</label>
    <textarea name="question_text" class="form-control" rows="5"
              placeholder="Main prompt, passage, or image topic.">{{ old('question_text', $q?->question_text) }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Audio Transcript</label>
    <textarea name="audio_transcript" class="form-control" rows="3">{{ old('audio_transcript', $q?->audio_transcript) }}</textarea>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Audio URL</label>
        <input type="text" name="audio_url" class="form-control"
               value="{{ old('audio_url', $q?->audio_url) }}" placeholder="data/audio/file.mp3">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Image URL</label>
        <input type="text" name="image_url" class="form-control"
               value="{{ old('image_url', $q?->image_url) }}" placeholder="data/image/file.png">
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Image Alt Text</label>
    <input type="text" name="image_alt_text" class="form-control"
           value="{{ old('image_alt_text', $q?->image_alt_text) }}">
</div>

<div class="row mb-3">
    <div class="col-md-3">
        <label class="form-label fw-semibold">Prep Time (sec)</label>
        <input type="number" name="preparation_time_sec" class="form-control"
               value="{{ old('preparation_time_sec', $q?->preparation_time_sec) }}" min="0">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Answer Time (sec)</label>
        <input type="number" name="answer_time_sec" class="form-control"
               value="{{ old('answer_time_sec', $q?->answer_time_sec) }}" min="0">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Min Words</label>
        <input type="number" name="min_word_count" class="form-control"
               value="{{ old('min_word_count', $q?->min_word_count) }}" min="0">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Max Words</label>
        <input type="number" name="max_word_count" class="form-control"
               value="{{ old('max_word_count', $q?->max_word_count) }}" min="0">
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Correct Answer
        <span class="text-muted small">(Write from Dictation, Answer Short Question)</span>
    </label>
    <input type="text" name="correct_ans" class="form-control"
           value="{{ old('correct_ans', $q?->correct_ans) }}">
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Answer Explanation</label>
    <textarea name="correct_ans_explanation" class="form-control" rows="2">{{ old('correct_ans_explanation', $q?->correct_ans_explanation) }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Source Reference</label>
    <input type="text" name="source_reference" class="form-control"
           value="{{ old('source_reference', $q?->source_reference) }}"
           placeholder="e.g. Official Guide 2024 p.42">
</div>

<div class="mb-4">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="is_active" id="q_is_active" value="1"
               {{ old('is_active', $q?->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="q_is_active">Active</label>
    </div>
</div>

@if($q && ($q->options->isNotEmpty() || $q->blanks->isNotEmpty() || $q->segments->isNotEmpty() || $q->highlightWords->isNotEmpty()))
    <div class="alert alert-info">
        <strong>Existing satellite data:</strong>
        Options: {{ $q->options->count() }} ·
        Blanks: {{ $q->blanks->count() }} ·
        Segments: {{ $q->segments->count() }} ·
        Highlight words: {{ $q->highlightWords->count() }}
        <div class="form-text mt-1">Editing satellites (options / blanks / segments / words) is handled by the dedicated per-type editors which will be wired up later.</div>
    </div>
@endif
