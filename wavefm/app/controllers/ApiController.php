<?php
/**
 * API Controller — Handles JSON API endpoints
 */
class ApiController extends BaseController
{
    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // ── Listener Analytics API ───────────────────────────────────
    public function listenerAnalytics(): void
    {
        $model = new ListenerAnalyticsModel();
        $this->jsonResponse([
            'live_listeners'  => $model->getLiveListenerCount(),
            'daily_listeners' => $model->getDailyListenerCount(),
            'total_sessions'  => $model->getTotalSessionCount(),
        ]);
    }

    // ── Track Listener ───────────────────────────────────────────
    public function trackListener(): void
    {
        if (!isset($_SESSION['_initiated'])) {
            $_SESSION['_initiated'] = true;
        }

        $sessionId = session_id();
        $ipAddress = Security::getIp();
        $page      = Security::sanitize($_GET['page'] ?? $_SERVER['REQUEST_URI'] ?? '/');

        $model = new ListenerAnalyticsModel();
        $model->recordListener($sessionId, $ipAddress, $page);

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    // ── Subscribe to Newsletter ─────────────────────────────────
    public function subscribe(): void
    {
        if (!$this->isPost()) {
            $this->jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
        }

        $email = filter_var($this->post('email'), FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid email address'], 400);
        }

        $model = new SubscriberModel();
        
        // Check if already subscribed
        $existing = $model->getByEmail($email);
        if ($existing) {
            if ($existing['is_active']) {
                $this->jsonResponse(['success' => false, 'error' => 'Already subscribed'], 409);
            } else {
                // Re-activate previous subscriber
                $model->update($existing['id'], ['name' => $existing['name'], 'is_active' => 1]);
                $this->jsonResponse(['success' => true, 'message' => 'Welcome back! You\'ve been resubscribed.']);
            }
        }

        $data = [
            'email' => $email,
            'name'  => Security::sanitize($this->post('name') ?? ''),
        ];
        
        try {
            $model->create($data);
            $this->jsonResponse(['success' => true, 'message' => 'Thank you for subscribing!']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => 'Subscription failed'], 500);
        }
    }
}
