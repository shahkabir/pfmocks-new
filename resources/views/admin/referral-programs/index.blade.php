@extends('layouts.app')

@section('title', 'Referral Programs')

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
            <h5 class="mb-0"><i class="bi bi-gift-fill me-2"></i>Referral Programs</h5>
            <a href="{{ route('admin.referral-programs.create') }}" class="btn btn-sm btn-success">
                <i class="bi bi-plus-lg"></i> New Program
            </a>
        </div>
        <div class="card-body">
            <table id="programsTable" class="table table-striped table-bordered table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Referee Discount</th>
                        <th>Referrer Reward</th>
                        <th>Min Order</th>
                        <th>Max Discount</th>
                        <th>Starts</th>
                        <th>Ends</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
$(function () {
    $('#programsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("admin.referral-programs.list") }}',
        order: [[0, 'desc']],
        pageLength: 25,
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'referee_discount_value', orderable: false },
            { data: 'referrer_reward_value',  orderable: false },
            { data: 'min_first_purchase_amount' },
            { data: 'max_discount_amount' },
            { data: 'starts_at' },
            { data: 'ends_at' },
            { data: 'is_active', orderable: false },
            { data: 'action',    orderable: false, searchable: false },
        ],
    });
});
</script>
@endsection
