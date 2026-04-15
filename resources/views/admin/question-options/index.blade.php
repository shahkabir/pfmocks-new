@extends('layouts.app')
@section('title', 'Question Options')

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
            <h5 class="mb-0">Question Options</h5>
            <a href="{{ route('admin.question-options.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Option
            </a>
        </div>

        <div class="card-body">
            <table id="optionsTable" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Question</th>
                        <th>Type</th>
                        <th>Option Text</th>
                        <th>Correct</th>
                        <th>Active</th>
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
    const table = $('#optionsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("admin.question-options.index") }}',
        columns: [
            { data: 'id',               name: 'id' },
            { data: 'question_short',   name: 'actual_question', searchable: true },
            { data: 'question_type',    name: 'question_type' },
            { data: 'option_text',      name: 'option_text' },
            { data: 'is_correct_badge', name: 'is_correct',  orderable: false },
            { data: 'is_active_badge',  name: 'is_active',   orderable: false },
            { data: 'sort_order',       name: 'sort_order' },
            { data: 'action',           name: 'action',      orderable: false, searchable: false },
        ],
        pageLength: 25,
        order: [[0, 'desc']],
    });

    $('#optionsTable').on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this option?')) return;
        $.ajax({
            url: '{{ route("admin.question-options.index") }}/' + id,
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
