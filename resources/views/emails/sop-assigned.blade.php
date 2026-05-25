<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>SOP request assigned</title></head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,sans-serif;color:#212529;">
    <table cellpadding="0" cellspacing="0" border="0" style="width:100%;padding:32px 0;">
        <tr><td align="center">
            <table cellpadding="0" cellspacing="0" border="0"
                   style="width:560px;max-width:92%;background:#ffffff;border-radius:14px;
                          box-shadow:0 4px 18px rgba(0,0,0,.06);overflow:hidden;">

                {{-- ===== Header ===== --}}
                <tr><td style="background:linear-gradient(135deg,#0d6efd,#0a58ca);
                                color:#fff;padding:24px 32px;text-align:center;">
                    <div style="font-size:1.5rem;font-weight:700;">
                        Perfect<span style="font-weight:300;">Mocks</span>
                    </div>
                    <div style="font-size:.88rem;opacity:.9;margin-top:4px;">
                        @if($recipientType === 'evaluator')
                            New {{ $sopServiceLabel }} request assigned
                        @else
                            Your {{ $sopServiceLabel }} request is being processed
                        @endif
                    </div>
                </td></tr>

                {{-- ===== Body ===== --}}
                <tr><td style="padding:26px 32px;line-height:1.6;font-size:14.5px;">

                    @if($recipientType === 'student')
                        {{-- ─────────── Student copy (per spec) ─────────── --}}
                        <p style="margin:0 0 12px;">Hello {{ $studentName }},</p>

                        <p style="margin:0 0 14px;">
                            Your <strong>{{ $sopServiceLabel }}</strong> request has been assigned
                            to one of our experienced evaluators.
                        </p>

                        <div style="background:#fff7e6;border:1px solid #f5e9c8;border-radius:10px;
                                    padding:14px 18px;margin:14px 0;font-size:.92rem;color:#5a3e00;">
                            <strong>Expected turnaround:</strong>
                            <ul style="margin:6px 0 0 18px;padding:0;">
                                <li>Up to <strong>24 hours</strong> for SOP Review services.</li>
                                <li>Up to <strong>72 hours</strong> for Personalized SOP Writing services.</li>
                            </ul>
                        </div>

                        <p style="margin:0 0 14px;">
                            You will receive an email notification as soon as your request has been
                            completed.
                        </p>

                        <p style="margin:0 0 14px;">
                            Thank you for your patience and for choosing our service.
                        </p>

                        <p style="margin:18px 0 0;">
                            Best regards,<br>
                            <strong>PerfectMocks Team</strong>
                        </p>

                    @else
                        {{-- ─────────── Evaluator copy ─────────── --}}
                        <p style="margin:0 0 12px;">Hello {{ $evaluatorName }},</p>

                        <p style="margin:0 0 12px;">
                            You have been assigned a new <strong>{{ $sopServiceLabel }}</strong> request.
                        </p>

                        <ul style="margin:0 0 14px 18px;padding:0;">
                            <li><strong>Student:</strong> {{ $studentName }}</li>
                            <li><strong>Service:</strong> {{ $sopServiceLabel }}</li>
                            <li><strong>Assigned at:</strong> {{ $evaluation->assigned_at?->format('Y-m-d H:i') }}</li>
                        </ul>

                        <div style="text-align:center;margin:22px 0;">
                            <a href="{{ url('/evaluator/evaluations/' . $evaluation->id) }}"
                               style="display:inline-block;background:#0d6efd;color:#fff;
                                      text-decoration:none;font-weight:600;padding:11px 24px;
                                      border-radius:8px;">Open request</a>
                        </div>

                        <p style="margin:0 0 14px;font-size:.92rem;color:#6c757d;">
                            Please complete this request within
                            @if($evaluation->module_type === 'sop_review') 24 hours @else 72 hours @endif
                            of assignment.
                        </p>

                        <p style="margin:18px 0 0;">
                            Best regards,<br>
                            <strong>PerfectMocks Team</strong>
                        </p>
                    @endif
                </td></tr>

                {{-- ===== Footer ===== --}}
                <tr><td style="background:#f8f9fa;padding:14px 32px;font-size:11.5px;
                                color:#6c757d;text-align:center;border-top:1px solid #e9ecef;">
                    PerfectMocks · automated notification
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
