@extends('layouts.app')
@section('title', 'PTE Module ↔ Section')

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
            <h5 class="mb-0">PTE Module ↔ Section mappings</h5>
            <a href="{{ route('admin.pte.modules.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Mapping
            </a>
        </div>

        <div class="card-body">
            <table id="pteModulesTable" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Module</th>
                        <th>PTE Section</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(function () {
    const table = $('#pteModulesTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("admin.pte.modules.index") }}',
        columns: [
            { data: 'id',           name: 'id' },
            { data: 'module_name',  name: 'module.name', orderable: false },
            { data: 'section_name', name: 'section.name', orderable: false },
            { data: 'created_at',   name: 'created_at' },
            { data: 'action',       name: 'action', orderable: false, searchable: false },
        ],
        pageLength: 25,
        order: [[0, 'desc']],
    });

    $('#pteModulesTable').on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this mapping? Linked questions will be detached.')) return;
        $.ajax({
            url: '{{ url("admin/pte/modules") }}/' + id,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: r => r.status === 'success' ? table.ajax.reload(null, false) : alert(r.message),
            error:   x => alert('Error: ' + x.responseText),
        });
    });
});
</script>
@endsection
