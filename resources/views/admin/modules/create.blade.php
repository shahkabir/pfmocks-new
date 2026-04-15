@extends('layouts.app')
@section('title', 'Create Module')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm" style="max-width:720px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create Module</h5>
            <a href="{{ route('admin.modules.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.modules.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Exam <span class="text-danger">*</span></label>
                    <select name="exam_id" class="form-select" required>
                        <option value="">— Select Exam —</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}" {{ old('exam_id') == $exam->id ? 'selected' : '' }}>
                                {{ $exam->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Module Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                           placeholder="e.g. BB AD Full Mock Test 1" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Module Type <span class="text-danger">*</span></label>
                        <select name="module_type" class="form-select" required>
                            <option value="">— Select —</option>
                            @foreach(['reading','writing','listening','speaking','general_mcq'] as $mt)
                                <option value="{{ $mt }}" {{ old('module_type') === $mt ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $mt)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Access Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="free"  {{ old('type') === 'free'  ? 'selected' : '' }}>Free</option>
                            <option value="paid"  {{ old('type') === 'paid'  ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Duration (minutes) <span class="text-danger">*</span></label>
                        <input type="number" name="duration_minutes" class="form-control"
                               value="{{ old('duration_minutes', 60) }}" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Price (BDT)</label>
                        <input type="number" name="price_in_bdt" class="form-control"
                               value="{{ old('price_in_bdt', 0) }}" min="0" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Price (USD)</label>
                        <input type="number" name="price_in_usd" class="form-control"
                               value="{{ old('price_in_usd', 0) }}" min="0" step="0.01">
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-success">Create Module</button>
                    <a href="{{ route('admin.modules.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
