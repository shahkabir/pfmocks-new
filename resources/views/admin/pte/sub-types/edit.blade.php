@extends('layouts.app')
@section('title', 'Edit PTE Sub-type')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm" style="max-width:860px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Sub-type — <span class="text-muted fw-normal">{{ $subType->name }}</span></h5>
            <a href="{{ route('admin.pte.sub-types.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.pte.sub-types.update', $subType->id) }}">
                @csrf
                @method('PUT')
                @include('admin.pte.sub-types._form', ['subType' => $subType])
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('admin.pte.sub-types.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
