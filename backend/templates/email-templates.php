<?php
function welcomeMailTemplate($toMail, $resetLink, $tokenExpire, $fullname, $username, $password = null)
{
    $mailBody = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Account Created Successfully</title>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif; background-color: #0a0a0a; color: #e5e7eb; line-height: 1.6; padding: 20px; }
                .email-container { max-width: 600px; margin: 0 auto; background: linear-gradient(145deg, #0f172a, #1e293b); border-radius: 20px; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5); }
                .logo { width: 60px; height: 60px; background: rgba(255, 255, 255, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; border: 1px solid rgba(255, 255, 255, 0.2); }
                .logo i { font-size: 28px; color: white; }
                .header h1 { font-size: 32px; font-weight: 700; color: white; margin-bottom: 10px; letter-spacing: -0.5px; }
                .header p { font-size: 16px; color: rgba(255, 255, 255, 0.9); opacity: 0.9; }
                .content { padding: 40px 30px; }
                .greeting { font-size: 24px; font-weight: 600; color: #f8fafc; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
                .greeting i { color: #0ea5e9; }
                .message { font-size: 16px; color: #cbd5e1; margin-bottom: 30px; line-height: 1.8; }
                .details-card { background: rgba(30, 41, 59, 0.7); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 25px; margin-bottom: 30px; }
                .details-card h3 { font-size: 18px; font-weight: 600; color: #f1f5f9; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
                .details-card h3 i { color: #10b981; }
                .detail-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
                .detail-row:last-child { border-bottom: none; }
                .detail-label { color: #94a3b8; font-weight: 500; font-size: 14px; }
                .detail-value { color: #e2e8f0; font-weight: 600; font-size: 14px; }
                .password-note { background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 12px; padding: 15px; margin: 20px 0; display: flex; align-items: center; gap: 12px; }
                .password-note i { color: #f59e0b; font-size: 18px; }
                .password-note p { color: #fbbf24; font-size: 14px; margin: 0; }
                .cta-section { text-align: center; margin: 40px 0; }
                .cta-button { display: inline-block; background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; text-decoration: none; padding: 18px 40px; border-radius: 12px; font-weight: 600; font-size: 16px; border: none; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 8px 30px rgba(139, 92, 246, 0.3); position: relative; overflow: hidden; }
                .cta-button:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(139, 92, 246, 0.4); }
                .cta-button::after { content: '→'; margin-left: 10px; font-size: 18px; }
                .expiry-notice { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 12px; padding: 20px; text-align: center; margin-top: 30px; }
                .expiry-notice h4 { color: #f87171; font-size: 16px; font-weight: 600; margin-bottom: 8px; display: flex; align-items: center; justify-content: center; gap: 10px; }
                .expiry-notice p { color: #fca5a5; font-size: 14px; margin: 0; }
                .timer { display: inline-flex; align-items: center; gap: 8px; background: rgba(239, 68, 68, 0.2); padding: 8px 16px; border-radius: 8px; margin-top: 10px; }
                .timer i { color: #f87171; }
                .timer span { color: #f87171; font-size: 14px; font-weight: 600; }
                .footer { text-align: center; padding: 30px; background: rgba(15, 23, 42, 0.8); border-top: 1px solid rgba(255, 255, 255, 0.1); }
                .footer p { color: #94a3b8; font-size: 14px; margin-bottom: 10px; }
                .social-links { display: flex; justify-content: center; gap: 20px; margin-top: 20px; }
                .social-link { width: 40px; height: 40px; background: rgba(255, 255, 255, 0.05); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #cbd5e1; text-decoration: none; transition: all 0.3s ease; }
                .social-link:hover { background: rgba(255, 255, 255, 0.1); color: #0ea5e9; transform: translateY(-2px); }
                .copyright { margin-top: 20px; font-size: 12px; color: #64748b; }
                @media (max-width: 600px) { .content, .header { padding: 30px 20px; } .header h1 { font-size: 28px; } .greeting { font-size: 20px; } .cta-button { padding: 16px 30px; width: 100%; } .detail-row { flex-direction: column; gap: 5px; } }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='content'>
                    <div class='greeting'><i class='fas fa-hand-wave'></i> <span>Hello, $fullname!</span></div>
                    <p class='message'>
                        We’re excited to welcome you to our online examination platform! Your account has been successfully
                        created and is now ready to use.
                    </p>
                    <div class='details-card'>
                        <h3><i class='fas fa-key'></i> Your Login Details</h3>
                        <div class='detail-row'><span class='detail-label'>Email Address:</span><span class='detail-value'> $toMail</span></div>
                        <div class='detail-row'><span class='detail-label'>Username:</span><span class='detail-value'> $username</span></div>";
    if ($password) {
        $mailBody .= "
            <div class='detail-row'>
                <span class='detail-label'>Temporary Password:</span>
                <span class='detail-value'>
                    <code style='background: rgba(0,0,0,0.2); padding: 4px 8px; border-radius: 4px;'>$password</code>
                </span>
            </div>
            ";
    }
    $mailBody .=  "<div class='detail-row'><span class='detail-label'>Account Created:</span> <span class='detail-value'>" . date('F j, Y \a\t g:i A') . "</span></div>
                    </div>
                    <div class='password-note'><i class='fas fa-exclamation-circle'></i> <p>For security reasons, please reset your password after your first login, or use the password reset link provided below.</p></div>
                    <div class='cta-section'>
                        <p style='color: #cbd5e1; margin-bottom: 20px; font-size: 16px;'><strong>Secure Your Account:</strong> Click the button below to reset your password and secure your account.</p>
                        <a href='$resetLink' class='cta-button'>Reset Your Password Now</a>
                    </div>
                    <div class='expiry-notice'>
                        <h4><i class='fas fa-clock'></i> Important Security Notice</h4>
                        <p>For security reasons, this password reset link will expire on:</p>
                        <div class='timer'><i class='fas fa-hourglass-half'></i> <span><strong>" . date('D, M d, Y \a\t H:i A', strtotime($tokenExpire)) . "</strong></span></div>
                        <p style='color:#94a3b8; font-size:14px; margin-top:15px; text-align:center;'>If this link expires, you can request a new password reset link from the login page.</p>
                    </div>
                </div>
                <div class='footer'>
                    <div class='copyright'>© " . config('app.copyright') . "<br>This is an automated email, please do not reply.</div>
                </div>
            </div>
        </body>
        </html>
    ";

    return $mailBody;
}

function resetMailTemplate($toMail, $resetLink, $fullname)
{
    $sentTime = date('D, M d, Y \a\t h:i A');

    $mailBody = "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Password Reset</title>
        <style>
            body {
                font-family: Inter, sans-serif;
                background: #0a0a0a;
                color: #e5e7eb;
                padding: 20px;
            }
            .box {
                max-width: 600px;
                margin: auto;
                background: #0f172a;
                border-radius: 16px;
                padding: 35px;
                border: 1px solid rgba(255,255,255,0.1);
            }
            h1 {
                color: #f8fafc;
                margin-bottom: 20px;
            }
            p {
                color: #cbd5e1;
                line-height: 1.6;
                font-size: 16px;
            }
            .btn {
                display: inline-block;
                margin: 30px 0;
                padding: 15px 35px;
                background: #7c3aed;
                color: #fff;
                text-decoration: none;
                border-radius: 10px;
                font-weight: 600;
            }
            .cta {
                text-align: center;
                margin: 35px 0;
            }
            .cta a {
                display: inline-block;
                padding: 16px 36px;
                background: linear-gradient(135deg, #8b5cf6, #7c3aed);
                color: #fff;
                text-decoration: none;
                border-radius: 12px;
                font-weight: 600;
            }
            .note {
                background: rgba(239,68,68,0.1);
                border: 1px solid rgba(239,68,68,0.25);
                padding: 18px;
                border-radius: 12px;
                margin-top: 25px;
                text-align: center;
            }
            .note strong {
                color: #f87171;
            }
                
            .footer {
                text-align: center;
                margin-top: 30px;
                font-size: 13px;
                color: #94a3b8;
            }
        </style>
    </head>
    <body>
        <div class='box'>
            <h1>Password Reset Request</h1>

            <p>Hello $fullname,</p>

            <p>
                We received a request to reset the password for your account associated with <strong>$toMail</strong>.
            </p>

            <p>
                Click the button below to reset your password. If you did not request this, you can safely ignore this email.
            </p>

            <div class='cta'>
                <a href='$resetLink'>Reset Password</a>
            </div>

            <p style='margin-top:12px;'>
                <strong>Email sent at: </strong>$sentTime
            </p>

            <div class='note'>
                <p>
                    <strong>This password reset link is valid for ONLY 5 minutes from the time this email was sent.</strong>
                </p>

                <p style='margin-top:10px; color:#94a3b8;'>
                    After 5 minutes, this link will automatically become invalid
                    and you must request a new password reset.
                </p>
            </div>

            <div class='footer'>
                © " . config('app.copyright') . "<br>
                This is an automated email. Please do not reply.
            </div>
        </div>
    </body>
    </html>
    ";

    return $mailBody;
}
