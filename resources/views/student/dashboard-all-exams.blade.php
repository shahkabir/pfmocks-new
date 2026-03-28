@extends('layouts.app')

@section('title', 'My Exams')

@section('content')
<style>
   
    .row {
        row-gap: 15px;
    }
    /* Custom CSS for pulse animation on badges */
    /* .status-dot {
        height: 10px;
        width: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }
    

    .status-dot.pending {
    background-color: rgb(255, 193, 7);#dc3545; */
   /* animation: pulse 1.5s infinite; */
/* } */

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    }
    70% {
        box-shadow: 0 0 0 6px rgba(220, 53, 69, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
    }
}

</style>

<div class="container-fluid">

    <h4 class="mb-3">Available Exams - {{ strtoupper(request()->segment(2)) }}</h4>

    {{-- @dd($__data) --}}

    {{-- {{ dd(get_defined_vars())}} --}}
    <div class="row" style="gap-rows: 0.5rem">
        @forelse($exams as $exam)
        <div class="col-md-4">
            <div class="card card-outline card-primary h-100">

                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        {{ $exam->name }}
                    </h5>

                    {{-- Example tag badge --}}
                    <span class="badge badge-danger text-uppercase">
                        {{ $exam->tag }}
                    </span>
                </div>

                {{-- BODY --}}
                <div class="card-body">

                    {{-- <p class="mb-2 text-muted">Modules</p> --}}

                    @forelse($exam->modules as $module)

                        <div class="mb-2 p-2 border rounded-sm">

                            <div class="d-flex justify-content-between align-items-center">

                                {{-- Module Name --}}
                                <strong>{{ $module->name }}</strong>

                                {{-- Duration --}}
                                <span class="badge bg-info text-dark">
                                    {{ $module->duration_minutes }} min
                                </span>
                            </div>

                            {{-- Price --}}
                            <div class="mt-1">
                                @if(isset($module->price_in_bdt))
                                    <span class="badge bg-success text-white">
                                        {{ number_format($module->price_in_bdt, 2) }} BDT
                                    </span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </div>

                        </div>

                    @empty
                        <div class="text-muted">No modules available.</div>
                    @endforelse

                </div>

                {{-- FOOTER (optional actions) --}}
                <div class="card-footer text-right">
                    <a href="#" class="btn btn-sm btn-secondary">
                        View Details <i class="bi bi-chevron-double-up"></i>
                    </a>

                    {{-- @if($userExam->status === 'free')
                                <a href="{{ route('exam.start', $userExam->module_id) }}"
                                class="btn btn-sm btn-success">
                                    Start Exam
                                </a>
                            @elseif($userExam->status === 'payment_pending')
                                <span class="status-dot pending dot-top"></span>
                                <span class="text-warning mr-2">Payment Pending</span>
                            @elseif($userExam->status === 'cancelled')
                                <span class="text-danger mr-2">Cancelled</span>
                            @elseif($userExam->status === 'purchased')
                            <a href="{{ route('exam.start', $userExam->module_id) }}"
                            class="btn btn-sm btn-success">
                                Start Exam
                            </a>
                            @elseif($userExam->status === 'completed')
                                <a href="{{ route('exam.start', $userExam->id) }}"
                                class="btn btn-sm btn-info">
                                    View Result
                                </a>
                            @endif --}}
                </div>

            </div>
        </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No exams available yet.
                </div>
            </div>
        @endforelse

    </div>
</div>
@endsection