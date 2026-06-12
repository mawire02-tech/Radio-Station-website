<?php
/**
 * WAVE FM — Front Controller / Entry Point
 * All requests route through here via .htaccess
 */
declare(strict_types=1);

// ── Bootstrap ────────────────────────────────────────────────
require_once dirname(__DIR__) . '/config/config.php';
require_once APP_PATH . 'helpers/Logger.php';
require_once APP_PATH . 'helpers/Security.php';
require_once APP_PATH . 'helpers/Router.php';
require_once dirname(__DIR__) . '/config/Database.php';
require_once APP_PATH . 'models/BaseModel.php';
require_once APP_PATH . 'models/Models.php';
require_once APP_PATH . 'controllers/BaseController.php';
require_once APP_PATH . 'controllers/PublicControllers.php';
require_once APP_PATH . 'controllers/AdminControllers.php';
require_once APP_PATH . 'controllers/ApiController.php';

// ── Session ──────────────────────────────────────────────────
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_samesite', 'Lax');
if (!defined('HTTPS_FORCED') || ENVIRONMENT === 'production') {
    ini_set('session.cookie_secure', '0'); // Set to 1 on HTTPS
}
session_name(SESSION_NAME);
session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// Session is marked on first visit. Session ID is regenerated at login for security.
// Do NOT call session_regenerate_id() here — it would destroy the CSRF token
// set during the login GET before the login POST can verify it.
if (!isset($_SESSION['_initiated'])) {
    $_SESSION['_initiated'] = true;
}

// Session timeout enforcement
if (isset($_SESSION['admin_login_time'])) {
    if (time() - $_SESSION['admin_login_time'] > SESSION_LIFETIME) {
        session_unset(); session_destroy();
        header('Location: ' . BASE_URL . 'admin/login?timeout=1');
        exit;
    }
    $_SESSION['admin_login_time'] = time(); // Rolling window
}

// ── Set security headers ──────────────────────────────────────
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// ── Maintenance mode ─────────────────────────────────────────
// (Bypass for admin routes)
if (!defined('DB_HOST')) { die('Config not loaded'); }

// ── Register routes ───────────────────────────────────────────
$router = new Router();

// API Routes
$router->get('/api/listener-analytics', [ApiController::class, 'listenerAnalytics']);
$router->post('/api/track-listener',    [ApiController::class, 'trackListener']);
$router->post('/api/subscribe',         [ApiController::class, 'subscribe']);

// Public routes
$router->get('/',                   [HomeController::class,       'index']);
$router->get('/news',               [NewsController::class,       'index']);
$router->get('/news/:slug',         [NewsController::class,       'article']);
$router->get('/shows',              [ShowsController::class,      'index']);
$router->get('/shows/:slug',        [ShowsController::class,      'detail']);
$router->get('/presenters',         [PresentersController::class, 'index']);
$router->get('/presenters/:slug',   [PresentersController::class, 'profile']);
$router->get('/requests',           [RequestsController::class,   'index']);
$router->post('/requests/submit',   [RequestsController::class,   'submitRequest']);
$router->post('/requests/feedback', [RequestsController::class,   'submitFeedback']);
$router->post('/requests/poll',     [RequestsController::class,   'pollVote']);
$router->get('/about',              [AboutController::class,      'index']);
$router->post('/about/contact',     [AboutController::class,      'contact']);

// Admin routes
$router->get('/admin',                          fn() => header('Location: ' . BASE_URL . 'admin/dashboard') ?: null);
$router->get('/admin/login',                    [AdminAuthController::class,       'loginPage']);
$router->post('/admin/login',                   [AdminAuthController::class,       'login']);
$router->get('/admin/logout',                   [AdminAuthController::class,       'logout']);
$router->get('/admin/dashboard',                [AdminDashboardController::class,  'index']);

$router->get('/admin/news',                     [AdminNewsController::class,       'index']);
$router->get('/admin/news/create',              [AdminNewsController::class,       'create']);
$router->post('/admin/news/store',              [AdminNewsController::class,       'store']);
$router->get('/admin/news/edit/:id',            [AdminNewsController::class,       'edit']);
$router->post('/admin/news/update/:id',         [AdminNewsController::class,       'update']);
$router->get('/admin/news/delete/:id',          [AdminNewsController::class,       'delete']);

$router->get('/admin/presenters',               [AdminPresentersController::class, 'index']);
$router->get('/admin/presenters/create',        [AdminPresentersController::class, 'create']);
$router->post('/admin/presenters/store',        [AdminPresentersController::class, 'store']);
$router->get('/admin/presenters/edit/:id',      [AdminPresentersController::class, 'edit']);
$router->post('/admin/presenters/update/:id',   [AdminPresentersController::class, 'update']);
$router->get('/admin/presenters/delete/:id',    [AdminPresentersController::class, 'delete']);

$router->get('/admin/shows',                    [AdminShowsController::class,      'index']);
$router->get('/admin/shows/create',             [AdminShowsController::class,      'create']);
$router->post('/admin/shows/store',             [AdminShowsController::class,      'store']);
$router->get('/admin/shows/edit/:id',           [AdminShowsController::class,      'edit']);
$router->post('/admin/shows/update/:id',        [AdminShowsController::class,      'update']);
$router->get('/admin/shows/delete/:id',         [AdminShowsController::class,      'delete']);

$router->get('/admin/podcasts',                 [AdminPodcastsController::class,   'index']);
$router->get('/admin/podcasts/create',          [AdminPodcastsController::class,   'create']);
$router->post('/admin/podcasts/store',          [AdminPodcastsController::class,   'store']);
$router->get('/admin/podcasts/delete/:id',      [AdminPodcastsController::class,   'delete']);

$router->get('/admin/requests',                 [AdminRequestsController::class,   'index']);
$router->post('/admin/requests/status',         [AdminRequestsController::class,   'updateStatus']);
$router->get('/admin/requests/delete/:id',      [AdminRequestsController::class,   'delete']);

$router->get('/admin/events',                   [AdminEventsController::class,     'index']);
$router->get('/admin/events/create',            [AdminEventsController::class,     'create']);
$router->post('/admin/events/store',            [AdminEventsController::class,     'store']);
$router->get('/admin/events/edit/:id',          [AdminEventsController::class,     'edit']);
$router->post('/admin/events/update/:id',       [AdminEventsController::class,     'update']);
$router->get('/admin/events/delete/:id',        [AdminEventsController::class,     'delete']);

$router->get('/admin/subscribers',              [AdminSubscribersController::class, 'index']);
$router->get('/admin/subscribers/create',       [AdminSubscribersController::class, 'create']);
$router->post('/admin/subscribers/store',       [AdminSubscribersController::class, 'store']);
$router->get('/admin/subscribers/edit/:id',     [AdminSubscribersController::class, 'edit']);
$router->post('/admin/subscribers/update/:id',  [AdminSubscribersController::class, 'update']);
$router->get('/admin/subscribers/delete/:id',   [AdminSubscribersController::class, 'delete']);

$router->get('/admin/users',                    [AdminUsersController::class,      'index']);
$router->get('/admin/users/create',             [AdminUsersController::class,      'create']);
$router->post('/admin/users/store',             [AdminUsersController::class,      'store']);
$router->get('/admin/users/edit/:id',           [AdminUsersController::class,      'edit']);
$router->post('/admin/users/update/:id',        [AdminUsersController::class,      'update']);
$router->get('/admin/users/delete/:id',         [AdminUsersController::class,      'delete']);

$router->get('/admin/settings',                 [AdminSettingsController::class,   'index']);
$router->post('/admin/settings/update',         [AdminSettingsController::class,   'update']);

// ── Dispatch ──────────────────────────────────────────────────
$router->dispatch();
