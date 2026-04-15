@extends('layouts.app')
@section('title', 'Question Groups')

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
            <h5 class="mb-0">Question Groups</h5>
            <a href="{{ route('admin.question-groups.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Group
            </a>
        </div>

        <div class="card-body">
            <table id="groupsTable" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Question</th>
                        <th>Part No.</th>
                        <th>Blocks</th>
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
    const table = $('#groupsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("admin.question-groups.index") }}',
        columns: [
            { data: 'id',             name: 'id' },
            { data: 'question_label', name: 'question_id', searchable: true },
            { data: 'part_number',    name: 'part_number' },
            { data: 'blocks_count',   name: 'blocks_count',  orderable: false, searchable: false },
            { data: 'action',         name: 'action',        orderable: false, searchable: false },
        ],
        pageLength: 25,
        order: [[0, 'desc']],
    });

    $('#groupsTable').on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this question group?')) return;
        $.ajax({
            url: '{{ route("admin.question-groups.index") }}/' + id,
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
