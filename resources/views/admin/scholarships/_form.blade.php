@php
    /** @var \App\Models\Scholarship\Scholarship|null $scholarship */
    $scholarship = $scholarship ?? null;
    $isEdit      = $scholarship !== null;
@endphp

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="row mb-3">
    <div class="col-md-9">
        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control"
               value="{{ old('title', $scholarship?->title ?? '') }}"
               required maxlength="220" placeholder="e.g. Chevening Scholarship 2027">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold d-block">Active</label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" role="switch"
                   id="is_active" name="is_active" value="1"
                   {{ old('is_active', $scholarship?->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Visible to students</label>
        </div>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch"
                   id="is_featured" name="is_featured" value="1"
                   {{ old('is_featured', $scholarship?->is_featured ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_featured">Featured</label>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
        <select name="type" class="form-select" required>
            <option value="">— Select —</option>
            @foreach(\App\Constants\ScholarshipConstants::TYPES as $val => $label)
                <option value="{{ $val }}"
                    {{ old('type', $scholarship?->type ?? '') === $val ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Country</label>
        <select name="country_code" class="form-select">
            <option value="">— Select country —</option>
            @foreach($countries as $code => $name)
                <option value="{{ $code }}"
                    {{ old('country_code', $scholarship?->country_code ?? '') === $code ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label fw-semibold">Deadline</label>
        <input type="date" name="deadline" class="form-control"
               value="{{ old('deadline', $scholarship?->deadline?->format('Y-m-d') ?? '') }}">
    </div>
    <div class="col-md-2">
        <label class="form-label fw-semibold">Program Level</label>
        <select name="program_level" class="form-select">
            <option value="">—</option>
            @foreach(\App\Constants\ScholarshipConstants::PROGRAM_LEVELS as $val => $label)
                <option value="{{ $val }}"
                    {{ old('program_level', $scholarship?->program_level ?? '') === $val ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label fw-semibold">Funding Type <span class="text-danger">*</span></label>
        <select name="funding_type" class="form-select" required>
            @foreach(\App\Constants\ScholarshipConstants::FUNDING_TYPES as $val => $label)
                <option value="{{ $val }}"
                    {{ old('funding_type', $scholarship?->funding_type ?? 'fully_funded') === $val ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Official Link 1</label>
        <input type="url" name="official_link_1" class="form-control"
               value="{{ old('official_link_1', $scholarship?->official_link_1 ?? '') }}"
               maxlength="500" placeholder="https://...">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Official Link 2</label>
        <input type="url" name="official_link_2" class="form-control"
               value="{{ old('official_link_2', $scholarship?->official_link_2 ?? '') }}"
               maxlength="500" placeholder="https://...">
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Short Description</label>
    <textarea name="short_description" class="form-control" rows="2" maxlength="1000"
              placeholder="One or two lines shown on the card.">{{ old('short_description', $scholarship?->short_description ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Eligibility Criteria</label>
    <textarea name="eligibility_criteria" id="eligibility_criteria" class="form-control wysiwyg">{!! old('eligibility_criteria', $scholarship?->eligibility_criteria ?? '') !!}</textarea>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Benefits</label>
    <textarea name="benefits" id="benefits" class="form-control wysiwyg">{!! old('benefits', $scholarship?->benefits ?? '') !!}</textarea>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Required Documents</label>
    <textarea name="required_documents" id="required_documents" class="form-control wysiwyg">{!! old('required_documents', $scholarship?->required_documents ?? '') !!}</textarea>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check2-circle me-1"></i>{{ $isEdit ? 'Save Changes' : 'Create Scholarship' }}
    </button>
    <a href="{{ route('admin.scholarships.index') }}" class="btn btn-secondary">Cancel</a>
</div>

@push('page_scripts')
<script>
$(function () {
    const toolbar = [
        ['style',  ['style']],
        ['font',   ['bold', 'italic', 'underline', 'clear']],
        ['para',   ['ul', 'ol', 'paragraph']],
        ['insert', ['link']],
        ['view',   ['codeview']],
    ];
    $('#eligibility_criteria, #benefits, #required_documents').summernote({
        height: 180,
        toolbar: toolbar,
    });
});
</script>
@endpush
