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

            <form method="POST" action="{{ route('admin.question-groups.update', $group->id) }}"
                  enctype="multipart/form-data">
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
                        <label class="form-label fw-semibold">
                            Part Audio
                            <span class="text-muted fw-normal small">(MP3 or WAV — no size limit)</span>
                        </label>

                        {{-- Current audio --}}
                        @if($group->part_audio_url)
                            <div class="mb-2 p-2 bg-light rounded border">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="small text-muted">
                                        <i class="bi bi-music-note me-1"></i>Current: <code>{{ basename($group->part_audio_url) }}</code>
                                    </span>
                                    <span class="badge bg-secondary small">Upload a new file to replace</span>
                                </div>
                                <audio controls class="w-100" style="height:36px"
                                       src="{{ asset($group->part_audio_url) }}"></audio>
                            </div>
                        @endif

                        <input type="file" name="part_audio_file" id="part_audio_file"
                               class="form-control @error('part_audio_file') is-invalid @enderror"
                               accept=".mp3,.wav,audio/mpeg,audio/wav">
                        @error('part_audio_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="audio_preview" class="mt-2" style="display:none">
                            <div class="small text-success mb-1"><i class="bi bi-check-circle me-1"></i>New file selected:</div>
                            <audio controls class="w-100" style="height:36px"></audio>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Part Image
                        <span class="text-muted fw-normal small">(JPG, PNG or GIF — max {{ number_format(config('upload_image.max_size_kb') / 1024, 0) }} MB)</span>
                    </label>

                    {{-- Current image --}}
                    @if($group->part_image_url)
                        <div class="mb-2 p-2 bg-light rounded border">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="small text-muted">
                                    <i class="bi bi-image me-1"></i>Current: <code>{{ basename($group->part_image_url) }}</code>
                                </span>
                                <span class="badge bg-secondary small">Upload a new file to replace</span>
                            </div>
                            <img src="{{ asset($group->part_image_url) }}" alt="Current image"
                                 class="img-thumbnail" style="max-height:120px">
                        </div>
                    @endif

                    <input type="file" name="part_image_file" id="part_image_file"
                           class="form-control @error('part_image_file') is-invalid @enderror"
                           accept=".jpg,.jpeg,.png,.gif,image/jpeg,image/png,image/gif">
                    @error('part_image_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="image_preview" class="mt-2" style="display:none">
                        <div class="small text-success mb-1"><i class="bi bi-check-circle me-1"></i>New file selected:</div>
                        <img src="" alt="Preview" class="img-thumbnail" style="max-height:160px">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-semibold mb-0">Grouped Option IDs</label>
                        <div class="form-check form-check-inline mb-0">
                            <input class="form-check-input" type="checkbox" id="select_all_options">
                            <label class="form-check-label small fw-semibold" for="select_all_options">Select All</label>
                        </div>
                    </div>
                    <select name="question_options_group_ids[]" id="options_select"
                            class="form-select" multiple size="6">
                        @foreach($options as $opt)
                            <option value="{{ $opt->id }}"
                                {{ in_array($opt->id, old('question_options_group_ids', $selectedIds)) ? 'selected' : '' }}>
                                #{{ $opt->id }} [{{ $opt->question_type }}] — {{ Str::limit($opt->actual_question ?: $opt->option_text, 70) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Hold Ctrl/Cmd to select multiple, or use Select All above.</div>
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
// ── Live file previews ────────────────────────────────────────────────────────
document.getElementById('part_audio_file').addEventListener('change', function () {
    const wrap  = document.getElementById('audio_preview');
    const audio = wrap.querySelector('audio');
    if (this.files[0]) {
        audio.src = URL.createObjectURL(this.files[0]);
        wrap.style.display = '';
    } else {
        wrap.style.display = 'none';
        audio.src = '';
    }
});

document.getElementById('part_image_file').addEventListener('change', function () {
    const wrap = document.getElementById('image_preview');
    const img  = wrap.querySelector('img');
    if (this.files[0]) {
        img.src = URL.createObjectURL(this.files[0]);
        wrap.style.display = '';
    } else {
        wrap.style.display = 'none';
        img.src = '';
    }
});

// ── Options multi-select ──────────────────────────────────────────────────────
const optionsSel   = document.getElementById('options_select');
const selectAllChk = document.getElementById('select_all_options');

document.getElementById('question_id').addEventListener('change', function () {
    const qId = this.value;
    if (!qId) return;
    fetch('{{ route("admin.ajax.options-by-question", ":id") }}'.replace(':id', qId))
        .then(r => r.json())
        .then(data => {
            optionsSel.innerHTML = '';
            data.forEach(o => {
                const label = `#${o.id} [${o.question_type}] — ${(o.actual_question || o.option_text || '').substring(0, 70)}`;
                optionsSel.innerHTML += `<option value="${o.id}">${label}</option>`;
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
