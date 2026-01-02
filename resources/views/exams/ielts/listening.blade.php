{{-- @extends('layouts.app') --}}

{{-- @section('title', 'IELTS Listening Test') --}}

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
        height: calc(100vh - 260px);
        overflow-y: auto;
        padding: 20px;
    }

    .question-block {
        margin-bottom: 25px;
    }

    .question-block input {
        width: 120px;
        display: inline-block;
        margin: 0 5px;
        text-align: center;
    }

    /* ===== QUESTION PALETTE ===== */
    .question-palette {
        background: #f9fafb;
        border-top: 1px solid #ddd;
        padding: 10px 15px;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
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

    .palette-nav {
        margin-left: auto;
        display: flex;
        gap: 5px;
    }

    /* ===== FOOTER ===== */
    .exam-footer {
        background: #ffffff;
        border-top: 1px solid #ddd;
        padding: 10px 15px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .exam-footer audio {
        width: 320px;
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
            <span>28 minutes remaining</span>
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

    <div class="mb-3">
        <strong>Part 1</strong><br>
        Listen and answer questions 1–10.
    </div>

    <div class="mb-3">
        <strong>Questions 1–10</strong><br>
        Complete the notes. Write <strong>ONE WORD AND/OR A NUMBER</strong> for each answer.
    </div>

    <h6 class="mb-3">Phone call about second-hand furniture</h6>

    <div class="question-block">
        <strong>Items:</strong><br><br>

        Dining table:
        <ul>
            <li>
                <input type="text" class="form-control d-inline" placeholder="1"> shape
            </li>
            <li>medium size</li>
            <li>
                <input type="text" class="form-control d-inline" placeholder="2"> old
            </li>
            <li>price: £25.00</li>
        </ul>
    </div>

    <div class="question-block">
        Dining chairs:
        <ul>
            <li>
                set of <input type="text" class="form-control d-inline" placeholder="3"> chairs
            </li>
            <li>
                seats covered in <input type="text" class="form-control d-inline" placeholder="4"> material
            </li>
            <li>
                in <input type="text" class="form-control d-inline" placeholder="5"> condition
            </li>
            <li>price: £20.00</li>
        </ul>
    </div>

    <div class="question-block">
        Desk:
        <ul>
            <li>length: 1 metre 20</li>
            <li>
                <input type="text" class="form-control d-inline" placeholder="6"> drawers
            </li>
        </ul>
    </div>

</div>

{{-- ================= QUESTION PALETTE ================= --}}
<div class="question-palette">
    @for($i = 1; $i <= 10; $i++)
        <button class="palette-btn {{ $i === 1 ? 'active' : '' }}">
            {{ $i }}
        </button>
    @endfor

    <div class="palette-nav">
        <button class="btn btn-outline-secondary btn-sm">
            ←
        </button>
        <button class="btn btn-primary btn-sm">
            →
        </button>
    </div>
</div>

{{-- ================= FOOTER ================= --}}
<div class="exam-footer">

    <audio controls>
        <source src="/dummy/audio/listening-part1.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>

    <div class="footer-right">
        <button class="btn btn-outline-secondary btn-sm">
            Exit
        </button>
    </div>
</div>

{{-- @endsection --}}
