@extends('layouts.app')

@section('title', 'Modules')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between mb-3">
        <h4>Modules</h4>
        <a href="{{ route('admin.modules.create') }}" class="btn btn-primary">
            + Add Module
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Exam</th>
                    <th>Name</th>
                    <th>Duration (mins)</th>
                    <th width="180">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($modules as $module)
                    <tr>
                        <td>{{ $module->id }}</td>
                        <td>{{ $module->exam->name }}</td>
                        <td>{{ $module->name }}</td>
                        <td>{{ $module->duration_minutes }}</td>
                        <td>
                            <a href="{{ route('admin.modules.edit', $module) }}"
                               class="btn btn-sm btn-warning">
                                Edit
                            </a>

                            <form action="{{ route('admin.modules.destroy', $module) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this module?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No modules found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $modules->links() }}
        </div>
    </div>

</div>
@endsection