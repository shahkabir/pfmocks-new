{{-- @extends('layouts.app') --}}

@section('title', 'IELTS Writing Test')

{{-- @section('content') --}}
<style>
    .exam-topbar {
        background: #343a40;
        color: #fff;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .timer {
        font-weight: bold;
        font-size: 16px;
    }

    .exam-details {
        background: #f4f6f9;
        padding: 15px;
        border-bottom: 1px solid #ddd;
    }

    .exam-body {
        display: flex;
        height: calc(100vh - 260px);
    }

    .question-panel {
        width: 45%;
        padding: 15px;
        overflow-y: auto;
        border-right: 1px solid #ddd;
        background: #fff;
    }

    .writing-panel {
        width: 55%;
        padding: 15px;
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
        text-align: right;
        font-size: 13px;
        color: #666;
        margin-top: 5px;
    }

    .exam-footer {
        background: #f4f6f9;
        padding: 10px 15px;
        border-top: 1px solid #ddd;
        display: flex;
        align-items: center;
    }

    .part-nav button {
        margin-right: 5px;
    }

    .footer-right {
        margin-left: auto;
    }
</style>

{{-- TOP BAR --}}
<div class="exam-topbar">
    <div>
        <strong>Candidate:</strong> XXXX XXXXXXX – 123456
    </div>
    <div class="timer">
        ⏱ <span id="time">59:00</span> minutes left
    </div>
</div>

{{-- EXAM DETAILS --}}
<div class="exam-details">
    <strong>IELTS Academic Writing</strong><br>
    <small>
        Task 1 · You should spend about 20 minutes on this task.  
        Write at least 150 words.
    </small>
</div>

{{-- MAIN BODY --}}
<div class="exam-body">

    {{-- LEFT: QUESTION --}}
    <div class="question-panel">
        <h5>Part 1</h5>

        <p>
            The table below gives information about the underground railway
            systems in six cities.
        </p>

        <p>
            Summarise the information by selecting and reporting the main
            features, and make comparisons where relevant.
        </p>

        <table class="table table-bordered table-sm mt-3">
            <thead class="thead-light">
                <tr>
                    <th>City</th>
                    <th>Date opened</th>
                    <th>Kilometres</th>
                    <th>Passengers (millions)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>London</td>
                    <td>1863</td>
                    <td>394</td>
                    <td>775</td>
                </tr>
                <tr>
                    <td>Paris</td>
                    <td>1900</td>
                    <td>199</td>
                    <td>1191</td>
                </tr>
                <tr>
                    <td>Tokyo</td>
                    <td>1927</td>
                    <td>155</td>
                    <td>1927</td>
                </tr>
                <tr>
                    <td>Washington DC</td>
                    <td>1976</td>
                    <td>126</td>
                    <td>144</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- RIGHT: WRITING AREA --}}
    <div class="writing-panel">
        <textarea id="writingArea"
                  class="form-control"
                  placeholder="Write your answer here..."></textarea>

        <div class="word-count">
            Word count: <span id="wordCount">0</span>
        </div>
    </div>

</div>

{{-- FOOTER NAVIGATION --}}
<div class="exam-footer">

    <div class="part-nav">
        <button class="btn btn-primary btn-sm">Part 1</button>
        <button class="btn btn-outline-secondary btn-sm">Part 2</button>
    </div>

    <div class="footer-right">
        <button class="btn btn-success btn-sm">
            Next ➜
        </button>
    </div>

</div>

{{-- SCRIPT: WORD COUNT + TIMER --}}
<script>
    // Word count
    const textarea = document.getElementById('writingArea');
    const wordCount = document.getElementById('wordCount');

    textarea.addEventListener('input', function () {
        const words = this.value.trim().split(/\s+/).filter(w => w.length);
        wordCount.innerText = words.length;
    });

    // Countdown timer (dummy)
    let seconds = 3540; // 59 minutes

    setInterval(() => {
        if (seconds <= 0) return;
        seconds--;
        let m = Math.floor(seconds / 60);
        let s = seconds % 60;
        document.getElementById('time').innerText =
            `${m}:${s.toString().padStart(2, '0')}`;
    }, 1000);
</script>
{{-- @endsection --}}
