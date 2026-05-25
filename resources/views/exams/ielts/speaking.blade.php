{{-- @extends('layouts.app') --}}

@section('title', 'IELTS Speaking Test')

@include('layouts.header')

<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- @section('content') --}}
<style>
    body {
        background: #f4f6f9;
    }

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

    /* ===== CONTENT ===== */
    .exam-content {
        background: #fff;
        height: calc(100vh - 130px);
        overflow-y: auto;
        padding: 20px;
    }

    .exam-body {
        width: 100%;
    }

    .speaking-part {
        width: 100%;
    }

    .question-panel {
        width: 100%;
    }

    .question-block {
        margin-bottom: 25px;
    }

    /* ===== FOOTER ===== */
    .exam-footer {
        background: #ffffff;
        border-top: 1px solid #ddd;
        padding: 8px 16px;
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 12px;
    }

    .footer-left {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        justify-self: start;
    }

    .footer-center {
        display: flex;
        gap: 6px;
        justify-self: center;
    }

    .footer-right {
        display: flex;
        align-items: center;
        gap: 8px;
        justify-self: end;
    }

    /* Small numbered palette buttons */
    .palette-btn {
        width: 26px;
        height: 26px;
        border-radius: 4px;
        border: 1px solid #ced4da;
        background: #f8f9fa;
        color: #495057;
        font-size: 11px;
        font-weight: 600;
        padding: 0;
        line-height: 1;
        transition: background-color .12s, border-color .12s, color .12s;
    }
    .palette-btn:hover:not(.active) {
        background: #e9ecef;
        border-color: #adb5bd;
    }
    .palette-btn.active {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }

    /* Part buttons */
    .part-btn {
        font-weight: 600;
        padding: 5px 14px;
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

    /* Speaking record controls */
    .speaking-question {
        padding: 14px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
    }
    .speaking-question .btn {
        font-weight: 500;
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
        <span id="timer">{{ str_pad((int)$module->duration_minutes, 2, '0', STR_PAD_LEFT) }}:00</span>
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

{{-- {{ dd(get_defined_vars()) }} --}}

@php
    $parts = [];

    foreach ($questions as $question) {

        $part = $question['part_number']; //['group']

        if (!isset($parts[$part])) {

            $parts[$part] = [
                'header' => $question['question_header'],
                'passage' => $question['passage'],
                'part_audio_url' => $question['part_audio_url'],
                'part_image_url' => $question['part_image_url'],
                'blocks' => []
            ];
        }

        foreach ($question['group']['blocks'] as $block) {

            $blockKey = $block['id'];

            if (!isset($parts[$part]['blocks'][$blockKey])) {

                $parts[$part]['blocks'][$blockKey] = [
                    'instruction' => $block['instruction_text'],
                    'questions' => []
                ];
            }

            $optionsInBlock = collect($question['options'])
                ->whereIn('id', json_decode($block['question_option_ids'], true))
                ->groupBy('actual_question');

            foreach ($optionsInBlock as $actualQuestion => $options) {
                $parts[$part]['blocks'][$blockKey]['questions'][$actualQuestion] = $options;
            }
        }
    }
@endphp

{{-- ================= MAIN CONTENT ================= --}}
<form class="speaking-form">
@csrf
<div class="exam-content">

    <div class="exam-body">
        @php $qNo = 1; @endphp
        @foreach($parts as $partNumber => $partData)

                <div class="exam-body speaking-part"
                    id="part-{{ $partNumber }}"
                    style="{{ $partNumber === array_key_first($parts) ? '' : 'display:none' }}">

                    {{-- RIGHT: QUESTIONS --}}
                    <div class="question-panel">

                        @foreach($partData['blocks'] as $block)

                            {{-- INSTRUCTION (shown ONCE per block) --}}
                                <div class="alert alert-light mb-3 border-secondary">
                                    <b>{!! $block['instruction'] !!}</b>
                                </div>

                            {{-- QUESTIONS UNDER THIS INSTRUCTION --}}
                            @foreach($block['questions'] as $actualQuestion => $options)
                                <div class="mb-4 question-item" id="question-{{ $qNo }}"
                                     data-part="{{ $partNumber }}" data-qno="{{ $qNo }}">
                                    <h6 class="mb-2">
                                             @if(!str_contains($actualQuestion, '[[blank]]')
                                                && $options[0]['question_type'] != 'fill_in_blanks'
                                                && $options[0]['question_type'] != 'no_question')
                                                <strong>{{ $qNo }}&nbsp;{{ $actualQuestion }} </strong>
                                             @elseif($options[0]['question_type'] == 'no_question')
                                              @php $qNo--; @endphp
                                                {{ $actualQuestion }}
                                             @endif
                                    </h6>

                                    {{-- show question image if exists --}}
                                    @if(!empty($options[0]['question_image_path']))
                                        <div class="mb-3">
                                            <img src="{{ asset($options[0]['question_image_path']) }}"
                                            alt="Question Image" class="img-fluid"
                                            style="max-width: 400px; max-height: 400px;">
                                        </div>
                                    @endif

                                        @foreach($options as $option)

                                            @if($option['question_type'] == 'ielts_speaking')
                                                @php
                                                    // Speaking answers are keyed by option id (each prompt is its own option)
                                                    $recordedAudio = ($userFillAnswers ?? [])[$option['id']] ?? null;
                                                    // Normalize: older rows may lack the "storage/" prefix
                                                    if ($recordedAudio && !\Illuminate\Support\Str::startsWith($recordedAudio, ['http://', 'https://', 'storage/'])) {
                                                        $recordedAudio = 'storage/' . ltrim($recordedAudio, '/');
                                                    }
                                                @endphp

                                                <div class="speaking-question mb-3"
                                                     data-question-id="{{ $option['question_id'] }}"
                                                     data-option-id="{{ $option['id'] }}">

                                                    @if(empty($reviewMode))
                                                        <button type="button" class="btn btn-sm btn-primary start-btn">
                                                            <i class="bi bi-mic-fill me-1"></i>Start
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger stop-btn">
                                                            <i class="bi bi-stop-fill me-1"></i>Stop
                                                        </button>
                                                    @endif

                                                    <audio class="preview mt-2" controls
                                                        @if(!empty($reviewMode) && $recordedAudio) src="{{ asset($recordedAudio) }}" @endif>
                                                    </audio>

                                                    @if(!empty($reviewMode) && !$recordedAudio)
                                                        <div class="text-muted small mt-1">
                                                            <i class="bi bi-mic-mute me-1"></i>No recording was submitted for this question.
                                                        </div>
                                                    @endif
                                                </div>

                                                @php break; @endphp
                                            @endif

                                        @endforeach
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

</div>

{{-- ================= FOOTER ================= --}}
<div class="exam-footer">
    {{-- LEFT: Question navigation palette --}}
    <div class="footer-left">
        @for($i = 1; $i <= $qNo; $i++)
            <button type="button" class="palette-btn {{ $i === 1 ? 'active' : '' }}"
                    data-target="question-{{ $i }}">
                {{ $i }}
            </button>
        @endfor
    </div>

    {{-- CENTER: Part navigation --}}
    <div class="footer-center">
        @foreach($parts as $partNumber => $partData)
            <button type="button"
                    class="part-btn {{ $partNumber === array_key_first($parts) ? 'active' : '' }}"
                    data-part="{{ $partNumber }}"
                    onclick="showPart({{ $partNumber }}, this)">
                Part {{ $partNumber }}
            </button>
        @endforeach
    </div>

    {{-- RIGHT: Submit / Exit --}}
    <div class="footer-right">
        <button type="submit" id="submitAnswersBtn" class="btn-submit-exam">
            <i class="bi bi-check2-circle me-1"></i>Submit
        </button>
        <button type="button" id="exitBtn" class="btn-exit-exam">
            <i class="bi bi-box-arrow-right me-1"></i>Exit
        </button>
        <input type="hidden" name="module_id" value="{{ $module->id }}">
        <input type="hidden" name="exam_name" value="{{ $module->name }}">
        <input type="hidden" name="module_type" value="{{ $module->module_type }}">
    </div>
</div>
</form>

<script>
// ── Review mode bootstrap (dashboard "Show Result") ──
@if(!empty($reviewMode))
$(function () {
    $('.speaking-form :input').not('button').prop('disabled', true);
    $('#submitAnswersBtn').prop('disabled', true)
                          .html('<i class="bi bi-lock-fill me-1"></i>Submitted');
    $('.start-btn, .stop-btn').prop('disabled', true);
    $('#timer-pill').removeClass('warning');
    $('#timer').text('—');
});
@endif

let recorders = {};

@if(empty($reviewMode))
{{-- Recorder is only wired during a live exam — in review mode the Start/Stop
     buttons don't exist and the recordings are simply played back. --}}
document.querySelectorAll('.speaking-question').forEach(container => {

    const startBtn = container.querySelector('.start-btn');
    const stopBtn = container.querySelector('.stop-btn');
    const audioPreview = container.querySelector('.preview');

    if (!startBtn || !stopBtn) return;

    let mediaRecorder;
    let chunks = [];

    navigator.mediaDevices.getUserMedia({ audio: true }).then(stream => {

        mediaRecorder = new MediaRecorder(stream);

        mediaRecorder.ondataavailable = e => chunks.push(e.data);

        mediaRecorder.onstop = () => {
            const blob = new Blob(chunks, { type: 'audio/webm' });
            chunks = [];

            audioPreview.src = URL.createObjectURL(blob);

            // Each prompt is a distinct option — send its option id so recordings
            // don't overwrite each other server-side.
            uploadAudio(blob, container.dataset.questionId, container.dataset.optionId, container);
        };

        startBtn.onclick = () => mediaRecorder.start();
        stopBtn.onclick = () => mediaRecorder.stop();
    });
});
@endif

function uploadAudio(blob, questionId, optionId, container) {
    const moduleId = document.querySelector('input[name="module_id"]')?.value;

    const formData = new FormData();
    formData.append('audio', blob, 'speaking.webm');
    formData.append('question_id', questionId);
    formData.append('question_option_id', optionId);
    if (moduleId) formData.append('module_id', moduleId);

    // Mark this prompt as "uploading" so the final submit can warn if not done
    if (container) container.dataset.uploadState = 'uploading';

    fetch('{{ route('exam.ielts.speaking.upload_audio') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
        if (container) container.dataset.uploadState = ok ? 'done' : 'failed';
        if (!ok) {
            alert('Recording upload failed: ' + (data.message || 'please re-record this answer.'));
        }
    })
    .catch(() => {
        if (container) container.dataset.uploadState = 'failed';
        alert('Recording upload failed due to a network error. Please re-record this answer.');
    });
}

// True when the submission is triggered by the timer running out (not a manual click)
let autoSubmitMode = false;
const isReviewMode = {{ !empty($reviewMode) ? 'true' : 'false' }};

// ── Final "Submit Speaking Test" → finalize endpoint ───────────────────────
$('.speaking-form').on('submit', function (e) {
    e.preventDefault();

    // Don't finalize while a recording is still uploading (manual submit only —
    // auto-submit on time-up proceeds regardless).
    if (!autoSubmitMode) {
        const stillUploading = document.querySelector('.speaking-question[data-upload-state="uploading"]');
        if (stillUploading) {
            alert('A recording is still uploading. Please wait a moment and submit again.');
            return;
        }
        const failed = document.querySelector('.speaking-question[data-upload-state="failed"]');
        if (failed && !confirm('One or more recordings failed to upload. Submit anyway?')) {
            return;
        }
    }

    const $btn = $('#submitAnswersBtn');
    const orig = $btn.html();
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat me-1"></i>Submitting…');

    $.ajax({
        url: "{{ route('exam.ielts.speaking.submit') }}",
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        data: $(this).serialize(),
        success: function (res) {
            if (autoSubmitMode && typeof Swal !== 'undefined') {
                // Time-up auto-save — student MUST click OK, then the tab closes.
                Swal.fire({
                    icon: 'success',
                    title: "Time's up!",
                    text: 'Your allotted time has finished. Your answers were saved automatically.',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then(function () {
                    window.close();
                    document.body.innerHTML =
                        '<div style="display:flex;align-items:center;justify-content:center;'
                      + 'height:100vh;font-family:sans-serif;font-size:1.1rem;color:#198754;'
                      + 'text-align:center;padding:24px;">'
                      + '<div><i class="bi bi-check-circle-fill"></i><br>'
                      + 'Your answers were saved. You may now close this tab.</div></div>';
                });
                return;
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Submitted',
                    text: res.message || 'Speaking test submitted. You will be notified once an evaluator reviews it.',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                }).then(() => { window.location.href = "{{ route('dashboard') }}"; });
            } else {
                alert(res.message || 'Submitted');
                window.location.href = "{{ route('dashboard') }}";
            }
        },
        error: function (xhr) {
            $btn.prop('disabled', false).html(orig);
            alert(xhr.responseJSON?.message || 'Submission failed. Please try again.');
        }
    });
});

// Fired once when the countdown hits zero — reuses the form's submit AJAX.
function triggerAutoSubmit() {
    if (isReviewMode || autoSubmitMode) return;
    autoSubmitMode = true;
    $('.speaking-form').trigger('submit');
}

// ── Part switcher ──────────────────────────────────────────────────────────
function showPart(partNumber, btn) {
    document.querySelectorAll('.speaking-part').forEach(part => {
        part.style.display = 'none';
    });
    document.getElementById('part-' + partNumber).style.display = 'flex';

    // Update active part button
    document.querySelectorAll('.part-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
}

// ── Question palette jump ──────────────────────────────────────────────────
$(document).on('click', '.palette-btn', function () {
    const targetId = $(this).data('target');
    const $target  = $('#' + targetId);
    if (!$target.length) return;

    const part = $target.data('part');
    $('.speaking-part').hide();
    $('#part-' + part).show();

    $('html, body').animate({ scrollTop: $target.offset().top - 100 }, 300);

    $('.palette-btn').removeClass('active');
    $(this).addClass('active');
});

// ── Countdown timer ────────────────────────────────────────────────────────
let seconds = {{ (int)$module->duration_minutes * 60 }};
const $timerPill = document.getElementById('timer-pill');
const $timerText = document.getElementById('timer');

// Don't start the countdown in review mode — the exam is already over.
const countdownInterval = @if(!empty($reviewMode)) null @else setInterval(() => {
    if (seconds <= 0) {
        clearInterval(countdownInterval);
        return;
    }
    seconds--;
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    $timerText.innerText = `${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;

    if (seconds <= 300) $timerPill.classList.add('warning');

    // Time's up → auto-save the answers
    if (seconds <= 0) {
        clearInterval(countdownInterval);
        $timerText.innerText = '00:00';
        triggerAutoSubmit();
    }
}, 1000) @endif;

// ── Exit button ────────────────────────────────────────────────────────────
$('#exitBtn').on('click', function () {
    if (confirm('Exit without submitting? Your answers will be lost.')) {
        window.location.href = "{{ route('dashboard') }}";
    }
});
</script>

{{-- @endsection --}}
