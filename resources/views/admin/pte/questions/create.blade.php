@extends('layouts.app')
@section('title', 'Create PTE Question')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm" style="max-width:960px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create PTE Question</h5>
            <a href="{{ route('admin.pte.questions.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.pte.questions.store') }}">
                @csrf
                @include('admin.pte.questions._form', ['question' => null])
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Create</button>
                    <a href="{{ route('admin.pte.questions.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
