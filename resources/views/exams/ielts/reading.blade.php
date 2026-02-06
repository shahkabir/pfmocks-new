{{-- @extends('layouts.app') --}}

@section('title', 'IELTS Reading Test')

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
                'passage' => $question['passage'],
                'questions' => []
            ];
        }

        $grouped = collect($question['options'])->groupBy('actual_question');

        foreach ($grouped as $actualQuestion => $options) {
            $parts[$part]['questions'][$actualQuestion] = $options;
        }
    }

    // dd($parts);

@endphp


<div class="exam-body">
    @foreach($parts as $partNumber => $partData)
            <div class="exam-body reading-part"
                id="part-{{ $partNumber }}"
                style="{{ $partNumber === array_key_first($parts) ? '' : 'display:none' }}">

                {{-- LEFT: PASSAGE --}}
                <div class="reading-panel">
                    <h5>Part {{ $partNumber }}</h5>
                    <p><strong>{{ $partData['header'] }}</strong></p>
                    <div>
                        {!! nl2br(e($partData['passage'])) !!}
                    </div>
                </div>

                {{-- RIGHT: QUESTIONS --}}
                <div class="question-panel">
                    @php $qNo = 1; @endphp

                    @foreach($partData['questions'] as $actualQuestion => $options)
                        <div class="mb-4">

                            <h6 class="mb-2">
                                {{ $qNo++ }}. {{ $actualQuestion }}
                            </h6>

                            @foreach($options as $option)
                                <div class="form-check mb-1">
                                    <input class="form-check-input"
                                        type="radio"
                                        name="answers[{{ md5($actualQuestion) }}]"
                                        value="{{ $option['id'] }}"
                                        id="option-{{ $option['id'] }}">

                                    <label class="form-check-label"
                                        for="option-{{ $option['id'] }}">
                                        {{ $option['option_text'] }}
                                    </label>
                                </div>
                            @endforeach

                        </div>
                    @endforeach
                </div>
            </div>
    @endforeach
</div>

{{-- ================= Parts ================= --}}
<div class="mb-3">
    @foreach($parts as $partNumber => $partData)
        <button class="btn btn-sm btn-primary"
                onclick="showPart({{ $partNumber }})">
            Part {{ $partNumber }}
        </button>
    @endforeach
</div>

{{-- ================= QUESTION PALETTE ================= --}}
<div class="question-palette">
    @for($i = 1; $i <= 13; $i++)
        <button class="palette-btn {{ $i === 1 ? 'active' : '' }}">
            {{ $i }}
        </button>
    @endfor
</div>



{{-- ================= FOOTER ================= --}}
<div class="exam-footer">
    <div class="current-time">
        ⏰ 15:49
    </div>

    <div class="footer-right">
        <button class="btn btn-outline-secondary btn-sm">Exit</button>
    </div>
</div>

{{-- @endsection --}}
<script>
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
</script>