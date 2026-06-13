@extends('layouts.app')
@section('title', 'PTE Question Sub-types')

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
            <h5 class="mb-0">PTE Question Sub-types</h5>
            <a href="{{ route('admin.pte.sub-types.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Sub-type
            </a>
        </div>

        <div class="card-body">
            <table id="pteSubTypesTable" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Section</th>
                        <th>Name</th>
                        <th>Tag</th>
                        <th>Response Type</th>
                        <th>Stimulus</th>
                        <th>Marks</th>
                        <th>Order</th>
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
    const table = $('#pteSubTypesTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("admin.pte.sub-types.index") }}',
        columns: [
            { data: 'id',                   name: 'id' },
            { data: 'section_name',         name: 'section.name', orderable: false },
            { data: 'name',                 name: 'name' },
            { data: 'tag',                  name: 'tag' },
            { data: 'response_type',        name: 'response_type' },
            { data: 'stimulus_type',        name: 'stimulus_type' },
            { data: 'marks_default',        name: 'marks_default' },
            { data: 'display_order',        name: 'display_order' },
            { data: 'action',               name: 'action', orderable: false, searchable: false },
        ],
        pageLength: 25,
        order: [[7, 'asc']],
    });

    $('#pteSubTypesTable').on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this sub-type? Questions under it will not be deletable.')) return;
        $.ajax({
            url: '{{ url("admin/pte/sub-types") }}/' + id,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: r => r.status === 'success' ? table.ajax.reload(null, false) : alert(r.message),
            error:   x => alert('Error: ' + x.responseText),
        });
    });
});
</script>
@endsection
