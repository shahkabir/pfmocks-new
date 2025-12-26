@extends('layouts.app')

@section('title', 'Edit Module')

@section('content')
<div class="container-fluid">
    <h4>Edit Module</h4>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.modules.update', $module) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Exam</label>
                    <select name="exam_id" class="form-control" required>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}"
                                {{ $module->exam_id == $exam->id ? 'selected' : '' }}>
                                {{ $exam->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Module Name</label>
                    <input type="text" name="name"
                           value="{{ $module->name }}"
                           class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Duration (minutes)</label>
                    <input type="number" name="duration_minutes"
                           value="{{ $module->duration_minutes }}"
                           class="form-control" required>
                </div>

                <button class="btn btn-primary">Update</button>
                <a href="{{ route('admin.modules.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
