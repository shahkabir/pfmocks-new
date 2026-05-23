@extends('layouts.app')

@section('title', 'Scholarships')

@section('content')
<div class="container-fluid mt-3">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0"><i class="bi bi-mortarboard-fill me-2"></i>Scholarships</h5>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <select id="typeFilter" class="form-select form-select-sm" style="width:200px">
                    <option value="">All types</option>
                    @foreach(\App\Constants\ScholarshipConstants::TYPES as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select id="fundingFilter" class="form-select form-select-sm" style="width:160px">
                    <option value="">All funding</option>
                    @foreach(\App\Constants\ScholarshipConstants::FUNDING_TYPES as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
                <a href="{{ route('admin.scholarships.create') }}" class="btn btn-sm btn-success">
                    <i class="bi bi-plus-lg"></i> New
                </a>
            </div>
        </div>
        <div class="card-body">
            <table id="scholarshipsTable" class="table table-striped table-bordered table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Country</th>
                        <th>Funding</th>
                        <th>Deadline</th>
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
    const table = $('#scholarshipsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '{{ route("admin.scholarships.list") }}',
            data: function (d) {
                d.type    = $('#typeFilter').val();
                d.funding = $('#fundingFilter').val();
            }
        },
        order: [[0, 'desc']],
        pageLength: 25,
        columns: [
            { data: 'id' },
            { data: 'title' },
            { data: 'type' },
            { data: 'country_name', orderable: false },
            { data: 'funding_type', orderable: false },
            { data: 'deadline' },
            { data: 'is_active',   orderable: false },
            { data: 'action',      orderable: false, searchable: false },
        ],
    });

    $('#typeFilter, #fundingFilter').on('change', () => table.ajax.reload());
});
</script>
@endsection
