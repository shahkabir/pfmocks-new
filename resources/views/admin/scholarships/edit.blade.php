@extends('layouts.app')

@section('title', 'Edit Scholarship')

@section('content')
<div class="container-fluid mt-3">
    <div class="card shadow-sm" style="max-width:980px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-mortarboard-fill me-2"></i>Edit Scholarship #{{ $scholarship->id }}</h5>
            <a href="{{ route('admin.scholarships.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.scholarships.update', $scholarship->id) }}">
                @csrf
                @method('PUT')
                @include('admin.scholarships._form')
            </form>
        </div>
    </div>
</div>
@endsection
