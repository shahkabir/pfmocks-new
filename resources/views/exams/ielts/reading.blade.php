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
<div class="exam-body">

    {{-- LEFT: READING PASSAGE --}}
    <div class="reading-panel">
        <h5>Part 1</h5>
        <p><strong>Read the text and answer questions 1–13.</strong></p>

        <h6 class="mt-3">The life and work of Marie Curie</h6>

        <p>
            Marie Curie is probably the most famous woman scientist who has ever lived.
            Born Maria Sklodowska in Poland in 1867, she is famous for her work on radioactivity,
            and was twice a winner of the Nobel Prize.
        </p>

        <p>
            With her husband Pierre Curie, and Henri Becquerel, she was awarded the 1903 Nobel Prize
            for Physics, and was then sole winner of the 1911 Nobel Prize for Chemistry.
        </p>

        <p>
            From childhood, Marie was remarkable for her prodigious memory, and at the age of 16
            won a gold medal on completion of her secondary education.
        </p>

        <p>
            In 1891 Marie went to Paris and began to study at the Sorbonne. She often worked far into
            the night and lived on little more than bread and tea.
        </p>

        <p>
            Their marriage in 1895 marked the start of a partnership that was soon to achieve
            results of world significance.
        </p>
    </div>

    {{-- RIGHT: QUESTIONS --}}
    <div class="question-panel">
        <h6>Questions 1–6</h6>
        <p class="mb-3">
            Choose <strong>TRUE</strong>, <strong>FALSE</strong> or <strong>NOT GIVEN</strong>.
        </p>

        <div class="question">
            <h6>1. Marie Curie’s husband was a joint winner of both Marie’s Nobel Prizes.</h6>
            <label><input type="radio" name="q1"> TRUE</label>
            <label><input type="radio" name="q1"> FALSE</label>
            <label><input type="radio" name="q1"> NOT GIVEN</label>
        </div>

        <div class="question">
            <h6>2. Marie became interested in science when she was a child.</h6>
            <label><input type="radio" name="q2"> TRUE</label>
            <label><input type="radio" name="q2"> FALSE</label>
            <label><input type="radio" name="q2"> NOT GIVEN</label>
        </div>

        <div class="question">
            <h6>3. Marie was able to attend the Sorbonne because of her sister’s financial help.</h6>
            <label><input type="radio" name="q3"> TRUE</label>
            <label><input type="radio" name="q3"> FALSE</label>
            <label><input type="radio" name="q3"> NOT GIVEN</label>
        </div>
    </div>

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