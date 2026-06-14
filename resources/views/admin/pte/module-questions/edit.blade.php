@extends('layouts.app')
@section('title', 'Edit PTE Module Question')

@php
    use App\Models\Pte\PteQuestionGranular;

    // Questions available in the current module's section, grouped by sub-type.
    $availableQuestions = PteQuestionGranular::with('subType:id,name,tag,display_order')
        ->whereHas('subType', fn ($q) => $q->where('pte_section_id', $mapping->pteModule?->pte_section_id))
        ->where('is_active', true)
        ->orderBy('pte_sub_type_id')
        ->orderBy('id')
        ->get()
        ->groupBy(fn ($q) => ($q->subType?->tag ?? '—') . ' · ' . ($q->subType?->name ?? ''));
@endphp

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
                    <div class="form-text">Changing the module will not refilter the question list on this page.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Question <span class="text-danger">*</span></label>
                    <select name="pte_question_granular_id" class="form-select" required>
                        <option value="">— Select Question —</option>
                        @foreach($availableQuestions as $groupLabel => $items)
                            <optgroup label="{{ $groupLabel }}">
                                @foreach($items as $q)
                                    <option value="{{ $q->id }}"
                                        {{ old('pte_question_granular_id', $mapping->pte_question_granular_id) == $q->id ? 'selected' : '' }}>
                                        {{ $q->question_granular_id }} — {{ \Illuminate\Support\Str::limit(strip_tags((string) $q->question_text), 80) }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <div class="form-text">List is filtered to the current mapping's section.</div>
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
