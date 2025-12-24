## Before Hosting Setup Instructions

Follow these steps **before uploading the project to hosting**.

---

### 1. `root/.htaccess` file

> Update this **after hosting** only if the project is inside a subfolder.

```apache
RewriteBase /NIT/exam/
```

### 2. `root/.env` file

> Configure environment variables before hosting.

```env
APP_NAME=Online Exam
APP_VERSION=1.0.0
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/NIT/exam
APP_FOLDER=
IS_CPANEL = false
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=exam
DB_USERNAME=root
DB_PASSWORD=
NEED_SEEDS = true
```

### 3. `root/config/app.php` file

```php
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
```

### 4. `root/config/config.php` file

```php
<?php
if (!defined('BASE_URL')) define('BASE_URL', getenv('APP_URL') ?? 'http://localhost/NIT/exam');
if (!defined('ROOT')) define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
if (!defined('CONFIG_PATH')) define('CONFIG_PATH', ROOT . 'config' . DIRECTORY_SEPARATOR);
if (!defined('FRONTEND_PATH')) define('FRONTEND_PATH', ROOT . 'frontend' . DIRECTORY_SEPARATOR);
if (!defined('BACKEND_PATH')) define('BACKEND_PATH', ROOT . 'backend' . DIRECTORY_SEPARATOR);
if (!defined('HELPERS_PATH')) define('HELPERS_PATH', BACKEND_PATH . 'helpers' . DIRECTORY_SEPARATOR);
if (!defined('MODAL_PATH')) define('MODAL_PATH', BACKEND_PATH . 'modal' . DIRECTORY_SEPARATOR);
if (!defined('ASSETS_PATH')) define('ASSETS_PATH', FRONTEND_PATH . 'assets' . DIRECTORY_SEPARATOR);
if (!defined('ROUTES_PATH')) define('ROUTES_PATH', FRONTEND_PATH . 'routes' . DIRECTORY_SEPARATOR);
if (!defined('LIB_PATH')) define('LIB_PATH', ROOT . 'lib' . DIRECTORY_SEPARATOR);
if (!defined('MIDDLEWARE_PATH')) define('MIDDLEWARE_PATH', BACKEND_PATH . 'middleware' . DIRECTORY_SEPARATOR);
if (!defined('API_PATH')) define('API_PATH', BACKEND_PATH . 'API' . DIRECTORY_SEPARATOR);
if (!defined('VERSION')) define('VERSION', '1.0.0');
if (!defined('NAME')) define('NAME', 'Online Exam');
if (!defined('SKIP_ROOT')) define('SKIP_ROOT', false);
```


### 5. Install `dompdf`

> Make sure **Composer** and **PHP zip extension** are enabled.

```powershell
cd C:xampp\htdocs
```
```powershell
composer -v
```
```powershell
composer require dompdf/dompdf
```

✅ Verification
```powershell
dir vendor\dompdf
```

If the folder exists, `dompdf` is installed successfully 🎉