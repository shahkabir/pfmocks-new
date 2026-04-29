@php
    /** @var \App\Models\Evaluation\Evaluation $evaluation */
    $bands  = $evaluation->band_scores ?? [];
    $overall = $evaluation->overallBand();

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
    $isSpeaking = $evaluation->module_type === 'speaking';
    $criteria   = $isSpeaking ? $speakingCriteria : $writingCriteria;
@endphp

<style>
    .eval-feedback-panel {
        margin: 14px;
        background: linear-gradient(135deg, #fff7e6, #fff);
        border: 1px solid #f5e9c8;
        border-radius: 12px;
        padding: 18px 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
    }
    .eval-feedback-panel h6 {
        font-weight: 700;
        color: #b45309;
        margin: 0 0 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .eval-feedback-panel .band-row {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 1px dashed #f5e9c8;
        font-size: .92rem;
    }
    .eval-feedback-panel .band-row:last-of-type { border-bottom: none; }
    .eval-feedback-panel .band-row strong { color: #5a3e00; }
    .eval-feedback-panel .overall-card {
        background: linear-gradient(135deg, #d1f4e0, #fffbf2);
        border: 1px solid #a3cfbb;
        border-radius: 10px;
        padding: 12px 16px;
        text-align: center;
        margin-top: 10px;
    }
    .eval-feedback-panel .overall-card .num {
        font-size: 2rem;
        font-weight: 800;
        color: #0a5934;
        line-height: 1;
    }
    .eval-feedback-panel .overall-card .lbl {
        font-size: .72rem; letter-spacing:.5px; text-transform:uppercase;
        color: #0a5934; font-weight:700; margin-bottom:2px;
    }
    .eval-feedback-panel .feedback-text {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 14px;
        white-space: pre-wrap;
        font-size: .92rem;
        line-height: 1.55;
        color: #212529;
    }
    .eval-feedback-panel audio { width: 100%; max-width: 480px; }
</style>

<div class="eval-feedback-panel">
    <h6>
        <i class="bi bi-clipboard-check-fill"></i>
        Evaluator's Feedback
        @if($evaluation->evaluator?->name ?? null)
            <span class="text-muted small fw-normal">— by {{ $evaluation->evaluator->name }}</span>
        @endif
    </h6>

    <div class="row g-3">
        <div class="col-md-7">
            @foreach($criteria as $key => $label)
                <div class="band-row">
                    <span>{{ $label }}</span>
                    <strong>{{ isset($bands[$key]) ? number_format((float)$bands[$key], 1) : '—' }}</strong>
                </div>
            @endforeach
        </div>

        <div class="col-md-5">
            @if($overall !== null)
                <div class="overall-card">
                    <div class="lbl"><i class="bi bi-star-fill"></i> Overall Band</div>
                    <div class="num">{{ number_format($overall, 1) }}</div>
                </div>
            @endif
        </div>
    </div>

    @if($evaluation->feedback_text)
        <div class="mt-3">
            <div class="small text-muted fw-semibold mb-1">Written feedback:</div>
            <div class="feedback-text">{{ $evaluation->feedback_text }}</div>
        </div>
    @endif

    @if($evaluation->feedback_audio_path)
        <div class="mt-3">
            <div class="small text-muted fw-semibold mb-1">
                <i class="bi bi-mic-fill me-1"></i>Voice feedback:
            </div>
            <audio controls src="{{ asset($evaluation->feedback_audio_path) }}"></audio>
        </div>
    @endif
</div>
