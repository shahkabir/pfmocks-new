@extends('layouts.app')
@section('title', 'Questions')

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
            <h5 class="mb-0">Questions</h5>
            <a href="{{ route('admin.questions.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Question
            </a>
        </div>

        <div class="card-body">
            <table id="questionsTable" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Module</th>
                        <th>Type</th>
                        <th>Header</th>
                        <th>Marks</th>
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
    const table = $('#questionsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("admin.questions.index") }}',
        columns: [
            { data: 'id',           name: 'id' },
            { data: 'module_name',  name: 'module.name', searchable: true },
            { data: 'type',         name: 'type' },
            { data: 'header_short', name: 'question_header', searchable: true },
            { data: 'marks',        name: 'marks' },
            { data: 'sort_order',   name: 'sort_order' },
            { data: 'action',       name: 'action', orderable: false, searchable: false },
        ],
        pageLength: 25,
        order: [[0, 'desc']],
    });

    $('#questionsTable').on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this question and all its options/groups?')) return;
        $.ajax({
            url: '{{ route("admin.questions.index") }}/' + id,
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
