<?php
/**
 * BaseController — shared view rendering and helpers for all controllers
 */
abstract class BaseController
{
    protected SettingsModel $settings;

    public function __construct()
    {
        $this->settings = new SettingsModel();
    }

    /**
     * Render a view file, passing data into scope.
     * Layout wraps content views; admin layout wraps admin views.
     */
    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        // Make data available as variables
        extract($data);

        // Site-wide settings available to all views
        $siteName      = $this->settings->get('station_name', 'WAVE FM');
        $siteTagline   = $this->settings->get('station_tagline');
        $siteFrequency = $this->settings->get('station_frequency', '98.7');
        $streamUrl     = $this->settings->get('stream_url');

        // Buffer the content view
        ob_start();
        $viewFile = APP_PATH . 'views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            Logger::error('View not found: ' . $viewFile);
            http_response_code(500);
            require APP_PATH . 'views/errors/500.php';
            exit;
        }
        require $viewFile;
        $content = ob_get_clean();

        // Wrap in layout
        $layoutFile = APP_PATH . 'views/' . $layout . '_layout.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    protected function renderJson(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function getFlash(): ?array
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function post(string $key, string $default = ''): string
    {
        return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
    }

    protected function get(string $key, string $default = ''): string
    {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
    }

    protected function intParam(string $key, string $source = 'get'): int
    {
        $val = $source === 'post' ? ($_POST[$key] ?? 0) : ($_GET[$key] ?? 0);
        return Security::sanitizeInt($val);
    }
}
