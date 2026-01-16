{{-- @extends('layouts.app') --}}

@include('layouts.header')

@section('title', 'IELTS Writing Test')

{{-- @section('content') --}}
<style>
    .exam-topbar {
        background: #343a40;
        color: #fff;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .timer {
        font-weight: bold;
        font-size: 16px;
    }

    .exam-details {
        background: #f4f6f9;
        padding: 15px;
        border-bottom: 1px solid #ddd;
    }

    .exam-body {
        display: flex;
        height: calc(100vh - 260px);
    }

    .question-panel {
        width: 45%;
        padding: 15px;
        overflow-y: auto;
        border-right: 1px solid #ddd;
        background: #fff;
    }

    .writing-task {
        display: flex;
        width: 100%;
        height: 100%;
    }

    .exam-content {
        display: flex;
        width: 100%;
        height: 100%;
    }

    .writing-panel {
        width: 55%;
        padding: 15px;
        background: #fff;
        display: flex;
        flex-direction: column;
    }

    .writing-panel textarea {
        flex: 1;
        resize: none;
        font-size: 14px;
        line-height: 1.6;
    }

    .word-count {
        text-align: left;
        font-size: 13px;
        color: #666;
        margin-top: 5px;
    }

    .exam-footer {
        background: #f4f6f9;
        padding: 10px 15px;
        border-top: 1px solid #ddd;
        display: flex;
        align-items: center;
    }

    .part-nav button {
        margin-right: 5px;
    }

    .footer-right {
        margin-left: auto;
    }

    .feedback {
        margin-top: 15px;
        padding: 10px;
        background: #e9ecef;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 14px;
        color: #495057;
    }

    .palette-btn {
        width: 45px;
        height: 34px;
        border-radius: 4px;
        border: 1px solid #cccccca2;
        background: #36cb77;
        font-size: 13px;
    }

    .palette-btn.active {
        background: #007bff;
        color: #fff;
        border-color: #007bff;
    }

</style>

{{-- TOP BAR --}}
<div class="exam-topbar">
    <div>
        <strong>Candidate:</strong> {{ $user->name }} – {{ $user->id }}
    </div>
    <div class="timer">
        ⏱ <span id="time">59:00</span> minutes left
    </div>
</div>

{{-- EXAM DETAILS --}}
<div class="exam-details">
    <strong>IELTS {{ 'test' }} Writing</strong><br>
    <small>
        {{-- Task 1 · You should spend about 20 minutes on this task.  
        Write at least 150 words. --}}
    </small>
</div>

{{-- @dd($__data) --}}

{{-- {{ dd(get_defined_vars()) }} --}}

<form class="writing-form">
    @csrf
    {{-- MAIN BODY --}}
    <div class="exam-body">
        <div class="exam-content">
            {{-- ================= QUESTIONS ================= --}}
            @foreach($questions as $index => $question)
            {{-- {{ dd($question) }} --}}
                <div class="writing-task" id="task-{{ $index }}" data-question-id="{{ $question['id'] }}" 
                style="{{ $index === 0 ? '' : 'display:none;' }}">
                    {{-- LEFT: QUESTION --}}
                    <div class="question-panel">

                        <h5>Task {{ $index + 1 }}</h5>

                        <div class="mb-3">
                            {!! nl2br(e($question['question_header'])) !!}
                        </div>

                        <div class="mb-3">
                            {!! nl2br(e($question['passage'])) !!}
                        </div>

                        {{-- UNIQUE IDENTIFIER --}}
                        <input type="hidden" name="answers[{{ $question['id'] }}][question_id]"
                        value="{{ $question['id'] }}">

                        {{-- <h5>Part 1</h5>
                        <p>{{ $questions[0]['question_header'] }}</p>
                        <p>{{ $questions[0]['passage'] }}</p> --}}

                    
                    </div>

                    {{-- RIGHT: WRITING AREA --}}
                    <div class="writing-panel">
                        {{-- <textarea id="writingArea"
                                class="form-control"
                                placeholder="Write your answer here..."></textarea> --}}

                        {{-- ANSWER --}}
                        <textarea class="form-control writing-answer" name="answers[{{ $question['id'] }}][answer]"
                            rows="10" placeholder="Write your answer here..."
                            data-question-id="{{ $question['id'] }}"
                        ></textarea>

                        <div class="word-count mt-1">
                            Word count: <span id="wordCount">0</span>
                        </div>

                        @if($showFeedback)
                        <div class="feedback">
                                <h5 class="mt-4">Your answers are rated as per below standard:
                                    Task Achievement: 6.0
                                    Coherence and Cohesion: 6.0
                                    Lexical Resource: 6.0
                                    Grammatical Range and Accuracy: 6.0
                                </h5>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- FOOTER NAVIGATION --}}
    <div class="exam-footer">

        <div class="part-nav">
            {{-- btn btn-primary btn-sm --}}
            {{-- can be made dynamic by for loop for each array  --}}
            @for($i = 0; $i < count($questions); $i++)
                <button type="button" onclick="showTask({{ $i }})" class="palette-btn {{ $i === 0 ? 'active' : '' }}">
                    Part {{ $i + 1 }}
                </button>
            @endfor

            {{-- <button class="palette-btn" onclick="showTask(1)">Part 1</button> 
            <button class="palette-btn" onclick="showTask(2)">Part 2</button> --}}
        </div>

        <div class="footer-right">
            <button type="submit" class="btn btn-success btn-sm">Submit Answers</button>
        </div>
    </div>
</form>

{{-- SCRIPT: WORD COUNT + TIMER --}}
<script>

    // function showTask(index) {
    //     document.querySelectorAll('.writing-task')
    //         .forEach((el, i) => {
    //             el.style.display = i === index ? 'block' : 'none';
    //         });
    // }
    function showTask(index) {
        // Hide all tasks first
        $('.writing-task').hide();

        // Select the specific task and apply the flex styles
        $('.writing-task').eq(index).css({
            'display': 'flex',
            'width': '100%',
            'height': '100%'
        });
    }

    // $('.writing-form').on('submit', function(e) {
    //     e.preventDefault();
    // });

    $('.writing-form').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serialize();

        $.ajax({
            url: "{{ route('exam.ielts.writing.submit') }}",
            method: "POST",
            data: formData,
            success: function(response) {
                alert('Your answers have been submitted successfully!');
                // Optionally, redirect or perform other actions
                console.log(response);
            },
            error: function(xhr, status, error) {
                alert('An error occurred while submitting your answers. Please try again.');
            }
        });
    });

    // Word count per textarea
    document.querySelectorAll('.writing-answer').forEach(textarea => {
        textarea.addEventListener('input', function () {
            const words = this.value.trim().split(/\s+/).filter(Boolean);
            this.closest('.writing-task')
                .querySelector('.count')
                .innerText = words.length;
        });
    });





    // Countdown timer (dummy)
    let seconds = 3540; // 59 minutes

    setInterval(() => {
        if (seconds <= 0) return;
        seconds--;
        let m = Math.floor(seconds / 60);
        let s = seconds % 60;
        document.getElementById('time').innerText =
            `${m}:${s.toString().padStart(2, '0')}`;
    }, 1000);
</script>
{{-- @endsection --}}
