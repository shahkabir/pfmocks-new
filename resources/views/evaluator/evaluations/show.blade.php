@extends('layouts.app')

@section('title', 'Evaluation — ' . ucfirst($module->module_type))

@section('content')
@php
    $isWriting  = $module->module_type === 'writing';
    $isSpeaking = $module->module_type === 'speaking';
    $bandScores = $evaluation->band_scores ?? [];

    // NOTE: do NOT keyBy('question_id') — speaking stores one answer row per
    // prompt (per question_option_id), and several prompts share a question_id,
    // so keying by question_id would silently drop all but the last recording.

    $writingCriteria = [
        'task_achievement'   => 'Task Achievement',
        'coherence_cohesion' => 'Coherence and Cohesion',
        'lexical_resource'   => 'Lexical Resource',
        'grammatical_range'  => 'Grammatical Range and Accuracy',
    ];
    $speakingCriteria = [
        'fluency_coherence'  => 'Fluency and Coherence',
        'lexical_resource'   => 'Lexical Resource',
        'grammatical_range'  => 'Grammatical Range and Accuracy',
        'pronunciation'      => 'Pronunciation',
    ];
    $criteria = $isSpeaking ? $speakingCriteria : $writingCriteria;
@endphp

<style>
    .answer-card {
        border:1px solid #e9ecef; border-radius:10px;
        background:#fff; padding:16px 18px; margin-bottom:14px;
    }
    .answer-card .answer-q {
        font-weight:600; color:#212529; font-size:.95rem; margin-bottom:8px;
    }
    .answer-card .answer-body {
        background:#f8f9fa; border:1px solid #e9ecef; border-radius:8px;
        padding:12px 14px; white-space:pre-wrap; line-height:1.55; font-size:.95rem;
    }
    .audio-block { padding:8px 0; }
    .band-input { max-width:120px; }
    .feedback-card {
        background:linear-gradient(135deg,#fff7e6, #fff);
        border:1px solid #f5e9c8; border-radius:12px;
        padding:18px; margin-bottom:18px;
    }
    .recorder-pill {
        display:inline-flex; align-items:center; gap:8px; padding:6px 12px;
        background:#f8f9fa; border:1px solid #dee2e6; border-radius:999px;
        font-size:.82rem;
    }
    .rec-dot { width:10px; height:10px; border-radius:50%; background:#adb5bd; }
    .rec-dot.recording { background:#dc3545; animation:pulse 1.2s infinite; }
    @keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:.45;} }
</style>
{{-- {{ dd($answersByQ, $evaluation, $answers, $student, $module) }} --}}

<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-{{ $isSpeaking ? 'mic-fill' : 'pencil-fill' }} text-primary me-2"></i>
                {{ ucfirst($module->module_type) }} Evaluation
            </h4>
            <small class="text-muted">
                Student: <strong>{{ $student->name }}</strong> ·
                {{ $module->exam?->name }} — {{ $module->name }}
            </small>
        </div>
        <a href="{{ route('evaluator.evaluations.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
    </div>

    <div class="row g-3">
        {{-- ============ LEFT: STUDENT ANSWERS ============ --}}
        <div class="col-lg-7">
            <h6 class="fw-bold text-primary mb-2">
                <i class="bi bi-file-earmark-text me-1"></i>Student's submission
            </h6>

            {{-- {{ dd($answers) }} --}}

            @forelse($answers as $answer)
                @php
                    $q = $answer->question;
                @endphp
                <div class="answer-card">
                    <div class="answer-q">
                        Q{{ $loop->iteration }}.
                        @if($q?->question_header)
                            {!! $q->question_header !!}
                        @endif
                    </div>
                    @if($q?->passage_instruction)
                        <div class="text-muted small mb-2">{!! $q->passage_instruction !!}</div>
                    @endif
                    @if($q?->passage)
                        <div class="text-muted small mb-2">{!! $q->passage !!}</div>
                    @endif

                    @if($isWriting)
                        <div class="answer-body">{{ $answer->answer ?: '— no answer submitted —' }}</div>
                        @if($answer->answer)
                            <div class="small text-muted mt-1">
                                Word count:
                                <strong>{{ str_word_count(strip_tags($answer->answer)) }}</strong>
                            </div>
                        @endif
                    @elseif($isSpeaking)
                        @php
                            // speaking answer.answer holds the audio path. Normalize:
                            // older rows may lack the "storage/" prefix.
                            $audioPath = $answer->answer;
                            if ($audioPath && !\Illuminate\Support\Str::startsWith($audioPath, ['http://', 'https://', 'storage/'])) {
                                $audioPath = 'storage/' . ltrim($audioPath, '/');
                            }
                        @endphp
                        @if(!empty($audioPath))
                            <div class="audio-block">
                                <audio controls src="{{ asset($audioPath) }}" style="width:100%;"></audio>
                            </div>
                        @else
                            <div class="answer-body">— no recording on file —</div>
                        @endif
                    @else
                        <div class="answer-body">{{ $answer->answer ?: '— no answer submitted —' }}</div>
                    @endif
                </div>
            @empty
                <div class="alert alert-warning small">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    No answers found for this attempt.
                </div>
            @endforelse
        </div>

        {{-- ============ RIGHT: FEEDBACK FORM ============ --}}
        <div class="col-lg-5">
            <h6 class="fw-bold text-primary mb-2">
                <i class="bi bi-chat-square-text-fill me-1"></i>Your evaluation
            </h6>

            <form method="POST"
                  action="{{ route('evaluator.evaluations.update', $evaluation->id) }}"
                  enctype="multipart/form-data" id="feedbackForm">
                @csrf

                {{-- IELTS criteria scores --}}
                <div class="feedback-card">
                    <div class="fw-semibold mb-2">IELTS band scores (0–9, half-step)</div>
                    @foreach($criteria as $key => $label)
                        <div class="row align-items-center mb-2">
                            <label class="col-7 form-label mb-0 small">{{ $label }}</label>
                            <div class="col-5">
                                <input type="number" name="{{ $key }}" min="0" max="9" step="0.5"
                                       class="form-control form-control-sm band-input"
                                       value="{{ old($key, $bandScores[$key] ?? '') }}">
                            </div>
                        </div>
                    @endforeach

                    <hr class="my-2">

                    <div class="row align-items-center">
                        <label class="col-7 form-label mb-0 fw-bold">
                            <i class="bi bi-star-fill text-warning me-1"></i>Overall Band
                        </label>
                        <div class="col-5">
                            <input type="number" name="overall" min="0" max="9" step="0.5"
                                   class="form-control form-control-sm band-input fw-bold"
                                   value="{{ old('overall', $bandScores['overall'] ?? '') }}"
                                   placeholder="Auto-avg if blank">
                        </div>
                    </div>
                </div>

                {{-- Typed feedback --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Typed Feedback</label>
                    <textarea name="feedback_text" rows="6" class="form-control"
                              placeholder="Provide detailed feedback on the student's performance…"
                              maxlength="8000">{{ old('feedback_text', $evaluation->feedback_text) }}</textarea>
                </div>

                {{-- Voice feedback (Recorded) --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold small">
                        Voice Feedback (optional)
                    </label>

                    <div class="d-flex gap-2 align-items-center mb-2 flex-wrap">
                        <button type="button" id="recStartBtn" class="btn btn-sm btn-danger">
                            <i class="bi bi-record-circle"></i> Record
                        </button>
                        <button type="button" id="recStopBtn" class="btn btn-sm btn-secondary" disabled>
                            <i class="bi bi-stop-fill"></i> Stop
                        </button>
                        <span class="recorder-pill">
                            <span class="rec-dot" id="recDot"></span>
                            <span id="recStatus">Idle</span>
                        </span>
                    </div>

                    <audio id="recPreview" controls class="w-100 mb-2" style="display:none;"></audio>

                    @if($evaluation->feedback_audio_path)
                        <div class="small text-muted">
                            <strong>Existing recording:</strong>
                            <audio controls src="{{ asset($evaluation->feedback_audio_path) }}"
                                   style="width:100%;margin-top:4px;"></audio>
                            <div>Submitting a new recording will replace this.</div>
                        </div>
                    @endif

                    {{-- Holds the recorded blob; appended to FormData on submit --}}
                    <input type="hidden" id="audioReady" value="0">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success" id="submitBtn">
                        <i class="bi bi-send-fill me-1"></i>
                        {{ $evaluation->isCompleted() ? 'Update' : 'Submit' }} Evaluation
                    </button>
                    <a href="{{ route('evaluator.evaluations.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(function () {
    let mediaRecorder = null;
    let chunks        = [];
    let recordedBlob  = null;

    const $startBtn  = $('#recStartBtn');
    const $stopBtn   = $('#recStopBtn');
    const $recDot    = $('#recDot');
    const $recStatus = $('#recStatus');
    const $preview   = $('#recPreview');

    $startBtn.on('click', async function () {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);
            chunks = [];

            mediaRecorder.ondataavailable = e => chunks.push(e.data);

            mediaRecorder.onstop = () => {
                recordedBlob = new Blob(chunks, { type: 'audio/webm' });
                $preview.attr('src', URL.createObjectURL(recordedBlob)).show();
                $('#audioReady').val('1');
                stream.getTracks().forEach(t => t.stop());
            };

            mediaRecorder.start();
            $recDot.addClass('recording');
            $recStatus.text('Recording…');
            $startBtn.prop('disabled', true);
            $stopBtn.prop('disabled', false);
        } catch (err) {
            alert('Could not access microphone: ' + err.message);
        }
    });

    $stopBtn.on('click', function () {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
        }
        $recDot.removeClass('recording');
        $recStatus.text('Recorded — preview above');
        $startBtn.prop('disabled', false);
        $stopBtn.prop('disabled', true);
    });

    $('#feedbackForm').on('submit', function (e) {
        e.preventDefault();

        const $btn = $('#submitBtn');
        const orig = $btn.html();
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat me-1"></i>Saving…');

        const fd = new FormData(this);
        if (recordedBlob) {
            fd.append('feedback_audio', recordedBlob, 'feedback_' + Date.now() + '.webm');
        }

        $.ajax({
            url: this.action,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: fd,
            processData: false,
            contentType: false,
            success: function () {
                window.location = '{{ route("evaluator.evaluations.index") }}';
            },
            error: function (xhr) {
                let msg = 'Failed to save evaluation.';
                if (xhr.responseJSON?.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                } else if (xhr.responseJSON?.message) {
                    msg = xhr.responseJSON.message;
                }
                alert(msg);
                $btn.prop('disabled', false).html(orig);
            }
        });
    });
});
</script>
@endsection
