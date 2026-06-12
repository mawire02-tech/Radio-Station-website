<?php
/**
 * WAVE FM — Application Configuration
 * Copy this file to config/config.php and adjust for your environment.
 */

// ── Environment ──────────────────────────────────────────────
define('ENVIRONMENT', 'development'); // 'development' | 'production'
define('APP_VERSION', '1.0.0');

// ── Paths ─────────────────────────────────────────────────────
define('ROOT_PATH',   dirname(__DIR__) . '/');
define('APP_PATH',    ROOT_PATH . 'app/');
define('PUBLIC_PATH', ROOT_PATH . 'public/');
define('LOG_PATH',    ROOT_PATH . 'logs/');
define('UPLOAD_PATH', PUBLIC_PATH . 'assets/images/uploads/');

// ── Base URL — change to match your XAMPP virtual host or subfolder ──
// If running at http://localhost/wavefm/ use:
define('BASE_URL', 'http://localhost/wavefm/');
// If running at http://wavefm.local/ use:
// define('BASE_URL', 'http://wavefm.local/');

define('ASSET_URL', BASE_URL . 'public/assets/');
define('UPLOAD_URL', BASE_URL . 'public/assets/images/uploads/');

// ── Database ──────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'wavefm');
define('DB_USER', 'root');      // Change in production!
define('DB_PASS', '');          // Change in production!
define('DB_CHARSET', 'utf8mb4');

// ── Session ───────────────────────────────────────────────────
define('SESSION_NAME',    'WAVEFM_SESS');
define('SESSION_LIFETIME', 7200);   // 2 hours
define('CSRF_TOKEN_NAME', '_csrf_token');

// ── Security ──────────────────────────────────────────────────
define('BCRYPT_COST', 12);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// ── Uploads ───────────────────────────────────────────────────
define('MAX_UPLOAD_SIZE',   10 * 1024 * 1024); // 10 MB
define('ALLOWED_IMG_TYPES', ['image/jpeg','image/png','image/webp','image/gif']);
define('ALLOWED_AUDIO_TYPES', ['audio/mpeg','audio/mp3','audio/ogg','audio/wav','audio/x-wav']);

// ── Pagination ────────────────────────────────────────────────
define('NEWS_PER_PAGE', 9);
define('ADMIN_PER_PAGE', 15);

// ── Timezone ─────────────────────────────────────────────────
date_default_timezone_set('Europe/London');

// ── Error Handling ────────────────────────────────────────────
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ── Autoloader ────────────────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $paths = [
        APP_PATH . 'controllers/' . $class . '.php',
        APP_PATH . 'models/'      . $class . '.php',
        APP_PATH . 'helpers/'     . $class . '.php',
    ];
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
