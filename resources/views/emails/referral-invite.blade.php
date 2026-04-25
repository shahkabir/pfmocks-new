<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>You're invited to PerfectMocks</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,sans-serif;color:#212529;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0"
           style="width:100%;padding:32px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0"
                       style="width:560px;max-width:92%;background:#ffffff;border-radius:14px;
                              box-shadow:0 4px 18px rgba(0,0,0,.06);overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#0d6efd,#0a58ca);
                                   color:#fff;padding:28px 32px;text-align:center;">
                            <div style="font-size:1.6rem;font-weight:700;letter-spacing:-0.5px;">
                                Perfect<span style="font-weight:300;">Mocks</span>
                            </div>
                            <div style="font-size:.9rem;opacity:.9;margin-top:4px;">
                                You've been invited to start practicing
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;line-height:1.55;font-size:14.5px;">
                            <p style="margin:0 0 12px;">Hi there,</p>
                            <p style="margin:0 0 12px;">
                                <strong>{{ $referrerName }}</strong> has invited you to join
                                <strong>PerfectMocks</strong> — your destination for high-quality
                                IELTS, BCS &amp; Bank-AD mock exams.
                            </p>

                            @if($personalMessage)
                                <blockquote style="margin:14px 0;padding:12px 16px;
                                                   background:#f8f9fa;border-left:4px solid #0d6efd;
                                                   border-radius:6px;color:#495057;font-style:italic;">
                                    "{{ $personalMessage }}"
                                </blockquote>
                            @endif

                            <p style="margin:18px 0 8px;">Use this referral code at signup to claim your discount on the first paid module:</p>

                            <div style="background:#fff7e6;border:1px dashed #f59f00;border-radius:8px;
                                        padding:12px 16px;text-align:center;margin:8px 0 18px;">
                                <div style="font-size:.78rem;color:#5a3e00;letter-spacing:.6px;
                                            text-transform:uppercase;font-weight:600;">Referral Code</div>
                                <div style="font-size:1.4rem;font-weight:800;color:#5a3e00;
                                            font-family:'Courier New',monospace;letter-spacing:2px;">
                                    {{ $referralCode }}
                                </div>
                            </div>

                            <div style="text-align:center;margin:22px 0;">
                                <a href="{{ $referralLink }}"
                                   style="display:inline-block;background:#0d6efd;color:#ffffff;
                                          text-decoration:none;font-weight:600;font-size:14px;
                                          padding:11px 24px;border-radius:8px;">
                                    Sign up &amp; claim discount
                                </a>
                            </div>

                            <p style="margin:18px 0 6px;font-size:13px;color:#6c757d;">
                                Or paste this link into your browser:
                            </p>
                            <p style="margin:0 0 4px;word-break:break-all;font-size:13px;">
                                <a href="{{ $referralLink }}" style="color:#0d6efd;">{{ $referralLink }}</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f8f9fa;padding:14px 32px;font-size:11.5px;
                                   color:#6c757d;text-align:center;border-top:1px solid #e9ecef;">
                            You received this email because someone you know wants to share PerfectMocks with you.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
