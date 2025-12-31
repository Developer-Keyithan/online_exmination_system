<?php
$y =  date('Y');
return [
    'app_name' => getenv('APP_NAME') ?: 'Online Exam',
    'version' => getenv('APP_VERSION') ?: '1.0.0',
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => (bool) (getenv('APP_DEBUG') ?: false),
    'url' => getenv('APP_URL') ?: 'http://localhost',
    'timezone' => 'UTC',
    'locale' => 'en',
    'collapse' => true,
    'logo' => "assets/img/logo.png",
    'user_icon' => "assets/img/1.png",
    'powered-url' => 'https://northernithub.com/',
    'powered-text' => 'Powered by: NorthernITHub',
    'copyright' => 'Copyright ' . $y . ' NorthernITHub. All rights reserved.',
    'platform-email' => 'devkeyithan@gmail.com',
    'email-app-password' => 'dmxq groe zjba kzdr',
    'email-username' => 'Online Examination System',
    'email-host' => 'smtp.gmail.com',
    'email-port' => 587
];
?>