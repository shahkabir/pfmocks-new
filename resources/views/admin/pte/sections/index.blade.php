@extends('layouts.app')
@section('title', 'PTE Sections')

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
            <h5 class="mb-0">PTE Sections</h5>
            <a href="{{ route('admin.pte.sections.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Section
            </a>
        </div>

        <div class="card-body">
            <table id="pteSectionsTable" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Tag</th>
                        <th>Display Order</th>
                        <th>Time (min)</th>
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
    const table = $('#pteSectionsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("admin.pte.sections.index") }}',
        columns: [
            { data: 'id',                   name: 'id' },
            { data: 'name',                 name: 'name' },
            { data: 'tag',                  name: 'tag' },
            { data: 'display_order',        name: 'display_order' },
            { data: 'time_allowed_minutes', name: 'time_allowed_minutes' },
            { data: 'created_at',           name: 'created_at' },
            { data: 'action',               name: 'action', orderable: false, searchable: false },
        ],
        pageLength: 25,
        order: [[3, 'asc']],
    });

    $('#pteSectionsTable').on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this section? Sub-types under it cannot be deleted.')) return;
        $.ajax({
            url: '{{ url("admin/pte/sections") }}/' + id,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: r => r.status === 'success' ? table.ajax.reload(null, false) : alert(r.message),
            error:   x => alert('Error: ' + x.responseText),
        });
    });
});
</script>
@endsection
