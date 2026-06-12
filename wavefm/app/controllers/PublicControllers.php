<?php
// ── HomeController ───────────────────────────────────────────
class HomeController extends BaseController
{
    public function index(): void
    {
        $newsModel     = new NewsModel();
        $scheduleModel = new ScheduleModel();
        $podcastModel  = new PodcastModel();
        $eventModel    = new EventModel();
        $pollModel     = new PollModel();

        $dayOfWeek = (int)date('w'); // 0=Sun
        $this->render('home/index', [
            'featured_news'    => $newsModel->getFeatured(3),
            'schedule_today'   => $scheduleModel->getByDay($dayOfWeek),
            'recent_podcasts'  => $podcastModel->getRecent(4),
            'upcoming_events'  => $eventModel->getUpcoming(5),
            'poll_results'     => $pollModel->getResults(),
            'day_of_week'      => $dayOfWeek,
            'page_title'       => 'Home',
        ]);
    }
}

// ── NewsController ────────────────────────────────────────────
class NewsController extends BaseController
{
    private NewsModel $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new NewsModel();
    }

    public function index(): void
    {
        $page       = max(1, $this->intParam('page'));
        $search     = Security::sanitize($this->get('q'));
        $categoryId = $this->intParam('cat') ?: null;

        $this->render('news/index', [
            'articles'    => $this->model->getPublished($page, NEWS_PER_PAGE, $categoryId, $search ?: null),
            'total'       => $this->model->countPublished($categoryId, $search ?: null),
            'page'        => $page,
            'per_page'    => NEWS_PER_PAGE,
            'search'      => $search,
            'category_id' => $categoryId,
            'categories'  => $this->model->getCategories(),
            'archives'    => $this->model->getArchiveMonths(),
            'page_title'  => 'News',
        ]);
    }

    public function article(string $slug): void
    {
        $article = $this->model->getBySlug(Security::sanitize($slug));
        if (!$article) {
            $this->notFound();
            return;
        }
        $this->render('news/article', [
            'article'    => $article,
            'related'    => $this->model->getPublished(1, 3, (int)$article['category_id']),
            'categories' => $this->model->getCategories(),
            'archives'   => $this->model->getArchiveMonths(),
            'page_title' => htmlspecialchars($article['title']),
        ]);
    }

    private function notFound(): void
    {
        http_response_code(404);
        $this->render('errors/404', ['page_title' => '404 Not Found']);
    }
}

// ── ShowsController ───────────────────────────────────────────
class ShowsController extends BaseController
{
    public function index(): void
    {
        $scheduleModel = new ScheduleModel();
        $showModel     = new ShowModel();
        $podcastModel  = new PodcastModel();

        $this->render('shows/index', [
            'shows'          => $showModel->getActive(),
            'weekly_schedule'=> $scheduleModel->getWeekly(),
            'recent_podcasts'=> $podcastModel->getRecent(6),
            'page_title'     => 'Shows & Schedule',
        ]);
    }

    public function detail(string $slug): void
    {
        $showModel    = new ShowModel();
        $podcastModel = new PodcastModel();
        $show = $showModel->getBySlug(Security::sanitize($slug));
        if (!$show) {
            http_response_code(404);
            $this->render('errors/404', ['page_title' => '404 Not Found']);
            return;
        }
        $this->render('shows/detail', [
            'show'       => $show,
            'episodes'   => $podcastModel->getByShow((int)$show['id']),
            'page_title' => htmlspecialchars($show['title']),
        ]);
    }
}

// ── PresentersController ──────────────────────────────────────
class PresentersController extends BaseController
{
    public function index(): void
    {
        $model = new PresenterModel();
        $this->render('presenters/index', [
            'presenters' => $model->getActive(),
            'page_title' => 'Our Presenters',
        ]);
    }

    public function profile(string $slug): void
    {
        $model     = new PresenterModel();
        $showModel = new ShowModel();
        $presenter = $model->getBySlug(Security::sanitize($slug));
        if (!$presenter) {
            http_response_code(404);
            $this->render('errors/404', ['page_title' => '404 Not Found']);
            return;
        }
        $this->render('presenters/profile', [
            'presenter'  => $presenter,
            'page_title' => htmlspecialchars($presenter['name']),
        ]);
    }
}

// ── RequestsController ────────────────────────────────────────
class RequestsController extends BaseController
{
    public function index(): void
    {
        $showModel = new ShowModel();
        $pollModel = new PollModel();
        $flash     = $this->getFlash();

        $this->render('requests/index', [
            'shows'        => $showModel->getActive(),
            'poll_results' => $pollModel->getResults(),
            'has_voted'    => $pollModel->hasVoted(Security::getIp()),
            'flash'        => $flash,
            'csrf'         => Security::csrfField(),
            'page_title'   => 'Music Requests & Shout-Outs',
        ]);
    }

    public function submitRequest(): void
    {
        if (!$this->isPost()) { $this->redirect(BASE_URL . 'requests'); }

        if (!Security::verifyCsrf($this->post(CSRF_TOKEN_NAME))) {
            $this->setFlash('error', 'Invalid form submission. Please try again.');
            $this->redirect(BASE_URL . 'requests');
        }

        $ip = Security::getIp();
        $clean = Security::sanitizeArray($_POST, ['listener_name','song_title','artist','shoutout']);
        $clean['listener_email'] = Security::sanitizeEmail($this->post('listener_email'));
        $clean['show_id']        = $this->intParam('show_id', 'post') ?: null;
        $clean['ip_address']     = $ip;

        $errors = Security::validateRequired($clean, ['listener_name','song_title','artist']);
        if ($clean['listener_email'] && !Security::validateEmail($clean['listener_email'])) {
            $errors['listener_email'] = 'Invalid email address.';
        }

        if (!empty($errors)) {
            $this->setFlash('error', 'Please fix the errors: ' . implode(' ', $errors));
            $this->redirect(BASE_URL . 'requests');
            return;
        }

        // Duplicate check
        $model = new RequestModel();
        if ($model->checkDuplicate($ip, $clean['song_title'], $clean['artist'])) {
            $this->setFlash('warning', 'You already submitted this request recently. Please wait before trying again.');
            $this->redirect(BASE_URL . 'requests');
            return;
        }

        $model->create($clean);
        $this->setFlash('success', 'Your request has been submitted! 🎵 We\'ll try to play it on the next show.');
        $this->redirect(BASE_URL . 'requests');
    }

    public function submitFeedback(): void
    {
        if (!$this->isPost()) { $this->redirect(BASE_URL . 'requests'); }

        if (!Security::verifyCsrf($this->post(CSRF_TOKEN_NAME))) {
            $this->setFlash('error', 'Invalid form submission.');
            $this->redirect(BASE_URL . 'requests');
        }

        $clean = Security::sanitizeArray($_POST, ['name','subject','message','type']);
        $clean['email']      = Security::sanitizeEmail($this->post('email'));
        $clean['ip_address'] = Security::getIp();

        $errors = Security::validateRequired($clean, ['name','subject','message']);
        if (!empty($errors)) {
            $this->setFlash('error', implode(' ', $errors));
            $this->redirect(BASE_URL . 'requests');
            return;
        }

        (new FeedbackModel())->create($clean);
        $this->setFlash('success', 'Thank you for your feedback! We appreciate every message.');
        $this->redirect(BASE_URL . 'requests');
    }

    public function pollVote(): void
    {
        header('Content-Type: application/json');
        if (!$this->isPost()) { echo json_encode(['ok'=>false]); exit; }

        $genreId = $this->intParam('genre_id', 'post');
        $ip      = Security::getIp();
        $model   = new PollModel();

        if ($model->hasVoted($ip)) {
            echo json_encode(['ok'=>false,'message'=>'You have already voted this week.']);
            exit;
        }
        $ok = $model->vote($genreId, $ip);
        $results = $model->getResults();
        echo json_encode(['ok'=>$ok,'results'=>$results]);
        exit;
    }
}

// ── AboutController ───────────────────────────────────────────
class AboutController extends BaseController
{
    public function index(): void
    {
        $eventModel = new EventModel();
        $this->render('about/index', [
            'mission'         => $this->settings->get('about_mission'),
            'history'         => $this->settings->get('about_history'),
            'station_email'   => $this->settings->get('station_email'),
            'station_phone'   => $this->settings->get('station_phone'),
            'station_address' => $this->settings->get('station_address'),
            'upcoming_events' => $eventModel->getUpcoming(6),
            'flash'           => $this->getFlash(),
            'csrf'            => Security::csrfField(),
            'page_title'      => 'About WAVE FM',
        ]);
    }

    public function contact(): void
    {
        if (!$this->isPost()) { $this->redirect(BASE_URL . 'about'); }

        if (!Security::verifyCsrf($this->post(CSRF_TOKEN_NAME))) {
            $this->setFlash('error', 'Invalid form submission.');
            $this->redirect(BASE_URL . 'about');
        }

        $clean = Security::sanitizeArray($_POST, ['name','subject','message','type']);
        $clean['email']      = Security::sanitizeEmail($this->post('email'));
        $clean['ip_address'] = Security::getIp();

        $errors = Security::validateRequired($clean, ['name','subject','message']);
        if (!empty($errors)) {
            $this->setFlash('error', implode(' ', $errors));
            $this->redirect(BASE_URL . 'about');
            return;
        }

        (new FeedbackModel())->create($clean);
        $this->setFlash('success', 'Message received! We aim to respond within 48 hours.');
        $this->redirect(BASE_URL . 'about');
    }
}
