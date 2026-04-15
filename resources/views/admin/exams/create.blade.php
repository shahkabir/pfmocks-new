@extends('layouts.app')
@section('title', 'Create Exam')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm" style="max-width:640px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create Exam</h5>
            <a href="{{ route('admin.exams.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.exams.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Exam Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                           placeholder="e.g. Bangladesh Bank AD" required>
                    <div class="form-text">Full display name shown to students.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tag (slug) <span class="text-danger">*</span></label>
                    <input type="text" name="tag" id="tag" class="form-control" value="{{ old('tag') }}"
                           placeholder="e.g. bangladesh_bank_ad" pattern="[a-z0-9_]+"
                           title="Lowercase letters, digits and underscores only" required>
                    <div class="form-text">Unique lowercase slug used in URLs. Auto-generated from name.</div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', '1') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active (visible in menu)</label>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-success">Create Exam</button>
                    <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto-generate tag from name
    document.getElementById('name') && document.querySelector('[name=name]').addEventListener('input', function () {
        const tagField = document.getElementById('tag');
        if (!tagField._touched) {
            tagField.value = this.value.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
        }
    });
    document.getElementById('tag').addEventListener('input', function () {
        this._touched = this.value !== '';
    });
</script>
@endsection
