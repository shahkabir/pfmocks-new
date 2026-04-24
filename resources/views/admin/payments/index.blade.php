@extends('layouts.app')

@section('title', 'Payment Verification')

@section('content')
<div class="container-fluid mt-3">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Payment Verification</h5>
            <div class="d-flex gap-2 align-items-center">
                <label class="form-label mb-0 small fw-semibold">Filter:</label>
                <select id="statusFilter" class="form-select form-select-sm" style="width:180px">
                    <option value="">All statuses</option>
                    <option value="pending_verification" selected>Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <table id="paymentsTable" class="table table-striped table-bordered table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Exam</th>
                        <th>Module</th>
                        <th>Method</th>
                        <th>Trx ID</th>
                        <th>Sender MSISDN</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
$(function () {
    const table = $('#paymentsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '{{ route("admin.payments.list") }}',
            data: function (d) {
                d.status = $('#statusFilter').val();
            }
        },
        order: [[0, 'desc']],
        pageLength: 25,
        columns: [
            { data: 'id' },
            { data: 'user_name' },
            { data: 'user_email' },
            { data: 'exam_name' },
            { data: 'module_name' },
            { data: 'payment_method' },
            { data: 'transaction_id' },
            { data: 'sender_msisdn' },
            { data: 'amount' },
            { data: 'status' },
            { data: 'created_at' },
            { data: 'action', orderable: false, searchable: false },
        ],
    });

    $('#statusFilter').on('change', () => table.ajax.reload());
});
</script>
@endsection
