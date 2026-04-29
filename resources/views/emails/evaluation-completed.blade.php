<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Evaluation ready</title></head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,sans-serif;color:#212529;">
    <table cellpadding="0" cellspacing="0" border="0" style="width:100%;padding:32px 0;">
        <tr><td align="center">
            <table cellpadding="0" cellspacing="0" border="0"
                   style="width:560px;max-width:92%;background:#ffffff;border-radius:14px;
                          box-shadow:0 4px 18px rgba(0,0,0,.06);overflow:hidden;">
                <tr><td style="background:linear-gradient(135deg,#198754,#0a5934);
                                color:#fff;padding:26px 32px;text-align:center;">
                    <div style="font-size:1.5rem;font-weight:700;">
                        Perfect<span style="font-weight:300;">Mocks</span>
                    </div>
                    <div style="font-size:.95rem;margin-top:6px;">
                        Your {{ $moduleName }} evaluation is ready
                    </div>
                </td></tr>
                <tr><td style="padding:26px 32px;line-height:1.55;font-size:14.5px;">
                    <p>Hi {{ $studentName }},</p>
                    <p>Our evaluator has completed the review of your <strong>{{ $moduleName }}</strong>
                       answers for <strong>{{ $examName }}</strong>.</p>

                    @if($overallBand !== null)
                        <div style="background:#e8f5ee;border:1px solid #a3cfbb;border-radius:10px;
                                    padding:16px;text-align:center;margin:18px 0;">
                            <div style="font-size:.78rem;color:#0a5934;letter-spacing:.6px;
                                        text-transform:uppercase;font-weight:700;">Overall Band</div>
                            <div style="font-size:2.2rem;font-weight:800;color:#0a5934;">
                                {{ number_format($overallBand, 1) }}
                            </div>
                        </div>
                    @endif

                    <p>Click below to see your detailed criteria-by-criteria breakdown
                       and the evaluator's feedback.</p>

                    <div style="text-align:center;margin:22px 0;">
                        <a href="{{ $resultUrl }}"
                           style="display:inline-block;background:#198754;color:#fff;
                                  text-decoration:none;font-weight:600;padding:11px 24px;
                                  border-radius:8px;">View detailed result</a>
                    </div>
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
