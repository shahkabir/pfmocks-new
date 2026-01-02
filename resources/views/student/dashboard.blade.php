@extends('layouts.app')

@section('title', 'My Exams')

@section('content')
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
                        <a href="{{ route('exam.start', $userExam->module_id) }}"
                           class="btn btn-sm btn-success">
                            Start Exam
                        </a>
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