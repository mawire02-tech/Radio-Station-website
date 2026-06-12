<?php
// ── AdminAuthController ───────────────────────────────────────
class AdminAuthController extends BaseController
{
    public function loginPage(): void
    {
        if (Security::isLoggedIn()) {
            $this->redirect(BASE_URL . 'admin/dashboard');
        }
        $this->render('admin/login', [
            'csrf'       => Security::csrfField(),
            'flash'      => $this->getFlash(),
            'page_title' => 'Admin Login',
        ], 'admin');
    }

    public function login(): void
    {
        if (!$this->isPost()) { $this->redirect(BASE_URL . 'admin/login'); }

        if (!Security::verifyCsrf($this->post(CSRF_TOKEN_NAME))) {
            $this->setFlash('error', 'Invalid request.');
            $this->redirect(BASE_URL . 'admin/login');
        }

        $username = Security::sanitize($this->post('username'));
        $password = $this->post('password');
        $ip       = Security::getIp();

        if (!Security::checkLoginAttempts($username . $ip)) {
            Logger::security("Login blocked (locked out): $username from $ip");
            $this->setFlash('error', 'Too many failed attempts. Please wait 15 minutes.');
            $this->redirect(BASE_URL . 'admin/login');
        }

        if (empty($username) || empty($password)) {
            $this->setFlash('error', 'Username and password are required.');
            $this->redirect(BASE_URL . 'admin/login');
        }

        $userModel = new UserModel();
        $user      = $userModel->getByUsername($username);

        if (!$user || !Security::verifyPassword($password, $user['password'])) {
            Security::incrementLoginAttempts($username . $ip);
            Logger::security("Failed login: $username from $ip");
            $this->setFlash('error', 'Invalid username or password.');
            $this->redirect(BASE_URL . 'admin/login');
        }

        // Successful login
        Security::clearLoginAttempts($username . $ip);
        session_regenerate_id(true);

        $_SESSION['admin_id']       = (int)$user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_name']     = $user['full_name'];
        $_SESSION['admin_role']     = $user['role'];
        $_SESSION['admin_login_time'] = time();

        $userModel->updateLastLogin((int)$user['id']);
        $userModel->logActivity((int)$user['id'], 'login', 'users', (int)$user['id'], 'Admin login');
        Logger::info("Admin login: {$user['username']} from $ip");

        $this->redirect(BASE_URL . 'admin/dashboard');
    }

    public function logout(): void
    {
        if (Security::isLoggedIn()) {
            $uid = $_SESSION['admin_id'];
            (new UserModel())->logActivity($uid, 'logout');
        }
        session_unset();
        session_destroy();
        $this->redirect(BASE_URL . 'admin/login');
    }
}

// ── AdminDashboardController ──────────────────────────────────
class AdminDashboardController extends BaseController
{
    public function index(): void
    {
        Security::requireAuth();
        $db = Database::getInstance();

        $stats = [
            'news_count'       => (int)($db->fetchOne("SELECT COUNT(*) AS c FROM news")['c'] ?? 0),
            'presenter_count'  => (int)($db->fetchOne("SELECT COUNT(*) AS c FROM presenters")['c'] ?? 0),
            'show_count'       => (int)($db->fetchOne("SELECT COUNT(*) AS c FROM shows")['c'] ?? 0),
            'request_count'    => (int)($db->fetchOne("SELECT COUNT(*) AS c FROM music_requests WHERE status='pending'")['c'] ?? 0),
            'feedback_unread'  => (new FeedbackModel())->countUnread(),
            'podcast_count'    => (int)($db->fetchOne("SELECT COUNT(*) AS c FROM podcasts")['c'] ?? 0),
        ];

        $recent_news     = (new NewsModel())->getAll(1, 5);
        $recent_requests = (new RequestModel())->getAll(1, 5);
        $activity_log    = $db->fetchAll(
            "SELECT al.*, u.username FROM activity_log al LEFT JOIN users u ON u.id = al.user_id ORDER BY al.created_at DESC LIMIT 10"
        );

        $this->render('admin/dashboard', [
            'stats'           => $stats,
            'recent_news'     => $recent_news,
            'recent_requests' => $recent_requests,
            'activity_log'    => $activity_log,
            'page_title'      => 'Dashboard',
        ], 'admin');
    }
}

// ── AdminNewsController ───────────────────────────────────────
class AdminNewsController extends BaseController
{
    private NewsModel $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new NewsModel();
    }

    public function index(): void
    {
        Security::requireAuth();
        $page   = max(1, $this->intParam('page'));
        $search = Security::sanitize($this->get('q'));
        $this->render('admin/news/index', [
            'articles'   => $this->model->getAll($page, ADMIN_PER_PAGE, $search ?: null),
            'total'      => $this->model->countAll($search ?: null),
            'page'       => $page,
            'per_page'   => ADMIN_PER_PAGE,
            'search'     => $search,
            'flash'      => $this->getFlash(),
            'page_title' => 'Manage News',
        ], 'admin');
    }

    public function create(): void
    {
        Security::requireAuth();
        $this->render('admin/news/form', [
            'article'    => null,
            'categories' => $this->model->getCategories(),
            'csrf'       => Security::csrfField(),
            'page_title' => 'Add Article',
        ], 'admin');
    }

    public function store(): void
    {
        Security::requireAuth();
        if (!$this->isPost() || !Security::verifyCsrf($this->post(CSRF_TOKEN_NAME))) {
            $this->setFlash('error', 'Invalid request.'); $this->redirect(BASE_URL . 'admin/news'); return;
        }

        $data = $this->collectFormData();
        $errors = Security::validateRequired($data, ['title','excerpt','body','category_id']);
        if (!empty($errors)) {
            $this->setFlash('error', implode(' ', $errors));
            $this->redirect(BASE_URL . 'admin/news/create'); return;
        }

        $data['slug']      = $this->uniqueSlug($data['title']);
        $data['author_id'] = $_SESSION['admin_id'];
        $data['image']     = $this->handleImageUpload() ?? null;

        $id = $this->model->create($data);
        (new UserModel())->logActivity($_SESSION['admin_id'], 'create', 'news', $id, $data['title']);
        $this->setFlash('success', 'Article created successfully.');
        $this->redirect(BASE_URL . 'admin/news');
    }

    public function edit(int $id): void
    {
        Security::requireAuth();
        $article = $this->model->getById($id);
        if (!$article) { $this->setFlash('error', 'Article not found.'); $this->redirect(BASE_URL . 'admin/news'); return; }
        $this->render('admin/news/form', [
            'article'    => $article,
            'categories' => $this->model->getCategories(),
            'csrf'       => Security::csrfField(),
            'page_title' => 'Edit Article',
        ], 'admin');
    }

    public function update(int $id): void
    {
        Security::requireAuth();
        if (!$this->isPost() || !Security::verifyCsrf($this->post(CSRF_TOKEN_NAME))) {
            $this->setFlash('error', 'Invalid request.'); $this->redirect(BASE_URL . 'admin/news'); return;
        }

        $article = $this->model->getById($id);
        if (!$article) { $this->setFlash('error', 'Not found.'); $this->redirect(BASE_URL . 'admin/news'); return; }

        $data = $this->collectFormData();
        $data['slug']  = Security::slugify($data['title']);
        $newImage      = $this->handleImageUpload();
        $data['image'] = $newImage ?? $article['image'];

        $this->model->update($id, $data);
        (new UserModel())->logActivity($_SESSION['admin_id'], 'update', 'news', $id, $data['title']);
        $this->setFlash('success', 'Article updated.');
        $this->redirect(BASE_URL . 'admin/news');
    }

    public function delete(int $id): void
    {
        Security::requireAuth();
        Security::requireRole('admin');
        if ($this->model->delete($id)) {
            (new UserModel())->logActivity($_SESSION['admin_id'], 'delete', 'news', $id);
            $this->setFlash('success', 'Article deleted.');
        } else {
            $this->setFlash('error', 'Could not delete article.');
        }
        $this->redirect(BASE_URL . 'admin/news');
    }

    private function collectFormData(): array
    {
        return [
            'category_id' => $this->intParam('category_id', 'post'),
            'title'       => Security::sanitize($this->post('title')),
            'excerpt'     => Security::sanitize($this->post('excerpt')),
            'body'        => $this->post('body'), // Sanitized differently as it contains HTML
            'status'      => in_array($this->post('status'), ['draft','published','archived']) ? $this->post('status') : 'draft',
            'featured'    => $this->post('featured') === '1' ? 1 : 0,
        ];
    }

    private function uniqueSlug(string $title): string
    {
        $base = Security::slugify($title);
        $slug = $base;
        $i    = 1;
        $db   = Database::getInstance();
        while ($db->fetchOne("SELECT id FROM news WHERE slug=?", [$slug])) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    private function handleImageUpload(): ?string
    {
        if (empty($_FILES['image']['name'])) return null;
        $file   = $_FILES['image'];
        $errors = Security::validateUpload($file, ALLOWED_IMG_TYPES, MAX_UPLOAD_SIZE);
        if (!empty($errors)) { $this->setFlash('warning', implode(' ', $errors)); return null; }

        $filename = Security::sanitizeFilename($file['name']);
        $dest     = UPLOAD_PATH . $filename;
        if (!is_dir(UPLOAD_PATH)) mkdir(UPLOAD_PATH, 0755, true);
        if (move_uploaded_file($file['tmp_name'], $dest)) return $filename;
        return null;
    }
}

// ── AdminPresentersController ─────────────────────────────────
class AdminPresentersController extends BaseController
{
    private PresenterModel $model;

    public function __construct() { parent::__construct(); $this->model = new PresenterModel(); }

    public function index(): void
    {
        Security::requireAuth();
        $page = max(1, $this->intParam('page'));
        $this->render('admin/presenters/index', [
            'presenters' => $this->model->getAll($page),
            'total'      => $this->model->countAll(),
            'page'       => $page,
            'per_page'   => ADMIN_PER_PAGE,
            'flash'      => $this->getFlash(),
            'page_title' => 'Manage Presenters',
        ], 'admin');
    }

    public function create(): void
    {
        Security::requireAuth();
        $this->render('admin/presenters/form', ['presenter'=>null,'csrf'=>Security::csrfField(),'page_title'=>'Add Presenter'], 'admin');
    }

    public function store(): void
    {
        Security::requireAuth();
        if (!$this->isPost() || !Security::verifyCsrf($this->post(CSRF_TOKEN_NAME))) {
            $this->redirect(BASE_URL . 'admin/presenters'); return;
        }
        $data = $this->collectFormData();
        $data['slug']  = Security::slugify($data['name']);
        $data['photo'] = $this->handleImageUpload() ?? null;
        $id = $this->model->create($data);
        (new UserModel())->logActivity($_SESSION['admin_id'], 'create', 'presenters', $id, $data['name']);
        $this->setFlash('success', 'Presenter added.');
        $this->redirect(BASE_URL . 'admin/presenters');
    }

    public function edit(int $id): void
    {
        Security::requireAuth();
        $p = $this->model->getById($id);
        if (!$p) { $this->setFlash('error','Not found.'); $this->redirect(BASE_URL . 'admin/presenters'); return; }
        $this->render('admin/presenters/form', ['presenter'=>$p,'csrf'=>Security::csrfField(),'page_title'=>'Edit Presenter'], 'admin');
    }

    public function update(int $id): void
    {
        Security::requireAuth();
        if (!$this->isPost() || !Security::verifyCsrf($this->post(CSRF_TOKEN_NAME))) {
            $this->redirect(BASE_URL . 'admin/presenters'); return;
        }
        $p = $this->model->getById($id);
        $data = $this->collectFormData();
        $data['slug']      = Security::slugify($data['name']);
        $data['is_active'] = $this->post('is_active') === '1' ? 1 : 0;
        $data['photo']     = $this->handleImageUpload() ?? ($p['photo'] ?? null);
        $this->model->update($id, $data);
        $this->setFlash('success', 'Presenter updated.');
        $this->redirect(BASE_URL . 'admin/presenters');
    }

    public function delete(int $id): void
    {
        Security::requireAuth(); Security::requireRole('admin');
        $this->model->delete($id);
        $this->setFlash('success', 'Presenter deleted.');
        $this->redirect(BASE_URL . 'admin/presenters');
    }

    private function collectFormData(): array
    {
        return [
            'name'        => Security::sanitize($this->post('name')),
            'bio'         => Security::sanitize($this->post('bio')),
            'role'        => Security::sanitize($this->post('role')),
            'email'       => Security::sanitizeEmail($this->post('email')),
            'twitter'     => Security::sanitize($this->post('twitter')),
            'instagram'   => Security::sanitize($this->post('instagram')),
            'facebook'    => Security::sanitize($this->post('facebook')),
            'sort_order'  => $this->intParam('sort_order', 'post'),
        ];
    }

    private function handleImageUpload(): ?string
    {
        if (empty($_FILES['photo']['name'])) return null;
        $errors = Security::validateUpload($_FILES['photo'], ALLOWED_IMG_TYPES, MAX_UPLOAD_SIZE);
        if (!empty($errors)) return null;
        $filename = Security::sanitizeFilename($_FILES['photo']['name']);
        if (!is_dir(UPLOAD_PATH)) mkdir(UPLOAD_PATH, 0755, true);
        if (move_uploaded_file($_FILES['photo']['tmp_name'], UPLOAD_PATH . $filename)) return $filename;
        return null;
    }
}

// ── AdminShowsController ──────────────────────────────────────
class AdminShowsController extends BaseController
{
    private ShowModel $model;

    public function __construct() { parent::__construct(); $this->model = new ShowModel(); }

    public function index(): void
    {
        Security::requireAuth();
        $page = max(1, $this->intParam('page'));
        $this->render('admin/shows/index', [
            'shows'    => $this->model->getAll($page),
            'total'    => $this->model->countAll(),
            'page'     => $page, 'per_page' => ADMIN_PER_PAGE,
            'flash'    => $this->getFlash(), 'page_title' => 'Manage Shows',
        ], 'admin');
    }

    public function create(): void
    {
        Security::requireAuth();
        $this->render('admin/shows/form', [
            'show'        => null,
            'presenters'  => (new PresenterModel())->getActive(),
            'genres'      => $this->model->getGenres(),
            'csrf'        => Security::csrfField(),
            'page_title'  => 'Add Show',
        ], 'admin');
    }

    public function store(): void
    {
        Security::requireAuth();
        if (!$this->isPost() || !Security::verifyCsrf($this->post(CSRF_TOKEN_NAME))) {
            $this->redirect(BASE_URL . 'admin/shows'); return;
        }
        $data = [
            'presenter_id' => $this->intParam('presenter_id','post'),
            'genre_id'     => $this->intParam('genre_id','post'),
            'title'        => Security::sanitize($this->post('title')),
            'slug'         => Security::slugify($this->post('title')),
            'description'  => Security::sanitize($this->post('description')),
            'image'        => null,
        ];
        $id = $this->model->create($data);
        (new UserModel())->logActivity($_SESSION['admin_id'], 'create', 'shows', $id, $data['title']);
        $this->setFlash('success', 'Show created.');
        $this->redirect(BASE_URL . 'admin/shows');
    }

    public function edit(int $id): void
    {
        Security::requireAuth();
        $show = $this->model->getById($id);
        if (!$show) { $this->setFlash('error','Not found.'); $this->redirect(BASE_URL . 'admin/shows'); return; }
        $this->render('admin/shows/form', [
            'show'       => $show,
            'presenters' => (new PresenterModel())->getActive(),
            'genres'     => $this->model->getGenres(),
            'csrf'       => Security::csrfField(),
            'page_title' => 'Edit Show',
        ], 'admin');
    }

    public function update(int $id): void
    {
        Security::requireAuth();
        if (!$this->isPost() || !Security::verifyCsrf($this->post(CSRF_TOKEN_NAME))) {
            $this->redirect(BASE_URL . 'admin/shows'); return;
        }
        $show = $this->model->getById($id);
        $data = [
            'presenter_id' => $this->intParam('presenter_id','post'),
            'genre_id'     => $this->intParam('genre_id','post'),
            'title'        => Security::sanitize($this->post('title')),
            'slug'         => Security::slugify($this->post('title')),
            'description'  => Security::sanitize($this->post('description')),
            'image'        => $show['image'],
            'is_active'    => $this->post('is_active') === '1' ? 1 : 0,
        ];
        $this->model->update($id, $data);
        $this->setFlash('success', 'Show updated.');
        $this->redirect(BASE_URL . 'admin/shows');
    }

    public function delete(int $id): void
    {
        Security::requireAuth(); Security::requireRole('admin');
        $this->model->delete($id);
        $this->setFlash('success', 'Show deleted.');
        $this->redirect(BASE_URL . 'admin/shows');
    }
}

// ── AdminRequestsController ───────────────────────────────────
class AdminRequestsController extends BaseController
{
    public function index(): void
    {
        Security::requireAuth();
        $page   = max(1, $this->intParam('page'));
        $status = in_array($this->get('status'),['','pending','approved','played','rejected']) ? $this->get('status') : '';
        $model  = new RequestModel();
        $this->render('admin/requests/index', [
            'requests'   => $model->getAll($page, ADMIN_PER_PAGE, $status),
            'total'      => $model->countAll($status),
            'page'       => $page, 'per_page' => ADMIN_PER_PAGE,
            'status'     => $status,
            'flash'      => $this->getFlash(),
            'page_title' => 'Music Requests',
        ], 'admin');
    }

    public function updateStatus(): void
    {
        Security::requireAuth();
        if (!$this->isPost()) { $this->redirect(BASE_URL . 'admin/requests'); return; }
        $id     = $this->intParam('id','post');
        $status = $this->post('status');
        if (in_array($status,['pending','approved','played','rejected'])) {
            (new RequestModel())->updateStatus($id, $status);
        }
        if ($this->isAjax()) { $this->renderJson(['ok'=>true]); }
        $this->setFlash('success','Status updated.');
        $this->redirect(BASE_URL . 'admin/requests');
    }

    public function delete(int $id): void
    {
        Security::requireAuth(); Security::requireRole('admin');
        (new RequestModel())->delete($id);
        $this->setFlash('success','Request deleted.');
        $this->redirect(BASE_URL . 'admin/requests');
    }
}

// ── AdminPodcastsController ───────────────────────────────────
class AdminPodcastsController extends BaseController
{
    private PodcastModel $model;

    public function __construct() { parent::__construct(); $this->model = new PodcastModel(); }

    public function index(): void
    {
        Security::requireAuth();
        $page = max(1, $this->intParam('page'));
        $this->render('admin/podcasts/index', [
            'podcasts'   => $this->model->getAll($page),
            'total'      => $this->model->countAll(),
            'page'       => $page, 'per_page' => ADMIN_PER_PAGE,
            'flash'      => $this->getFlash(), 'page_title' => 'Manage Podcasts',
        ], 'admin');
    }

    public function create(): void
    {
        Security::requireAuth();
        $this->render('admin/podcasts/form', [
            'podcast'    => null,
            'shows'      => (new ShowModel())->getActive(),
            'csrf'       => Security::csrfField(),
            'page_title' => 'Add Podcast',
        ], 'admin');
    }

    public function store(): void
    {
        Security::requireAuth();
        if (!$this->isPost() || !Security::verifyCsrf($this->post(CSRF_TOKEN_NAME))) {
            $this->redirect(BASE_URL . 'admin/podcasts'); return;
        }
        $audio = $this->handleAudioUpload();
        if (!$audio) { $this->setFlash('error', 'Audio file is required and must be a valid audio format.'); $this->redirect(BASE_URL . 'admin/podcasts/create'); return; }
        $data = [
            'show_id'      => $this->intParam('show_id','post'),
            'title'        => Security::sanitize($this->post('title')),
            'description'  => Security::sanitize($this->post('description')),
            'audio_file'   => $audio['filename'],
            'duration'     => $this->intParam('duration','post'),
            'file_size'    => $audio['size'],
            'published_at' => $this->post('published_at') ?: date('Y-m-d H:i:s'),
        ];
        $id = $this->model->create($data);
        (new UserModel())->logActivity($_SESSION['admin_id'], 'create', 'podcasts', $id, $data['title']);
        $this->setFlash('success', 'Podcast uploaded.');
        $this->redirect(BASE_URL . 'admin/podcasts');
    }

    public function delete(int $id): void
    {
        Security::requireAuth(); Security::requireRole('admin');
        $pod = $this->model->getById($id);
        if ($pod) {
            $audioPath = PUBLIC_PATH . 'uploads/' . $pod['audio_file'];
            if (file_exists($audioPath)) unlink($audioPath);
            $this->model->delete($id);
        }
        $this->setFlash('success', 'Podcast deleted.');
        $this->redirect(BASE_URL . 'admin/podcasts');
    }

    private function handleAudioUpload(): ?array
    {
        if (empty($_FILES['audio_file']['name'])) return null;
        $errors = Security::validateUpload($_FILES['audio_file'], ALLOWED_AUDIO_TYPES, MAX_UPLOAD_SIZE);
        if (!empty($errors)) return null;
        $filename = Security::sanitizeFilename($_FILES['audio_file']['name']);
        $audioDir = PUBLIC_PATH . 'uploads/';
        if (!is_dir($audioDir)) mkdir($audioDir, 0755, true);
        if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $audioDir . $filename)) {
            return ['filename' => $filename, 'size' => $_FILES['audio_file']['size']];
        }
        return null;
    }
}

// ── AdminUsersController ──────────────────────────────────────
class AdminUsersController extends BaseController
{
    private UserModel $model;

    public function __construct() { parent::__construct(); $this->model = new UserModel(); }

    public function index(): void
    {
        Security::requireRole('superadmin');
        $page = max(1, $this->intParam('page'));
        $this->render('admin/users/index', [
            'users'      => $this->model->getAll($page),
            'total'      => $this->model->countAll(),
            'page'       => $page, 'per_page' => ADMIN_PER_PAGE,
            'flash'      => $this->getFlash(), 'page_title' => 'Manage Users',
        ], 'admin');
    }

    public function create(): void
    {
        Security::requireRole('superadmin');
        $this->render('admin/users/form', ['user'=>null,'csrf'=>Security::csrfField(),'page_title'=>'Add User'], 'admin');
    }

    public function store(): void
    {
        Security::requireRole('superadmin');
        if (!$this->isPost() || !Security::verifyCsrf($this->post(CSRF_TOKEN_NAME))) {
            $this->redirect(BASE_URL . 'admin/users'); return;
        }
        $data = $this->collectFormData();
        $errors = Security::validateRequired($data, ['username','email','full_name','password']);
        if (!Security::validateEmail($data['email'])) $errors['email'] = 'Invalid email.';
        if (!empty($data['password']) && !Security::passwordStrength($data['password'])) {
            $errors['password'] = 'Password must be 8+ characters with uppercase, lowercase, number and special character.';
        }
        if (!empty($errors)) { $this->setFlash('error', implode(' ', $errors)); $this->redirect(BASE_URL . 'admin/users/create'); return; }
        $this->model->create($data);
        $this->setFlash('success', 'User created.');
        $this->redirect(BASE_URL . 'admin/users');
    }

    public function edit(int $id): void
    {
        Security::requireRole('superadmin');
        $user = $this->model->getById($id);
        if (!$user) { $this->setFlash('error','Not found.'); $this->redirect(BASE_URL . 'admin/users'); return; }
        $this->render('admin/users/form', ['user'=>$user,'csrf'=>Security::csrfField(),'page_title'=>'Edit User'], 'admin');
    }

    public function update(int $id): void
    {
        Security::requireRole('superadmin');
        if (!$this->isPost() || !Security::verifyCsrf($this->post(CSRF_TOKEN_NAME))) {
            $this->redirect(BASE_URL . 'admin/users'); return;
        }
        $data = $this->collectFormData();
        $this->model->update($id, $data);
        $this->setFlash('success', 'User updated.');
        $this->redirect(BASE_URL . 'admin/users');
    }

    public function delete(int $id): void
    {
        Security::requireRole('superadmin');
        if ($id === $_SESSION['admin_id']) { $this->setFlash('error','Cannot delete yourself.'); $this->redirect(BASE_URL . 'admin/users'); return; }
        $this->model->delete($id);
        $this->setFlash('success', 'User deleted.');
        $this->redirect(BASE_URL . 'admin/users');
    }

    private function collectFormData(): array
    {
        return [
            'username'  => Security::sanitize($this->post('username')),
            'email'     => Security::sanitizeEmail($this->post('email')),
            'full_name' => Security::sanitize($this->post('full_name')),
            'role'      => in_array($this->post('role'),['editor','admin','superadmin']) ? $this->post('role') : 'editor',
            'password'  => $this->post('password'),
            'is_active' => $this->post('is_active') === '1' ? 1 : 0,
        ];
    }
}

// ── AdminSettingsController ───────────────────────────────────
class AdminSettingsController extends BaseController
{
    public function index(): void
    {
        Security::requireRole('admin');
        $model = new SettingsModel();
        $this->render('admin/settings/index', [
            'settings'   => $model->getAll(),
            'flash'      => $this->getFlash(),
            'csrf'       => Security::csrfField(),
            'page_title' => 'Site Settings',
        ], 'admin');
    }

    public function update(): void
    {
        Security::requireRole('admin');
        if (!$this->isPost() || !Security::verifyCsrf($this->post(CSRF_TOKEN_NAME))) {
            $this->redirect(BASE_URL . 'admin/settings'); return;
        }
        $allowed = ['station_name','station_tagline','station_frequency','station_email','station_phone',
                    'station_address','stream_url','stream_backup_url','facebook_url','twitter_url',
                    'instagram_url','youtube_url','about_mission','about_history','meta_description'];
        $model = new SettingsModel();
        foreach ($allowed as $key) {
            if (isset($_POST[$key])) {
                $model->set($key, Security::sanitize($_POST[$key]));
            }
        }
        
        // Handle logo upload
        if (!empty($_FILES['station_logo']['name'])) {
            $file = $_FILES['station_logo'];
            $errors = Security::validateUpload($file, ALLOWED_IMG_TYPES, MAX_UPLOAD_SIZE);
            if (empty($errors)) {
                $filename = Security::sanitizeFilename($file['name']);
                $dest = UPLOAD_PATH . $filename;
                if (!is_dir(UPLOAD_PATH)) mkdir(UPLOAD_PATH, 0755, true);
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $model->set('station_logo', $filename);
                }
            } else {
                $this->setFlash('warning', 'Logo upload failed: ' . implode(' ', $errors));
            }
        }
        
        (new UserModel())->logActivity($_SESSION['admin_id'], 'update', 'settings');
        $this->setFlash('success', 'Settings saved.');
        $this->redirect(BASE_URL . 'admin/settings');
    }
}

// ── AdminEventsController ─────────────────────────────────────
class AdminEventsController extends BaseController
{
    private EventModel $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new EventModel();
    }

    public function index(): void
    {
        Security::requireAuth();
        $page   = max(1, $this->intParam('page'));
        $search = Security::sanitize($this->get('q'));
        $this->render('admin/events/index', [
            'events'     => $this->model->getAll($page, ADMIN_PER_PAGE, $search ?: null),
            'total'      => $this->model->countAll($search ?: null),
            'page'       => $page,
            'per_page'   => ADMIN_PER_PAGE,
            'search'     => $search,
            'flash'      => $this->getFlash(),
            'page_title' => 'Manage Events',
        ], 'admin');
    }

    public function create(): void
    {
        Security::requireAuth();
        $this->render('admin/events/form', [
            'event'      => null,
            'csrf'       => Security::csrfField(),
            'page_title' => 'Add Event',
        ], 'admin');
    }

    public function store(): void
    {
        Security::requireAuth();
        if (!$this->isPost() || !Security::verifyCsrf($this->post(CSRF_TOKEN_NAME))) {
            $this->setFlash('error', 'Invalid request.');
            $this->redirect(BASE_URL . 'admin/events');
            return;
        }

        $data = [
            'title'       => Security::sanitize($this->post('title')),
            'description' => $this->post('description'),
            'location'    => Security::sanitize($this->post('location')),
            'event_date'  => $this->post('event_date'),
            'start_time'  => $this->post('start_time') ?: null,
            'end_time'    => $this->post('end_time') ?: null,
            'is_featured' => $this->post('is_featured') === '1' ? 1 : 0,
            'created_by'  => $_SESSION['admin_id'],
        ];

        $errors = Security::validateRequired($data, ['title', 'event_date']);
        if (!empty($errors)) {
            $this->setFlash('error', implode(' ', $errors));
            $this->redirect(BASE_URL . 'admin/events/create');
            return;
        }

        $id = $this->model->create($data);
        (new UserModel())->logActivity($_SESSION['admin_id'], 'create', 'events', $id, $data['title']);
        $this->setFlash('success', 'Event created successfully.');
        $this->redirect(BASE_URL . 'admin/events');
    }

    public function edit(int $id): void
    {
        Security::requireAuth();
        $event = $this->model->getById($id);
        if (!$event) {
            $this->setFlash('error', 'Event not found.');
            $this->redirect(BASE_URL . 'admin/events');
            return;
        }
        $this->render('admin/events/form', [
            'event'      => $event,
            'csrf'       => Security::csrfField(),
            'page_title' => 'Edit Event',
        ], 'admin');
    }

    public function update(int $id): void
    {
        Security::requireAuth();
        if (!$this->isPost() || !Security::verifyCsrf($this->post(CSRF_TOKEN_NAME))) {
            $this->setFlash('error', 'Invalid request.');
            $this->redirect(BASE_URL . 'admin/events');
            return;
        }

        $event = $this->model->getById($id);
        if (!$event) {
            $this->setFlash('error', 'Event not found.');
            $this->redirect(BASE_URL . 'admin/events');
            return;
        }

        $data = [
            'title'       => Security::sanitize($this->post('title')),
            'description' => $this->post('description'),
            'location'    => Security::sanitize($this->post('location')),
            'event_date'  => $this->post('event_date'),
            'start_time'  => $this->post('start_time') ?: null,
            'end_time'    => $this->post('end_time') ?: null,
            'is_featured' => $this->post('is_featured') === '1' ? 1 : 0,
        ];

        $this->model->update($id, $data);
        (new UserModel())->logActivity($_SESSION['admin_id'], 'update', 'events', $id, $data['title']);
        $this->setFlash('success', 'Event updated successfully.');
        $this->redirect(BASE_URL . 'admin/events');
    }

    public function delete(int $id): void
    {
        Security::requireAuth();
        if ($this->model->delete($id)) {
            (new UserModel())->logActivity($_SESSION['admin_id'], 'delete', 'events', $id);
            $this->setFlash('success', 'Event deleted successfully.');
        } else {
            $this->setFlash('error', 'Could not delete event.');
        }
        $this->redirect(BASE_URL . 'admin/events');
    }
}

// ── AdminSubscribersController ────────────────────────────────
class AdminSubscribersController extends BaseController
{
    private SubscriberModel $model;

    public function __construct() { parent::__construct(); $this->model = new SubscriberModel(); }

    public function index(): void
    {
        Security::requireAuth();
        $page = max(1, $this->intParam('page'));
        $search = Security::sanitize($this->get('q'));
        
        $this->render('admin/subscribers/index', [
            'subscribers' => $this->model->getAll($page, ADMIN_PER_PAGE, $search),
            'total'       => $this->model->countAll($search),
            'page'        => $page,
            'per_page'    => ADMIN_PER_PAGE,
            'search'      => $search,
            'count'       => $this->model->getActiveCount(),
            'flash'       => $this->getFlash(),
            'page_title'  => 'Newsletter Subscribers',
        ], 'admin');
    }

    public function create(): void
    {
        Security::requireAuth();
        $this->render('admin/subscribers/form', [
            'subscriber' => null,
            'csrf'       => Security::csrfField(),
            'page_title' => 'Add Subscriber',
        ], 'admin');
    }

    public function store(): void
    {
        Security::requireAuth();
        if (!$this->isPost() || !Security::verifyCsrf($this->post(CSRF_TOKEN_NAME))) {
            $this->redirect(BASE_URL . 'admin/subscribers');
            return;
        }

        $email = filter_var($this->post('email'), FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'Invalid email address.');
            $this->redirect(BASE_URL . 'admin/subscribers/create');
            return;
        }

        // Check if already exists
        if ($this->model->getByEmail($email)) {
            $this->setFlash('error', 'This email is already subscribed.');
            $this->redirect(BASE_URL . 'admin/subscribers/create');
            return;
        }

        $data = [
            'email' => $email,
            'name'  => Security::sanitize($this->post('name')),
        ];
        
        $id = $this->model->create($data);
        (new UserModel())->logActivity($_SESSION['admin_id'], 'create', 'subscribers', $id, $email);
        $this->setFlash('success', 'Subscriber added successfully.');
        $this->redirect(BASE_URL . 'admin/subscribers');
    }

    public function edit(int $id): void
    {
        Security::requireAuth();
        $subscriber = $this->model->getById($id);
        if (!$subscriber) {
            $this->setFlash('error', 'Subscriber not found.');
            $this->redirect(BASE_URL . 'admin/subscribers');
            return;
        }

        $this->render('admin/subscribers/form', [
            'subscriber' => $subscriber,
            'csrf'       => Security::csrfField(),
            'page_title' => 'Edit Subscriber',
        ], 'admin');
    }

    public function update(int $id): void
    {
        Security::requireAuth();
        if (!$this->isPost() || !Security::verifyCsrf($this->post(CSRF_TOKEN_NAME))) {
            $this->redirect(BASE_URL . 'admin/subscribers');
            return;
        }

        $subscriber = $this->model->getById($id);
        if (!$subscriber) {
            $this->setFlash('error', 'Subscriber not found.');
            $this->redirect(BASE_URL . 'admin/subscribers');
            return;
        }

        $data = [
            'name'      => Security::sanitize($this->post('name')),
            'is_active' => $this->post('is_active') === '1' ? 1 : 0,
        ];

        $this->model->update($id, $data);
        (new UserModel())->logActivity($_SESSION['admin_id'], 'update', 'subscribers', $id);
        $this->setFlash('success', 'Subscriber updated successfully.');
        $this->redirect(BASE_URL . 'admin/subscribers');
    }

    public function delete(int $id): void
    {
        Security::requireAuth();
        $subscriber = $this->model->getById($id);
        if (!$subscriber) {
            $this->setFlash('error', 'Subscriber not found.');
            $this->redirect(BASE_URL . 'admin/subscribers');
            return;
        }

        if ($this->model->delete($id)) {
            (new UserModel())->logActivity($_SESSION['admin_id'], 'delete', 'subscribers', $id);
            $this->setFlash('success', 'Subscriber unsubscribed.');
        }
        $this->redirect(BASE_URL . 'admin/subscribers');
    }
}
