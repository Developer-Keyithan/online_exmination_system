<?php
function resetMailTemplate($toMail, $resetLink, $tokenExpire, $fullname, $username, $password)
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
                        <div class='detail-row'><span class='detail-label'>Username:</span><span class='detail-value'> $username</span></div>
                        <div class='detail-row'><span class='detail-label'>Temporary Password:</span><span class='detail-value'><code style='background: rgba(0,0,0,0.2); padding: 4px 8px; border-radius: 4px;'> $password</code></span></div>
                        <div class='detail-row'><span class='detail-label'>Account Created:</span> <span class='detail-value'>" . date('F j, Y \a\t g:i A') . "</span></div>
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
                    <div class='copyright'>© " . date('Y') . " Your Online Exam Platform. All rights reserved.<br>This is an automated email, please do not reply.</div>
                </div>
            </div>
        </body>
        </html>
    ";

    return $mailBody;
}
