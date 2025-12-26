@extends('layouts.app')

@section('title', 'Create Module')

@section('content')
<div class="container-fluid">
    <h4>Create Module</h4>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.modules.store') }}">
                @csrf

                <div class="form-group">
                    <label>Exam</label>
                    <select name="exam_id" class="form-control" required>
                        <option value="">Select Exam</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Module Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Duration (minutes)</label>
                    <input type="number" name="duration_minutes" class="form-control" required>
                </div>

                <button class="btn btn-success">Create</button>
                <a href="{{ route('admin.modules.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
