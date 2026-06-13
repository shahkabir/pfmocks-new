@extends('layouts.app')
@section('title', 'Edit PTE Module Question')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm" style="max-width:720px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Module-Question Mapping #{{ $mapping->id }}</h5>
            <a href="{{ route('admin.pte.module-questions.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.pte.module-questions.update', $mapping->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">PTE Module <span class="text-danger">*</span></label>
                    <select name="pte_module_id" class="form-select" required>
                        @foreach($pteModules as $pm)
                            <option value="{{ $pm->id }}"
                                {{ old('pte_module_id', $mapping->pte_module_id) == $pm->id ? 'selected' : '' }}>
                                #{{ $pm->id }} — {{ $pm->module?->name }} → {{ $pm->section?->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Question (Granular ID) <span class="text-danger">*</span></label>
                    <input type="number" name="pte_question_granular_id" class="form-control"
                           value="{{ old('pte_question_granular_id', $mapping->pte_question_granular_id) }}" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Display Order <span class="text-danger">*</span></label>
                        <input type="number" name="display_order" class="form-control"
                               value="{{ old('display_order', $mapping->display_order) }}" min="0" required>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                                   {{ old('is_active', $mapping->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('admin.pte.module-questions.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
