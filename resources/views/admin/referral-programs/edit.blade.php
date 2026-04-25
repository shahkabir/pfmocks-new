@extends('layouts.app')

@section('title', 'Edit Referral Program')

@section('content')
<div class="container-fluid mt-3">
    <div class="card shadow-sm" style="max-width:880px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-gift-fill me-2"></i>Edit Program #{{ $program->id }}</h5>
            <a href="{{ route('admin.referral-programs.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.referral-programs.update', $program->id) }}">
                @csrf
                @method('PUT')
                @include('admin.referral-programs._form')
            </form>
        </div>
    </div>
</div>
@endsection
