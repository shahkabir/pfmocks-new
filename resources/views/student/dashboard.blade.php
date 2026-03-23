@extends('layouts.app')

@section('title', 'My Exams')

@section('content')
<style>
    .row {
        row-gap: 15px;
    }

    .status-dot {
        height: 10px;
        width: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }
    /* .dot-top {
        position: relative;
        top: -1px;
    }
    .pending {
        background-color: #ffc107; /* Bootstrap warning color */
    /*} 
    */

    .status-dot.pending {
    background-color: rgb(255, 193, 7);/*#dc3545;*/
    animation: pulse 1.5s infinite;
}

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

    <h4 class="mb-3">My Exams</h4>

    {{-- @dd($__data) --}}

    {{-- {{ dd(get_defined_vars())}} --}}

    <div class="row">
        @forelse($exams as $userExam)
            <div class="col-md-4">
                <div class="card card-outline {{ $userExam->type === 'free' ? 'card-success' : 'card-primary' }}">
                    <div class="card-header">
                        <h5 class="card-title">
                            {{ $userExam->module->exam->name }}
                        </h5>
                        <div class="card-tools">
                            <span class="badge {{ $userExam->type === 'free' ? 'badge-success' : 'badge-info' }}">
                                {{ strtoupper($userExam->type) }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        <p>
                            <strong>Module:</strong> {{ $userExam->module->name }}
                        </p>
                        <p>
                            <strong>Duration:</strong>
                            {{ $userExam->module->duration_minutes }} minutes
                        </p>

                        
                            <p>
                                <strong>Price:</strong> 
                                @if($userExam->type === 'paid')
                                    ৳{{ number_format($userExam->price, 2) }}
                                @else
                                    {{ 'Free' }}
                                @endif
                            </p>
                        
                    </div>

                    <div class="card-footer text-right">
                        @if($userExam->status === 'free')
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
                        @endif
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