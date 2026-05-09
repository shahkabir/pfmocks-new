@extends('layouts.app')

@section('title', 'SOP Evaluation')

@section('content')
@php
    /** @var \App\Models\Evaluation\Evaluation $evaluation */
    /** @var \App\Models\Sop\SopSubmission|null $submission */
    $isReview = $submission?->service_type === 'review';
@endphp

<style>
    .sop-eval-card {
        border: 1px solid #e9ecef; border-radius: 12px;
        background: #fff; padding: 18px 20px; margin-bottom: 14px;
    }
    .sop-info-row {
        display: flex; align-items: center; gap: 8px;
        font-size: .9rem; color: #495057; padding: 6px 0;
        border-bottom: 1px dashed #e9ecef;
    }
    .sop-info-row:last-of-type { border-bottom: none; }
    .sop-info-row .label { color:#6c757d; font-weight:500; min-width:160px; }
    .sop-info-row .value { color:#212529; font-weight:600; }

    .file-pill {
        display:inline-flex; align-items:center; gap:8px;
        padding:8px 14px; background:#f8f9fa;
        border:1px solid #e9ecef; border-radius:10px;
        text-decoration:none; color:#212529;
        transition: background-color .15s, border-color .15s;
    }
    .file-pill:hover {
        background:#e9f2ff; border-color:#9ec5fe; color:#0a58ca;
    }
    .file-pill .bi { font-size:1.2rem; }
    .file-pill .small { color:#6c757d; }

    .feedback-card {
        background:linear-gradient(135deg,#fff7e6, #fff);
        border:1px solid #f5e9c8; border-radius:12px;
        padding:18px; margin-bottom:18px;
    }

    .chip {
        display:inline-flex; align-items:center; gap:5px;
        font-size:11px; font-weight:600; padding:3px 10px;
        border-radius:999px; letter-spacing:.3px; text-transform:uppercase;
        border:1px solid transparent;
    }
    .chip-review { background:#cff4fc; color:#055160; border-color:#9eeaf9; }
    .chip-new    { background:#d1e7dd; color:#0a5934; border-color:#a3cfbb; }
</style>

{{ dd($submission) }}
<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-file-earmark-text-fill text-primary me-2"></i>
                SOP Evaluation
                <span class="chip chip-{{ $submission?->service_type }} ms-1">
                    {{ $isReview ? 'Review existing' : 'Write new' }}
                </span>
            </h4>
            <small class="text-muted">
                Student: <strong>{{ $student->name }}</strong> ({{ $student->email }}) ·
                {{ $module->exam?->name }} — {{ $module->name }}
            </small>
        </div>
        <a href="{{ route('evaluator.evaluations.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
    </div>

    @if(!$submission)
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            No SOP submission record was found for this student/module.
        </div>
    @else
    <div class="row g-3">
        {{-- LEFT: Submission details + provided files --}}
        <div class="col-lg-6">
            <div class="sop-eval-card">
                <h6 class="fw-bold text-primary mb-2">
                    <i class="bi bi-info-circle me-1"></i>Student's submission
                </h6>

                <div class="sop-info-row">
                    <span class="label">Service type</span>
                    <span class="value">{{ $isReview ? 'SOP Review (existing → refined)' : 'New SOP (from scratch)' }}</span>
                </div>
                <div class="sop-info-row">
                    <span class="label">Intended University</span>
                    <span class="value">{{ $submission->intended_university }}</span>
                </div>
                <div class="sop-info-row">
                    <span class="label">Country</span>
                    <span class="value">{{ $submission->country }}</span>
                </div>
                <div class="sop-info-row">
                    <span class="label">Submitted</span>
                    <span class="value">{{ $submission->created_at?->format('Y-m-d H:i') }}</span>
                </div>
            </div>

            <div class="sop-eval-card">
                <h6 class="fw-bold text-primary mb-2">
                    <i class="bi bi-paperclip me-1"></i>Files from the student
                </h6>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('sop.download', [$submission->id, 'resume']) }}"
                       class="file-pill" target="_blank">
                        <i class="bi bi-file-earmark-person-fill text-primary"></i>
                        <div>
                            <div>Résumé</div>
                            <div class="small">click to download</div>
                        </div>
                    </a>

                    @if($submission->original_sop_path)
                        <a href="{{ route('sop.download', [$submission->id, 'original-sop']) }}"
                           class="file-pill" target="_blank">
                            <i class="bi bi-file-earmark-text-fill text-info"></i>
                            <div>
                                <div>Existing SOP</div>
                                <div class="small">click to download</div>
                            </div>
                        </a>
                    @endif
                </div>

                @if(!$submission->original_sop_path && $isReview)
                    <div class="alert alert-warning small mt-2 mb-0">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        The student selected "Review" but didn't attach an existing SOP. Use the résumé to draft from scratch.
                    </div>
                @endif
            </div>

            @if($submission->final_sop_path)
                <div class="sop-eval-card" style="border-color:#a3cfbb;background:#e8f5ee;">
                    <h6 class="fw-bold text-success mb-2">
                        <i class="bi bi-check-circle-fill me-1"></i>Final SOP already uploaded
                    </h6>
                    <a href="{{ route('sop.download', [$submission->id, 'final-sop']) }}"
                       class="file-pill" target="_blank">
                        <i class="bi bi-file-earmark-arrow-down-fill text-success"></i>
                        <div>
                            <div>Final SOP</div>
                            <div class="small">submitting a new file replaces this</div>
                        </div>
                    </a>
                </div>
            @endif
        </div>

        {{-- RIGHT: Evaluator's deliverable --}}
        <div class="col-lg-6">
            <h6 class="fw-bold text-primary mb-2">
                <i class="bi bi-cloud-upload-fill me-1"></i>Your deliverable
            </h6>

            <form method="POST"
                  action="{{ route('evaluator.evaluations.update', $evaluation->id) }}"
                  enctype="multipart/form-data" id="sopFeedbackForm">
                @csrf

                <div class="feedback-card">
                    <label class="form-label fw-semibold">
                        Final SOP file (PDF / DOC) <span class="text-danger">*</span>
                    </label>
                    <input type="file" name="final_sop" class="form-control"
                           accept=".pdf,.doc,.docx"
                           {{ $submission->final_sop_path ? '' : 'required' }}>
                    <div class="form-text">
                        @if($submission->final_sop_path)
                            Upload a new file to replace the current one (or leave blank to just edit feedback).
                        @else
                            This will be sent to the student as the deliverable for this service.
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Notes for the student (optional)</label>
                    <textarea name="feedback_text" rows="6" class="form-control" maxlength="8000"
                              placeholder="e.g. Strengthened the second paragraph; restructured the conclusion to focus on research alignment with the lab…">{{ old('feedback_text', $evaluation->feedback_text) }}</textarea>
                </div>

                {{-- Hidden overall band so the result modal still works (use 0 for SOP) --}}
                <input type="hidden" name="overall" value="0">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success" id="sopSubmitBtn">
                        <i class="bi bi-send-fill me-1"></i>
                        {{ $evaluation->isCompleted() ? 'Update' : 'Submit' }} Final SOP
                    </button>
                    <a href="{{ route('evaluator.evaluations.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

<script>
$(function () {
    $('#sopFeedbackForm').on('submit', function (e) {
        e.preventDefault();

        const $btn = $('#sopSubmitBtn');
        const orig = $btn.html();
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat me-1"></i>Saving…');

        const fd = new FormData(this);

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
                let msg = 'Failed to save.';
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
