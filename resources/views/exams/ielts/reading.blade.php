{{-- @extends('layouts.app') --}}

@section('title', 'IELTS Reading Test')

@include('layouts.header')
{{-- @section('content') --}}
<style>
    /* ===== Shared Theme ===== */
    body {
        background: #f4f6f9;
    }

    /* ===== TOP BAR ===== */
    .exam-topbar {
        background: #ffffff;
        border-bottom: 1px solid #ddd;
        padding: 10px 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .exam-topbar-left {
        display: flex;
        align-items: center;
        gap: 15px;
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
        font-weight: bold;
        font-size: 14px;
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
        height: calc(100vh - 200px);
        background: #fff;
        border-bottom: 1px solid #ddd;
    }

    .reading-panel {
        width: 55%;
        padding: 20px;
        overflow-y: auto;
        border-right: 1px solid #ddd;
        background: #e5e5e5; /* very soft blue background for reading panel */
    }

    .question-panel {
        width: 45%;
        padding: 20px;
        overflow-y: auto;
    }

    .question {
        margin-bottom: 25px;
    }

    .question h6 {
        font-weight: 600;
        margin-bottom: 10px;
    }

    .question label {
        display: block;
        font-weight: normal;
        cursor: pointer;
    }

    /* ===== QUESTION PALETTE ===== */
    .question-palette {
        background: #f9fafb;
        border-top: 1px solid #ddd;
        padding: 10px 15px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .palette-btn {
        width: 34px;
        height: 34px;
        border-radius: 4px;
        border: 1px solid #ccc;
        background: #fff;
        font-size: 13px;
    }

    .palette-btn.active {
        background: #007bff;
        color: #fff;
        border-color: #007bff;
    }

    /* ===== FOOTER ===== */
    .exam-footer {
        background: #ffffff;
        border-top: 1px solid #ddd;
        padding: 10px 15px;
        display: flex;
        align-items: center;
    }

    .footer-right {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .current-time {
        font-weight: bold;
        font-size: 14px;
    }
</style>

<style>
/* Style for mcq, multi select */
/* Default option block */
.exam-option {
    padding: 8px 12px;
    border-radius: 6px;
    transition: background-color 0.2s ease, border-color 0.2s ease;
    border: 1px solid transparent;
}

/* Hover */
.exam-option:hover {
    background-color: #f8f9fa;
}

/* Selected */
.exam-option.selected {
    background-color: #e9f2ff;   /* very soft blue */
    border-color: #c7ddff;
}

/* ===== Review mode (shown after "Show details") ===== */
.review-mode .exam-option.user-correct {
    background-color: #d1e7dd !important;
    border-color: #198754 !important;
}
.review-mode .exam-option.user-wrong {
    background-color: #f8d7da !important;
    border-color: #dc3545 !important;
}
.review-mode .exam-option.is-correct-answer {
    background-color: #d1e7dd !important;
    border-color: #198754 !important;
    box-shadow: 0 0 0 2px rgba(25,135,84,.2);
}
.review-mode .exam-option .review-tag {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .3px;
    text-transform: uppercase;
    padding: 2px 6px;
    border-radius: 4px;
    margin-left: 8px;
}
.review-tag.tag-correct  { background:#198754; color:#fff; }
.review-tag.tag-wrong    { background:#dc3545; color:#fff; }
.review-tag.tag-answer   { background:#0d6efd; color:#fff; }

.explanation-panel {
    margin-top: 10px;
    padding: 10px 14px;
    background: #fff7e6;
    border-left: 4px solid #f59f00;
    border-radius: 4px;
    font-size: 13px;
    color: #5a3e00;
    display: none;
}
.review-mode .explanation-panel { display: block; }

.fill-input.fill-correct { background:#d1e7dd !important; border-color:#198754 !important; }
.fill-input.fill-wrong   { background:#f8d7da !important; border-color:#dc3545 !important; }

/* ===== Result Modal ===== */
#resultModal .modal-content {
    border-radius: 16px;
    overflow: hidden;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,.25);
}
#resultModal .modal-header {
    background: linear-gradient(135deg, #0d6efd, #0a58ca);
    color: #fff;
    border: none;
    padding: 1.75rem 1.75rem 1.25rem;
}
#resultModal .modal-title {
    font-weight: 700;
    letter-spacing: -.3px;
}
#resultModal .modal-body { padding: 1.5rem 1.75rem; }
#resultModal .stat-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}
#resultModal .stat-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 12px 14px;
    transition: transform .15s, box-shadow .15s;
}
#resultModal .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(0,0,0,.08);
}
#resultModal .stat-label {
    font-size: 11px;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: .5px;
    display: flex;
    align-items: center;
    gap: 6px;
}
#resultModal .stat-value {
    font-size: 1.4rem;
    font-weight: 700;
    color: #212529;
    margin-top: 2px;
}
#resultModal .stat-value.accent { color: #0d6efd; }
#resultModal .band-hero {
    grid-column: 1 / -1;
    background: linear-gradient(135deg, #e9f2ff, #d0e4ff);
    border: 1px solid #b8d4ff;
    text-align: center;
    padding: 18px;
}
#resultModal .band-hero .stat-value {
    font-size: 2.25rem;
    color: #0a58ca;
}
#resultModal .modal-footer {
    border-top: 1px solid #e9ecef;
    padding: 1rem 1.75rem;
    gap: .5rem;
}
#resultModal .btn-pill {
    border-radius: 8px;
    padding: .5rem 1.25rem;
    font-weight: 600;
    transition: transform .15s, box-shadow .15s;
}
#resultModal .btn-pill:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(13,110,253,.3);
}
@keyframes pop {
    0%   { transform: scale(.8); opacity: 0; }
    60%  { transform: scale(1.05); opacity: 1; }
    100% { transform: scale(1); }
}
#resultModal.showing .modal-content,
#resultModal.show .modal-content { animation: pop .35s ease both; }

#reviewBanner {
    display: none;
    background: #fff3cd;
    border: 1px solid #ffeeba;
    color: #856404;
    padding: 10px 16px;
    border-radius: 8px;
    margin: 12px 15px;
    font-size: 14px;
}
.review-mode #reviewBanner { display: block; }
</style>

{{-- ================= TOP BAR ================= --}}
<div class="exam-topbar">
    <div class="exam-topbar-left">
        <div class="exam-logo">IELTS</div>
        <div class="candidate-info">
            <strong>48887345</strong><br>
            <span class="timer">59 minutes remaining</span>
        </div>
    </div>

    <div class="top-icons">
        <i class="fas fa-wifi"></i>
        <i class="far fa-bell"></i>
        <i class="fas fa-bars"></i>
        <i class="far fa-edit"></i>
    </div>
</div>

{{-- ================= REVIEW BANNER ================= --}}
<div id="reviewBanner">
    <i class="bi bi-eye-fill me-1"></i>
    <strong>Review Mode:</strong> Showing correct answers and explanations. Submit button has been disabled.
</div>

{{-- ================= BODY ================= --}}

{{-- {{ dd(get_defined_vars()) }} --}}

@php
    $parts = [];

    foreach ($questions as $question) {

        $part = $question['part_number']; //['group']
        // var_dump($part);

        if (!isset($parts[$part])) {

            $parts[$part] = [
                'header' => $question['question_header'],
                'passage_instruction' => $question['passage_instruction'],
                'passage' => $question['passage'],
                'blocks' => []
            ];
        }

        // Each instruction block
        foreach ($question['group']['blocks'] as $block) {

            $blockKey = $block['id'];

            if (!isset($parts[$part]['blocks'][$blockKey])) {

                $parts[$part]['blocks'][$blockKey] = [
                    'instruction' => $block['instruction_text'],
                    'questions' => []
                ];
            }

            // echo 'question_option_ids: '. print_r($block['question_option_ids']);

            // print_r(collect($question['options'])->pluck('id'));

            // Filter only options belonging to this block
            $optionsInBlock = collect($question['options'])
                ->whereIn('id', json_decode($block['question_option_ids'], true))
                ->groupBy('actual_question');

            //dd($optionsInBlock);

            foreach ($optionsInBlock as $actualQuestion => $options) {
                $parts[$part]['blocks'][$blockKey]['questions'][$actualQuestion] = $options;
            }
        }
    }
    
    // dd($parts);

@endphp

<form class="reading-form">
@csrf
    <div class="exam-body">
        @php $qNo = 1; @endphp
        @foreach($parts as $partNumber => $partData)

                <div class="exam-body reading-part"
                    id="part-{{ $partNumber }}"
                    style="{{ $partNumber === array_key_first($parts) ? '' : 'display:none' }}">

                    {{-- LEFT: PASSAGE --}}
                    <div class="reading-panel">
                        <h5>Part {{ $partNumber }}</h5>
                        <p>{!! $partData['passage_instruction'] !!}</p>
                        <p><strong>{{ $partData['header'] }}</strong></p>
                        <div>
                            {{-- {!! nl2br(e($partData['passage'])) !!} --}}
                            {!! $partData['passage'] !!}
                        </div>
                    </div>

                    {{-- RIGHT: QUESTIONS --}}
                    <div class="question-panel">
                    
                        @foreach($partData['blocks'] as $block)

                            {{-- INSTRUCTION (shown ONCE per block) --}}
                                <div class="alert alert-light mb-3 border-secondary">
                                    <b>{!! $block['instruction'] !!}</b>
                                    {{-- nl2br(e()) --}}
                                </div>
                            {{-- QUESTIONS UNDER THIS INSTRUCTION --}}
                            @foreach($block['questions'] as $actualQuestion => $options)
                                @php
                                    $questionExplanation = collect($options)
                                        ->pluck('correct_answer_explanation')
                                        ->filter()
                                        ->first();
                                @endphp
                                <div class="mb-4 question-block" data-q-no="{{ $qNo }}">

                                    <h6 class="mb-2">
                                             @if(!str_contains($actualQuestion, '[[blank]]'))
                                                <b>{{ $qNo }}&nbsp;</b>{{ $actualQuestion }}
                                             @endif
                                    </h6>

                                        @foreach($options as $option)

                                            @if($option['question_type'] == 'fill_in_blanks')

                                            <div class="mb-3">
                                                <b>{{ $qNo }}&nbsp;</b>
                                                {!! str_replace(
                                                    '[[blank]]',
                                                    '<input type="text"
                                                            class="form-control d-inline-block mx-1 fill-input"
                                                            style="width:160px"
                                                            name="answers[' . $option['question_id'] . '][question_option_id][' . $option['id'] . ']"
                                                            data-question-id="' . $option['question_id'] . '"
                                                            data-option-id="' . $option['id'] . '"
                                                            data-is-correct="' . ($option['is_correct'] ? '1' : '0') . '"
                                                            data-correct-text="' . e($option['option_text']) . '">',
                                                    e($option['actual_question'])
                                                ) !!}
                                            </div>

                                            @elseif($option['question_type'] == 'mcq_single')

                                                <div class="exam-option"
                                                     data-option-id="{{ $option['id'] }}"
                                                     data-is-correct="{{ $option['is_correct'] ? '1' : '0' }}">
                                                    <input class="form-check-input"
                                                        type="radio"
                                                        name="answers[{{ $option['question_id'] }}][question_option_id][{{$qNo}}]"
                                                        value="{{ $option['id'] }}"
                                                        id="option-{{ $option['id'] }}">

                                                    <label class="form-check-label" for="option-{{ $option['id'] }}">
                                                        {{ $option['option_text'] }}
                                                    </label>
                                                </div>

                                            @elseif($option['question_type'] === 'mcq_multiple')

                                                <div class="mb-1 exam-option"
                                                     data-option-id="{{ $option['id'] }}"
                                                     data-is-correct="{{ $option['is_correct'] ? '1' : '0' }}">
                                                    <input class="form-check-input"
                                                        type="checkbox"
                                                        name="answers[{{ $option['question_id'] }}][question_option_id][{{$qNo}}][]"
                                                        value="{{ $option['id'] }}"
                                                        id="option-{{ $option['id'] }}">

                                                    <label class="form-check-label"
                                                        for="option-{{ $option['id'] }}">
                                                        {{ $option['option_text'] }}
                                                    </label>
                                                </div>

                                            @elseif($option['question_type'] === 'mcq_select')
                                                <div class="mb-3">
                                                    <select class="form-select"
                                                        name="answers[{{ $option['question_id'] }}][question_option_id][{{$qNo}}]">

                                                        <option value="">-- Select Answer --</option>

                                                        @foreach($options as $option)
                                                            <option value="{{ $option['id'] }}"
                                                                    data-is-correct="{{ $option['is_correct'] ? '1' : '0' }}">
                                                                {{ $option['option_text'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </div>
                                                @php break; @endphp
                                            @endif

                                        @endforeach

                                    @if($questionExplanation)
                                        <div class="explanation-panel">
                                            <i class="bi bi-lightbulb-fill me-1"></i>
                                            <strong>Explanation:</strong>
                                            {!! $questionExplanation !!}
                                        </div>
                                    @endif
                                </div>
                             @php $qNo++ @endphp
                            {{-- {{ QUESTION BLOCK ENDS HERE }} --}}
                            @endforeach
                        {{-- BLOCKS loop ends here --}}
                        @endforeach
                    </div>
                </div>
       
        {{-- PARTS loop ends here--}}
        @endforeach 
    </div>


    {{-- ================= Parts ================= --}}
    <div class="mb-3">
        @foreach($parts as $partNumber => $partData)
            <button type="button" class="btn btn-sm btn-primary" onclick="showPart({{ $partNumber }})">
                Part {{ $partNumber }}
            </button>
        @endforeach
    </div>

    {{-- ================= QUESTION PALETTE ================= --}}
    {{-- <div class="question-palette">
        @for($i = 1; $i <= 13; $i++)
            <button class="palette-btn {{ $i === 1 ? 'active' : '' }}">
                {{ $i }}
            </button>
        @endfor
    </div> --}}



    {{-- ================= FOOTER ================= --}}
    <div class="exam-footer">
        <div class="current-time" id="stopwatch">
            ⏰ 00:00
        </div>

        <div class="footer-right">
            <button type="submit" id="submitAnswersBtn" class="btn btn-success btn-sm">
                <i class="bi bi-check2-circle me-1"></i>Submit Answers
            </button>
            <button type="button" id="exitBtn" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i>Exit
            </button>
            <input type="hidden" name="module_id" value="{{ $module->id }}">
            <input type="hidden" name="exam_name" value="{{ $module->name }}">
            <input type="hidden" name="module_type" value="{{ $module->module_type }}">
        </div>
    </div>
</form>

{{-- ================= RESULT MODAL ================= --}}
<div class="modal fade" id="resultModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
     aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 class="modal-title mb-1" id="resultModalLabel">
                        <i class="bi bi-award-fill me-2"></i>Exam Completed
                    </h4>
                    <div style="font-size:.85rem;opacity:.85;">Here's how you performed</div>
                </div>
            </div>
            <div class="modal-body">
                <div class="stat-grid">
                    <div class="stat-card band-hero">
                        <div class="stat-label justify-content-center">
                            <i class="bi bi-star-fill"></i> IELTS Band Score
                        </div>
                        <div class="stat-value" id="resBand">—</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-label"><i class="bi bi-mortarboard-fill"></i> Exam</div>
                        <div class="stat-value" id="resExam" style="font-size:1rem;">—</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-label"><i class="bi bi-bookmark-star-fill"></i> Module</div>
                        <div class="stat-value" id="resModule" style="font-size:1rem;">—</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-label"><i class="bi bi-list-ol"></i> Total Questions</div>
                        <div class="stat-value" id="resTotal">—</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-label"><i class="bi bi-check-circle-fill"></i> Correct Answers</div>
                        <div class="stat-value accent" id="resCorrect">—</div>
                    </div>

                    <div class="stat-card" style="grid-column:1 / -1;">
                        <div class="stat-label"><i class="bi bi-stopwatch-fill"></i> Time Elapsed</div>
                        <div class="stat-value" id="resTime" style="font-size:1.2rem;">—</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="showDetailsBtn" class="btn btn-outline-primary btn-pill">
                    <i class="bi bi-list-columns-reverse me-1"></i>Show Details
                </button>
                <button type="button" id="okBtn" class="btn btn-primary btn-pill">
                    <i class="bi bi-check-lg me-1"></i>OK
                </button>
            </div>
        </div>
    </div>
</div>

{{-- @endsection --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"></script>
<script>

    // ── Stopwatch ──────────────────────────────────────────────────────────
    const examStart = Date.now();
    function formatDuration(sec) {
        sec = Math.max(0, Math.floor(sec));
        const h = Math.floor(sec / 3600);
        const m = Math.floor((sec % 3600) / 60);
        const s = sec % 60;
        const pad = n => String(n).padStart(2, '0');
        return h > 0 ? `${pad(h)}:${pad(m)}:${pad(s)}` : `${pad(m)}:${pad(s)}`;
    }
    const stopwatchTimer = setInterval(() => {
        const elapsed = (Date.now() - examStart) / 1000;
        $('#stopwatch').html('⏰ ' + formatDuration(elapsed));
    }, 1000);

    // ── Submit handler ─────────────────────────────────────────────────────
    const resultModal = new bootstrap.Modal(document.getElementById('resultModal'));

    $('.reading-form').on('submit', function(e) {
        e.preventDefault();

        const $btn = $('#submitAnswersBtn');
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat me-1"></i>Submitting…');

        $.ajax({
            url: "{{ route('exam.ielts.reading.submit') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function(response) {
                clearInterval(stopwatchTimer);
                populateResultModal(response.summary || response);
                resultModal.show();
            },
            error: function() {
                $btn.prop('disabled', false).html(originalHtml);
                Swal.fire({
                    icon: 'error',
                    title: 'Submission failed',
                    text: 'An error occurred while submitting your answers. Please try again.',
                });
            }
        });
    });

    function populateResultModal(s) {
        $('#resBand').text(s.band_score ?? '—');
        $('#resExam').text(s.exam_name ?? '—');
        $('#resModule').text(s.module_name ?? '—');
        $('#resTotal').text(s.total_questions ?? '—');
        $('#resCorrect').text(s.correct_answers ?? '—');
        $('#resTime').text(formatDuration(s.time_elapsed_seconds ?? 0));
    }

    // ── OK → dashboard ─────────────────────────────────────────────────────
    $('#okBtn').on('click', function () {
        window.location.href = "{{ route('dashboard') }}";
    });

    // ── Exit button ────────────────────────────────────────────────────────
    $('#exitBtn').on('click', function () {
        if (document.body.classList.contains('review-mode')) {
            window.location.href = "{{ route('dashboard') }}";
            return;
        }
        if (confirm('Exit without submitting? Your answers will be lost.')) {
            window.location.href = "{{ route('dashboard') }}";
        }
    });

    // ── Show Details → reveal correctness ──────────────────────────────────
    $('#showDetailsBtn').on('click', function () {
        resultModal.hide();
        enterReviewMode();
    });

    function enterReviewMode() {
        document.body.classList.add('review-mode');

        // Disable all inputs
        $('.reading-form :input').not('button').prop('disabled', true);
        $('#submitAnswersBtn').prop('disabled', true)
                              .html('<i class="bi bi-lock-fill me-1"></i>Submitted');

        // Iterate each rendered option and mark
        $('.exam-option').each(function () {
            const $opt = $(this);
            const $input = $opt.find('input');
            const isCorrectAnswer = $opt.data('is-correct') == 1 || $opt.data('is-correct') === '1';
            const userPicked = $input.is(':checked');

            if (isCorrectAnswer && userPicked) {
                $opt.addClass('user-correct');
                appendTag($opt, 'Your answer · Correct', 'tag-correct');
            } else if (!isCorrectAnswer && userPicked) {
                $opt.addClass('user-wrong');
                appendTag($opt, 'Your answer · Incorrect', 'tag-wrong');
            } else if (isCorrectAnswer && !userPicked) {
                $opt.addClass('is-correct-answer');
                appendTag($opt, 'Correct answer', 'tag-answer');
            }
        });

        // Fill-in-the-blanks review
        $('.fill-input').each(function () {
            const $inp = $(this);
            const userVal = ($inp.val() || '').trim().toLowerCase();
            const correctVal = ($inp.data('correct-text') || '').trim().toLowerCase();
            if (!userVal) return;
            if (userVal === correctVal) $inp.addClass('fill-correct');
            else                        $inp.addClass('fill-wrong');
        });

        // Scroll to top of form
        document.getElementById('reviewBanner').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function appendTag($opt, text, cls) {
        if ($opt.find('.review-tag').length) return;
        $opt.find('label').append(`<span class="review-tag ${cls}">${text}</span>`);
    }


    function showPart(partNumber) {
        document.querySelectorAll('.reading-part').forEach(part => {
            part.style.display = 'none';
        });
        document.getElementById('part-' + partNumber).style.display = 'flex';

        // Update active button
        document.querySelectorAll('.palette-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');
    }


    $(document).on('change', '.exam-option input', function () {

        const $input = $(this);
        const $container = $input.closest('.exam-option');

        // If radio → remove selection from siblings
        if ($input.attr('type') === 'radio') {
            const name = $input.attr('name');

            $('input[name="' + name + '"]').each(function () {
                $(this).closest('.exam-option').removeClass('selected');
            });

            $container.addClass('selected');
        }

        // If checkbox → toggle individually
        if ($input.attr('type') === 'checkbox') {
            if ($input.is(':checked')) {
                $container.addClass('selected');
            } else {
                $container.removeClass('selected');
            }
        }

    });

</script>