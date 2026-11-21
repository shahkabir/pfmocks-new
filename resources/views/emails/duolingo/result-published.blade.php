<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Your result is ready</title></head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,sans-serif;color:#212529;">
    <table cellpadding="0" cellspacing="0" border="0" style="width:100%;padding:32px 0;">
        <tr><td align="center">
            <table cellpadding="0" cellspacing="0" border="0"
                   style="width:560px;max-width:92%;background:#ffffff;border-radius:14px;
                          box-shadow:0 4px 18px rgba(0,0,0,.06);overflow:hidden;">
                <tr><td style="background:linear-gradient(135deg,#0d6efd,#0a58ca);color:#fff;padding:24px 32px;text-align:center;">
                    <div style="font-size:1.5rem;font-weight:700;">Perfect<span style="font-weight:300;">Mocks</span></div>
                    <div style="font-size:.88rem;opacity:.9;margin-top:4px;">Your Duolingo practice test result is ready</div>
                </td></tr>

                <tr><td style="padding:26px 32px;line-height:1.6;font-size:14.5px;">
                    <p style="margin:0 0 12px;">Hello {{ $studentName }},</p>
                    <p style="margin:0 0 14px;">
                        Our evaluator has finished reviewing your writing and speaking responses for
                        <strong>{{ $moduleName }}</strong>, and your full result is now available.
                    </p>

                    @if($overallScore !== null)
                        <div style="background:#e8f5ee;border:1px solid #a3cfbb;border-radius:10px;padding:16px;text-align:center;margin:18px 0;">
                            <div style="font-size:.78rem;color:#0a5934;letter-spacing:.6px;text-transform:uppercase;font-weight:700;">Estimated overall score</div>
                            <div style="font-size:2.2rem;font-weight:800;color:#0a5934;">{{ $overallScore }}</div>
                            <div style="font-size:.8rem;color:#0a5934;">on the 10–160 scale</div>
                        </div>
                    @endif

                    <p style="text-align:center;margin:22px 0;">
                        <a href="{{ $resultUrl }}"
                           style="background:#0d6efd;color:#fff;text-decoration:none;padding:11px 22px;border-radius:8px;font-weight:600;display:inline-block;">
                            View your result
                        </a>
                    </p>

                    <p style="margin:0;font-size:12.5px;color:#6c757d;">
                        This is a PerfectMocks practice estimate, not an official Duolingo English Test score.
                    </p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
