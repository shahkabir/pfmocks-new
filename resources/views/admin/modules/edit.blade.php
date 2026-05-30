@extends('layouts.app')
@section('title', 'Edit Module')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm" style="max-width:720px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Module — <span class="text-muted fw-normal">{{ $module->name }}</span></h5>
            <a href="{{ route('admin.modules.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.modules.update', $module->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Exam <span class="text-danger">*</span></label>
                    <select name="exam_id" class="form-select" required>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}"
                                {{ old('exam_id', $module->exam_id) == $exam->id ? 'selected' : '' }}>
                                {{ $exam->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Module Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $module->name) }}" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Module Type <span class="text-danger">*</span></label>
                        <select name="module_type" class="form-select" required>
                            @foreach(['reading','writing','listening','speaking','general_mcq'] as $mt)
                                <option value="{{ $mt }}"
                                    {{ old('module_type', $module->module_type) === $mt ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $mt)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Access Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="free" {{ old('type', $module->type) === 'free' ? 'selected' : '' }}>Free</option>
                            <option value="paid" {{ old('type', $module->type) === 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Duration (minutes) <span class="text-danger">*</span></label>
                        <input type="number" name="duration_minutes" class="form-control"
                               value="{{ old('duration_minutes', $module->duration_minutes) }}" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Price (BDT)</label>
                        <input type="number" name="price_in_bdt" class="form-control"
                               value="{{ old('price_in_bdt', $module->price_in_bdt) }}" min="0" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Price (USD)</label>
                        <input type="number" name="price_in_usd" class="form-control"
                               value="{{ old('price_in_usd', $module->price_in_usd) }}" min="0" step="0.01">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Module Information</label>
                    <textarea name="module_information" class="form-control" rows="4"
                              placeholder="Description shown on the business website (optional).">{{ old('module_information', $module->module_information) }}</textarea>
                    <div class="form-text">Public-facing description exposed via the business website API.</div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('admin.modules.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
