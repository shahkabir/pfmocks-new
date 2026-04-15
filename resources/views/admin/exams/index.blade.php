@extends('layouts.app')
@section('title', 'Exams')

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
            <h5 class="mb-0">Exams</h5>
            <a href="{{ route('admin.exams.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Exam
            </a>
        </div>

        <div class="card-body">
            <table id="examsTable" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Tag</th>
                        <th>Active</th>
                        <th>Modules</th>
                        <th>Created At</th>
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
    const table = $('#examsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("admin.exams.index") }}',
        columns: [
            { data: 'id',            name: 'id' },
            { data: 'name',          name: 'name' },
            { data: 'tag',           name: 'tag' },
            { data: 'is_active',     name: 'is_active',     orderable: false },
            { data: 'modules_count', name: 'modules_count', orderable: false, searchable: false },
            { data: 'created_at',    name: 'created_at' },
            { data: 'action',        name: 'action',        orderable: false, searchable: false },
        ],
        pageLength: 25,
        order: [[0, 'desc']],
    });

    $('#examsTable').on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this exam and all its modules?')) return;
        $.ajax({
            url: '{{ route("admin.exams.index") }}/' + id,
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
