@extends('layouts.app')
@section('title', 'PTE Question Bank')

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
            <h5 class="mb-0">PTE Question Bank</h5>
            <a href="{{ route('admin.pte.questions.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Question
            </a>
        </div>

        <div class="card-body">
            <table id="pteQuestionsTable" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Granular ID</th>
                        <th>Section</th>
                        <th>Sub-type</th>
                        <th>Marks</th>
                        <th>Difficulty</th>
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
    const table = $('#pteQuestionsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("admin.pte.questions.index") }}',
        columns: [
            { data: 'id',             name: 'id' },
            { data: 'granular_id',    name: 'question_granular_id' },
            { data: 'section',        name: 'subType.section.name', orderable: false },
            { data: 'sub_type',       name: 'subType.name',         orderable: false },
            { data: 'marks',          name: 'marks' },
            { data: 'difficulty',     name: 'difficulty' },
            { data: 'is_active',      name: 'is_active', render: v => v ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' },
            { data: 'created_at',     name: 'created_at' },
            { data: 'action',         name: 'action', orderable: false, searchable: false },
        ],
        pageLength: 25,
        order: [[0, 'desc']],
    });

    $('#pteQuestionsTable').on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this question (and all its options/blanks/segments)?')) return;
        $.ajax({
            url: '{{ url("admin/pte/questions") }}/' + id,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: r => r.status === 'success' ? table.ajax.reload(null, false) : alert(r.message),
            error:   x => alert('Error: ' + x.responseText),
        });
    });
});
</script>
@endsection
