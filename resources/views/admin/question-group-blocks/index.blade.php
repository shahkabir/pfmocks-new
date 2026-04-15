@extends('layouts.app')
@section('title', 'Question Group Blocks')

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
            <h5 class="mb-0">Question Group Blocks</h5>
            <a href="{{ route('admin.question-group-blocks.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Block
            </a>
        </div>

        <div class="card-body">
            <table id="blocksTable" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Group</th>
                        <th>Instruction</th>
                        <th>Options</th>
                        <th>Sort</th>
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
    const table = $('#blocksTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("admin.question-group-blocks.index") }}',
        columns: [
            { data: 'id',                name: 'id' },
            { data: 'group_label',       name: 'question_group_id', searchable: true },
            { data: 'instruction_short', name: 'instruction_text',  searchable: true },
            { data: 'options_count',     name: 'options_count',     orderable: false, searchable: false },
            { data: 'sort_order',        name: 'sort_order' },
            { data: 'action',            name: 'action',            orderable: false, searchable: false },
        ],
        pageLength: 25,
        order: [[0, 'desc']],
    });

    $('#blocksTable').on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this block?')) return;
        $.ajax({
            url: '{{ route("admin.question-group-blocks.index") }}/' + id,
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
