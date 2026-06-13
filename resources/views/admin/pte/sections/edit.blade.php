@extends('layouts.app')
@section('title', 'Edit PTE Section')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm" style="max-width:640px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Section — <span class="text-muted fw-normal">{{ $section->name }}</span></h5>
            <a href="{{ route('admin.pte.sections.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.pte.sections.update', $section->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $section->name) }}" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tag <span class="text-danger">*</span></label>
                        <input type="text" name="tag" class="form-control text-uppercase"
                               value="{{ old('tag', $section->tag) }}" maxlength="5" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Display Order <span class="text-danger">*</span></label>
                        <input type="number" name="display_order" class="form-control"
                               value="{{ old('display_order', $section->display_order) }}" min="0" max="255" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Time Allowed (min)</label>
                        <input type="number" name="time_allowed_minutes" class="form-control"
                               value="{{ old('time_allowed_minutes', $section->time_allowed_minutes) }}" min="0">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $section->description) }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('admin.pte.sections.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
