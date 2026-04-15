@extends('layouts.app')
@section('title', 'Modules')

@section('content')
<div class="container-fluid mt-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Modules</h5>
            <a href="{{ route('admin.modules.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Module
            </a>
        </div>

        <div class="card-body">
            <table id="modulesTable" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Exam</th>
                        <th>Name</th>
                        <th>Module Type</th>
                        <th>Type</th>
                        <th>Duration (min)</th>
                        <th>Price BDT</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    const table = $('#modulesTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("admin.modules.index") }}',
        columns: [
            { data: 'id',               name: 'id' },
            { data: 'exam_name',        name: 'exam.name', searchable: true },
            { data: 'name',             name: 'name' },
            { data: 'module_type',      name: 'module_type' },
            { data: 'type_badge',       name: 'type',            orderable: false },
            { data: 'duration_minutes', name: 'duration_minutes' },
            { data: 'price_in_bdt',     name: 'price_in_bdt' },
            { data: 'action',           name: 'action',          orderable: false, searchable: false },
        ],
        pageLength: 25,
        order: [[0, 'desc']],
    });

    $('#modulesTable').on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this module?')) return;
        $.ajax({
            url: '{{ route("admin.modules.index") }}/' + id,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (r) {
                if (r.status === 'success') { table.ajax.reload(null, false); }
                else alert(r.message);
            },
            error: function (xhr) { alert('Error: ' + xhr.responseText); }
        });
    });
});
</script>
@endsection
