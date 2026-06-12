<?php
/**
 * Security — CSRF protection, sanitisation, validation, session helpers
 */
class Security
{
    // ── CSRF ─────────────────────────────────────────────────

    public static function generateCsrf(): string
    {
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    public static function verifyCsrf(?string $token): bool
    {
        if (empty($token) || empty($_SESSION[CSRF_TOKEN_NAME])) {
            return false;
        }
        return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }

    public static function csrfField(): string
    {
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . self::generateCsrf() . '">';
    }

    // ── Sanitisation ─────────────────────────────────────────

    public static function sanitize(mixed $value): string
    {
        return htmlspecialchars(trim((string)$value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public static function sanitizeArray(array $data, array $fields): array
    {
        $clean = [];
        foreach ($fields as $field) {
            $clean[$field] = isset($data[$field]) ? self::sanitize($data[$field]) : '';
        }
        return $clean;
    }

    public static function sanitizeEmail(string $email): string
    {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    public static function sanitizeInt(mixed $val): int
    {
        return (int) filter_var($val, FILTER_SANITIZE_NUMBER_INT);
    }

    // ── Validation ───────────────────────────────────────────

    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validateRequired(array $data, array $fields): array
    {
        $errors = [];
        foreach ($fields as $field) {
            if (empty(trim($data[$field] ?? ''))) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            }
        }
        return $errors;
    }

    public static function validateLength(string $value, int $min, int $max): bool
    {
        $len = mb_strlen(trim($value));
        return $len >= $min && $len <= $max;
    }

    // ── Password ─────────────────────────────────────────────

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function passwordStrength(string $password): bool
    {
        // At least 8 chars, one uppercase, one lowercase, one digit, one special char
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password) === 1;
    }

    // ── Rate limiting (login brute-force) ───────────────────

    public static function checkLoginAttempts(string $key): bool
    {
        $sessKey = 'login_attempts_' . md5($key);
        $timeKey = 'login_lockout_'  . md5($key);

        if (isset($_SESSION[$timeKey]) && time() < $_SESSION[$timeKey]) {
            return false; // Still locked out
        }
        return true;
    }

    public static function incrementLoginAttempts(string $key): void
    {
        $sessKey = 'login_attempts_' . md5($key);
        $timeKey = 'login_lockout_'  . md5($key);
        $_SESSION[$sessKey] = ($_SESSION[$sessKey] ?? 0) + 1;
        if ($_SESSION[$sessKey] >= MAX_LOGIN_ATTEMPTS) {
            $_SESSION[$timeKey] = time() + LOGIN_LOCKOUT_TIME;
            $_SESSION[$sessKey] = 0;
            Logger::security('Login lockout triggered for: ' . $key);
        }
    }

    public static function clearLoginAttempts(string $key): void
    {
        unset(
            $_SESSION['login_attempts_' . md5($key)],
            $_SESSION['login_lockout_'  . md5($key)]
        );
    }

    // ── Auth check ───────────────────────────────────────────

    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_role']);
    }

    public static function requireAuth(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . 'admin/login');
            exit;
        }
    }

    public static function requireRole(string $role): void
    {
        self::requireAuth();
        $hierarchy = ['editor' => 1, 'admin' => 2, 'superadmin' => 3];
        $required  = $hierarchy[$role]    ?? 99;
        $current   = $hierarchy[$_SESSION['admin_role']] ?? 0;
        if ($current < $required) {
            http_response_code(403);
            die('Access denied.');
        }
    }

    // ── Slug generator ───────────────────────────────────────

    public static function slugify(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[^\w\s\-]/u', '', $text);
        $text = preg_replace('/[\s_\-]+/', '-', $text);
        return trim($text, '-');
    }

    // ── IP Address ───────────────────────────────────────────

    public static function getIp(): string
    {
        $keys = ['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','HTTP_CLIENT_IP','REMOTE_ADDR'];
        foreach ($keys as $k) {
            if (!empty($_SERVER[$k])) {
                $ip = explode(',', $_SERVER[$k])[0];
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return '0.0.0.0';
    }

    // ── File upload validation ───────────────────────────────

    public static function validateUpload(array $file, array $allowedMimes, int $maxBytes = 0): array
    {
        $errors = [];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload error code: ' . $file['error'];
            return $errors;
        }
        if ($maxBytes > 0 && $file['size'] > $maxBytes) {
            $errors[] = 'File too large. Maximum: ' . self::formatBytes($maxBytes);
        }
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, $allowedMimes, true)) {
            $errors[] = 'Invalid file type: ' . $mimeType;
        }
        return $errors;
    }

    public static function sanitizeFilename(string $name): string
    {
        $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $base = pathinfo($name, PATHINFO_FILENAME);
        $base = preg_replace('/[^a-z0-9\-_]/i', '_', $base);
        return substr($base, 0, 60) . '_' . time() . '.' . $ext;
    }

    public static function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
