@extends('layouts.app')
@section('title', 'Edit PTE Module Mapping')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm" style="max-width:640px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit PTE Module Mapping #{{ $pteModule->id }}</h5>
            <a href="{{ route('admin.pte.modules.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.pte.modules.update', $pteModule->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Module <span class="text-danger">*</span></label>
                    <select name="module_id" class="form-select" required>
                        @foreach($modules as $m)
                            <option value="{{ $m->id }}"
                                {{ old('module_id', $pteModule->module_id) == $m->id ? 'selected' : '' }}>
                                {{ $m->name }} ({{ $m->module_type }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">PTE Section <span class="text-danger">*</span></label>
                    <select name="pte_section_id" class="form-select" required>
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}"
                                {{ old('pte_section_id', $pteModule->pte_section_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->name }} ({{ $s->tag }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('admin.pte.modules.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
