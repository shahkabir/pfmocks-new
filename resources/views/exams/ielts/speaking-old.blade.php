
{{-- @extends('layouts.app') --}}

@section('title', 'IELTS Speaking Test')

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
        display: flex;
        justify-content: space-between;
        align-items: center;
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
    }

    .top-icons i {
        margin-left: 15px;
        color: #555;
        cursor: pointer;
    }

    /* ===== CONTENT ===== */
    .exam-content {
        background: #fff;
        height: calc(100vh - 220px);
        overflow-y: auto;
        padding: 20px;
    }

    .speaking-part {
        border-bottom: 1px solid #eee;
        margin-bottom: 25px;
        padding-bottom: 20px;
    }

    .speaking-part h5 {
        margin-bottom: 15px;
        font-weight: 600;
    }

    .question-item {
        margin-bottom: 15px;
        padding: 10px 15px;
        background: #f9fafb;
        border-radius: 4px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .record-btn {
        white-space: nowrap;
    }

    /* ===== FOOTER ===== */
    .exam-footer {
        background: #ffffff;
        border-top: 1px solid #ddd;
        padding: 10px 15px;
        display: flex;
        align-items: center;
    }

    .time-left {
        font-weight: bold;
        font-size: 14px;
    }

    .footer-right {
        margin-left: auto;
    }
</style>

{{-- ================= TOP BAR ================= --}}
<div class="exam-topbar">
    <div class="exam-topbar-left">
        <div class="exam-logo">IELTS</div>
        <div class="candidate-info">
            <strong>48887345</strong><br>
            <span>14 minutes remaining</span>
        </div>
    </div>

    <div class="top-icons">
        <i class="fas fa-wifi"></i>
        <i class="far fa-bell"></i>
        <i class="fas fa-bars"></i>
        <i class="far fa-edit"></i>
    </div>
</div>

{{-- ================= MAIN CONTENT ================= --}}
<div class="exam-content">

    {{-- ===== PART 1 ===== --}}
    <div class="speaking-part">
        <h5>Part 1 – Introduction & Interview</h5>

        <div class="question-item">
            <div>1. Can you tell me about your hometown?</div>
            <button class="btn btn-outline-danger btn-sm record-btn">
                🎤 Record
            </button>
        </div>

        <div class="question-item">
            <div>2. Do you work or are you a student?</div>
            <button class="btn btn-outline-danger btn-sm record-btn">
                🎤 Record
            </button>
        </div>

        <div class="question-item">
            <div>3. What do you like to do in your free time?</div>
            <button class="btn btn-outline-danger btn-sm record-btn">
                🎤 Record
            </button>
        </div>
    </div>

    {{-- ===== PART 2 ===== --}}
    <div class="speaking-part">
        <h5>Part 2 – Long Turn</h5>

        <div class="mb-3">
            <strong>Describe a memorable journey you have taken.</strong>
            <ul>
                <li>where you went</li>
                <li>who you went with</li>
                <li>what you did</li>
                <li>and explain why it was memorable</li>
            </ul>
            <p><em>You will have 1 minute to prepare and up to 2 minutes to speak.</em></p>
        </div>

        <button class="btn btn-outline-danger btn-sm record-btn">
            🎤 Record Answer
        </button>
    </div>

    {{-- ===== PART 3 ===== --}}
    <div class="speaking-part">
        <h5>Part 3 – Discussion</h5>

        <div class="question-item">
            <div>1. Why do people like to travel to different countries?</div>
            <button class="btn btn-outline-danger btn-sm record-btn">
                🎤 Record
            </button>
        </div>

        <div class="question-item">
            <div>2. How has travel changed in the last few decades?</div>
            <button class="btn btn-outline-danger btn-sm record-btn">
                🎤 Record
            </button>
        </div>

        <div class="question-item">
            <div>3. Do you think international travel will increase in the future?</div>
            <button class="btn btn-outline-danger btn-sm record-btn">
                🎤 Record
            </button>
        </div>
    </div>

</div>

{{-- ================= FOOTER ================= --}}
<div class="exam-footer">
    <div class="time-left">
        ⏱ Time left: 14:00
    </div>

    <div class="footer-right">
        <button class="btn btn-success btn-sm">
            Submit Speaking Test
        </button>
    </div>
</div>

{{-- @endsection --}}
