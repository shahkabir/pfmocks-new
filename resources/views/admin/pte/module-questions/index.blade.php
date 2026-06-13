@extends('layouts.app')
@section('title', 'PTE Mock Test Assembly')

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
            <h5 class="mb-0">PTE Mock Test Assembly</h5>
            <a href="{{ route('admin.pte.module-questions.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Question to Module
            </a>
        </div>

        <div class="card-body">
            <table id="pteMwqTable" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>PTE Module</th>
                        <th>Question</th>
                        <th>Order</th>
                        <th>Active</th>
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
    const table = $('#pteMwqTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("admin.pte.module-questions.index") }}',
        columns: [
            { data: 'id',             name: 'id' },
            { data: 'pte_module',     name: 'pte_module_id' },
            { data: 'question',       name: 'question.question_granular_id', orderable: false },
            { data: 'display_order',  name: 'display_order' },
            { data: 'is_active',      name: 'is_active', render: v => v ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' },
            { data: 'created_at',     name: 'created_at' },
            { data: 'action',         name: 'action', orderable: false, searchable: false },
        ],
        pageLength: 25,
        order: [[1, 'asc'], [3, 'asc']],
    });

    $('#pteMwqTable').on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (!confirm('Remove this question from the module?')) return;
        $.ajax({
            url: '{{ url("admin/pte/module-questions") }}/' + id,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: r => r.status === 'success' ? table.ajax.reload(null, false) : alert(r.message),
            error:   x => alert('Error: ' + x.responseText),
        });
    });
});
</script>
@endsection
