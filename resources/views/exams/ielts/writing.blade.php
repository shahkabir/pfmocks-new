{{-- @extends('layouts.app') --}}

@include('layouts.header')

@section('title', 'IELTS Writing Test')

{{-- @section('content') --}}
<style>
    body { background: #f4f6f9; }

    /* ===== TOP BAR ===== */
    .exam-topbar {
        background: #ffffff;
        border-bottom: 1px solid #ddd;
        padding: 10px 15px;
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
    }

    .exam-topbar-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .exam-topbar-right {
        justify-self: end;
    }

    .exam-logo {
        font-weight: bold;
        font-size: 20px;
        color: #d32f2f;
    }

    .candidate-info {
        font-size: 13px;
        color: #333;
    }

    .timer {
        justify-self: center;
        font-weight: 700;
        font-size: 18px;
        color: #0d6efd;
        background: #e9f2ff;
        border: 1px solid #c7ddff;
        padding: 6px 16px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        letter-spacing: .5px;
        font-variant-numeric: tabular-nums;
    }

    .timer i { font-size: 18px; }

    .timer.warning {
        color: #b45309;
        background: #fff4e5;
        border-color: #ffd8a8;
    }

    .top-icons i {
        font-size: 16px;
        margin-left: 15px;
        cursor: pointer;
        color: #555;
    }

    /* ===== BODY ===== */
    .exam-body {
        display: flex;
        height: calc(100vh - 130px);
        background: #fff;
        border-bottom: 1px solid #ddd;
    }

    .question-panel {
        width: 45%;
        padding: 20px;
        overflow-y: auto;
        border-right: 1px solid #ddd;
        background: #e5e5e5;
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
        padding: 20px;
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

    .feedback {
        margin-top: 15px;
        padding: 10px;
        background: #e9ecef;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 14px;
        color: #495057;
    }

    /* ===== FOOTER ===== */
    .exam-footer {
        background: #ffffff;
        border-top: 1px solid #ddd;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .footer-left {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .footer-right {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Part nav buttons */
    .part-btn {
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        background: #f8f9fa;
        color: #495057;
        transition: background-color .15s, border-color .15s, color .15s;
        font-size: 13px;
    }
    .part-btn:hover:not(.active) {
        background: #e9ecef;
        border-color: #adb5bd;
    }
    .part-btn.active {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
        box-shadow: 0 2px 6px rgba(13,110,253,.25);
    }

    /* Exam action buttons */
    .btn-submit-exam {
        background: #198754;
        border-color: #198754;
        color: #fff;
        font-weight: 500;
        padding: 4px 12px;
        font-size: 13px;
        border-radius: 4px;
        transition: background-color .15s, box-shadow .15s;
    }
    .btn-submit-exam:hover:not(:disabled) {
        background: #146c43;
        border-color: #146c43;
        color: #fff;
        box-shadow: 0 2px 6px rgba(25,135,84,.25);
    }
    .btn-submit-exam:disabled { opacity: .75; }

    .btn-exit-exam {
        background: transparent;
        border: 1px solid #dc3545;
        color: #dc3545;
        font-weight: 500;
        padding: 4px 12px;
        font-size: 13px;
        border-radius: 4px;
        transition: background-color .15s, color .15s;
    }
    .btn-exit-exam:hover {
        background: #dc3545;
        color: #fff;
    }
</style>

{{-- Evaluator feedback panel (only shown when result view has a completed evaluation) --}}
@if(!empty($reviewMode) && !empty($evaluation) && $evaluation->isCompleted())
    @include('exams.partials._evaluator_feedback', ['evaluation' => $evaluation])
@endif

{{-- ================= TOP BAR ================= --}}
<div class="exam-topbar">
    <div class="exam-topbar-left">
        <div class="exam-logo">IELTS</div>
        <div class="candidate-info">
            <strong>{{ $user->name }}</strong><br>
            <span class="text-muted">ID: {{ $user->id }} · {{ $module->name }}</span>
        </div>
    </div>

    <div class="timer" id="timer-pill">
        <i class="bi bi-clock-fill"></i>
        <span id="time">{{ str_pad((int)$module->duration_minutes, 2, '0', STR_PAD_LEFT) }}:00</span>
    </div>

    <div class="exam-topbar-right">
        <div class="top-icons">
            <i class="bi bi-wifi"></i>
            <i class="bi bi-bell"></i>
            <i class="bi bi-list"></i>
            <i class="bi bi-pencil-square"></i>
        </div>
    </div>
</div>

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

                        {{-- <h5>Task {{ $index + 1 }}</h5> --}}

                        <div class="mb-3">
                            {!! $question['passage_instruction'] !!}
                        </div>

                        <div class="mb-3">
                            {!! $question['question_header'] !!}
                        </div>

                        <div class="mb-3">
                            {{-- {!! nl2br(e($question['passage'])) !!} --}}
                            {!! $question['passage'] !!}

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
                            Word count: <span class="word-count-num">0</span>
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

    {{-- ================= FOOTER ================= --}}
    <div class="exam-footer">
        <div class="footer-left">
            @for($i = 0; $i < count($questions); $i++)
                <button type="button"
                        class="part-btn {{ $i === 0 ? 'active' : '' }}"
                        onclick="showTask({{ $i }}, this)">
                    Part {{ $i + 1 }}
                </button>
            @endfor
        </div>

        <div class="footer-right">
            <button type="submit" id="submitAnswersBtn" class="btn-submit-exam">
                <i class="bi bi-check2-circle me-1"></i>Submit Answers
            </button>
            <button type="button" id="exitBtn" class="btn-exit-exam">
                <i class="bi bi-box-arrow-right me-1"></i>Exit
            </button>

            <input type="hidden" name="module_id" value="{{ $module->id }}">
            <input type="hidden" name="exam_name" value="{{ $module->exam->name ?? $module->name }}">
            <input type="hidden" name="module_type" value="{{ $module->module_type }}">
            <input type="hidden" name="total_questions" value="{{ count($questions) }}">
        </div>
    </div>
</form>

{{-- SCRIPT: WORD COUNT + TIMER --}}
<script>
    // ── Review mode bootstrap (dashboard "Show Result") ──
    @if(!empty($reviewMode))
    $(function () {
        $('.writing-form :input').not('button').prop('disabled', true);
        $('#submitAnswersBtn').prop('disabled', true)
                              .html('<i class="bi bi-lock-fill me-1"></i>Submitted');
        $('#timer-pill').removeClass('warning');
        $('#time').text('—');
    });
    @endif

    function showTask(index, btn) {
        // Hide all tasks first
        $('.writing-task').hide();

        // Select the specific task and apply the flex styles
        $('.writing-task').eq(index).css({
            'display': 'flex',
            'width': '100%',
            'height': '100%'
        });

        // Update active part button
        document.querySelectorAll('.part-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
    }

    // Exit button
    $('#exitBtn').on('click', function () {
        if (confirm('Exit without submitting? Your answers will be lost.')) {
            window.location.href = "{{ route('dashboard') }}";
        }
    });

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
                Swal.fire({
                    icon: 'success',
                    title: 'Answers Submitted',
                    text: 'Answers have been submitted. You will be notified once our Evaluators completes evaluation.',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then(function () {
                    window.location.href = "{{ route('dashboard') }}";
                });
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Submission Failed',
                    text: 'An error occurred while submitting your answers. Please try again.',
                    confirmButtonText: 'OK',
                    theme: 'bootstrap',
                });
            }
        });
    });

    // Word count per textarea (each writing-task has its own counter)
    document.querySelectorAll('.writing-answer').forEach(textarea => {
        const updateCount = () => {
            const words   = textarea.value.trim().split(/\s+/).filter(Boolean);
            const counter = textarea.closest('.writing-task')?.querySelector('.word-count-num');
            if (counter) counter.innerText = words.length;
        };
        textarea.addEventListener('input', updateCount);
        updateCount(); // initial render (e.g. when reviewMode pre-fills text)
    });





    // Countdown timer
    let seconds = {{ (int)$module->duration_minutes * 60 }};
    const $timerPill = document.getElementById('timer-pill');
    const $timeText  = document.getElementById('time');

    const countdownInterval = setInterval(() => {
        if (seconds <= 0) {
            clearInterval(countdownInterval);
            return;
        }
        seconds--;
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        $timeText.innerText = `${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;

        if (seconds <= 300) $timerPill.classList.add('warning');
    }, 1000);
</script>
{{-- @endsection --}}
