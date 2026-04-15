@extends('layouts.app')
@section('title', 'Edit Exam')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm" style="max-width:640px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Exam — <span class="text-muted fw-normal">{{ $exam->name }}</span></h5>
            <a href="{{ route('admin.exams.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.exams.update', $exam->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Exam Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $exam->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tag (slug) <span class="text-danger">*</span></label>
                    <input type="text" name="tag" class="form-control"
                           value="{{ old('tag', $exam->tag) }}"
                           pattern="[a-z0-9_]+" required>
                    <div class="form-text">Lowercase letters, digits and underscores only.</div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $exam->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
