@extends('layouts.app')

@section('title', 'Evaluations')

@section('content')
<div class="container-fluid mt-3">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Evaluations</h5>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <select id="moduleTypeFilter" class="form-select form-select-sm" style="width:160px">
                    <option value="">All module types</option>
                    <option value="writing">Writing</option>
                    <option value="speaking">Speaking</option>
                </select>
                <select id="evalStatusFilter" class="form-select form-select-sm" style="width:170px">
                    <option value="">All statuses</option>
                    <option value="unassigned" selected>Unassigned</option>
                    <option value="assigned">Assigned</option>
                    <option value="in_progress">In progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
        </div>

        <div class="card-body">
            <table id="evalTable" class="table table-striped table-bordered table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>Attempt #</th>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Exam</th>
                        <th>Module</th>
                        <th>Type</th>
                        <th>Finished At</th>
                        <th>Evaluator</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- ================= ASSIGN MODAL ================= --}}
<div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.evaluations.assign') }}" class="modal-content">
            @csrf
            <div class="modal-header" style="background:linear-gradient(135deg,#0d6efd,#0a58ca);color:#fff;border:none;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-check-fill me-1"></i>Assign Evaluator
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="exam_attempt_id" id="assign_attempt_id">

                <div class="alert alert-light border mb-3">
                    <div class="small text-muted">Attempt</div>
                    <div class="fw-semibold" id="assign_attempt_label">—</div>
                </div>

                <label class="form-label fw-semibold">Choose evaluator <span class="text-danger">*</span></label>
                <select name="evaluator_id" class="form-select" required>
                    <option value="">— Select an evaluator —</option>
                    @foreach($evaluators as $ev)
                        <option value="{{ $ev->id }}">{{ $ev->name }} ({{ $ev->email }})</option>
                    @endforeach
                </select>
                @if($evaluators->isEmpty())
                    <div class="form-text text-danger">
                        No users with the <code>evaluator</code> role exist yet. Create one from the Users page.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send-fill me-1"></i>Assign &amp; Notify
                </button>
            </div>
        </form>
    </div>
</div>

<script>
$(function () {
    const table = $('#evalTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '{{ route("admin.evaluations.list") }}',
            data: function (d) {
                d.module_type = $('#moduleTypeFilter').val();
                d.eval_status = $('#evalStatusFilter').val();
            }
        },
        order: [[6, 'desc']],
        pageLength: 25,
        columns: [
            { data: 'attempt_id' },
            { data: 'student_name' },
            { data: 'student_email' },
            { data: 'exam_name' },
            { data: 'module_name' },
            { data: 'module_type' },
            { data: 'ended_at' },
            { data: 'evaluator_name' },
            { data: 'evaluation_status' },
            { data: 'action', orderable: false, searchable: false },
        ],
    });

    $('#moduleTypeFilter, #evalStatusFilter').on('change', () => table.ajax.reload());

    // Open assign modal pre-filled with the right attempt id
    $(document).on('click', '.assign-btn', function () {
        const $row    = $(this);
        $('#assign_attempt_id').val($row.data('attempt-id'));
        $('#assign_attempt_label').text(
            'Attempt #' + $row.data('attempt-id') + ' — ' +
            $row.data('student') + ' · ' + $row.data('module')
        );
        bootstrap.Modal.getOrCreateInstance(document.getElementById('assignModal')).show();
    });
});
</script>
@endsection
