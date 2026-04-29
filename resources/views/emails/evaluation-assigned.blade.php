<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Evaluation assigned</title></head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,sans-serif;color:#212529;">
    <table cellpadding="0" cellspacing="0" border="0" style="width:100%;padding:32px 0;">
        <tr><td align="center">
            <table cellpadding="0" cellspacing="0" border="0"
                   style="width:560px;max-width:92%;background:#ffffff;border-radius:14px;
                          box-shadow:0 4px 18px rgba(0,0,0,.06);overflow:hidden;">
                <tr><td style="background:linear-gradient(135deg,#0d6efd,#0a58ca);
                                color:#fff;padding:24px 32px;text-align:center;">
                    <div style="font-size:1.5rem;font-weight:700;">
                        Perfect<span style="font-weight:300;">Mocks</span>
                    </div>
                    <div style="font-size:.88rem;opacity:.9;margin-top:4px;">
                        @if($recipientType === 'evaluator')
                            New evaluation assigned
                        @else
                            Your answers are with an evaluator
                        @endif
                    </div>
                </td></tr>
                <tr><td style="padding:26px 32px;line-height:1.55;font-size:14.5px;">
                    @if($recipientType === 'evaluator')
                        <p>Hi {{ $evaluatorName }},</p>
                        <p>You have been assigned a new <strong>{{ $moduleName }}</strong> evaluation.</p>
                        <ul>
                            <li><strong>Student:</strong> {{ $studentName }}</li>
                            <li><strong>Exam:</strong> {{ $examName }}</li>
                            <li><strong>Module:</strong> {{ $moduleName }}</li>
                            <li><strong>Assigned at:</strong> {{ $evaluation->assigned_at?->format('Y-m-d H:i') }}</li>
                        </ul>
                        <div style="text-align:center;margin:22px 0;">
                            <a href="{{ url('/evaluator/evaluations/' . $evaluation->id) }}"
                               style="display:inline-block;background:#0d6efd;color:#fff;
                                      text-decoration:none;font-weight:600;padding:11px 24px;
                                      border-radius:8px;">Open evaluation</a>
                        </div>
                    @else
                        <p>Hi {{ $studentName }},</p>
                        <p>Your <strong>{{ $moduleName }}</strong> answers for <strong>{{ $examName }}</strong>
                           have been sent to one of our evaluators for review.</p>
                        <p>You'll receive another email when your grading and feedback are ready.</p>
                    @endif
                </td></tr>
                <tr><td style="background:#f8f9fa;padding:14px 32px;font-size:11.5px;
                                color:#6c757d;text-align:center;border-top:1px solid #e9ecef;">
                    PerfectMocks · automated notification
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
