<?php
/**
 * UserController - User Dashboard Controller
 * File path: controllers/UserController.php
 */

class UserController
{
    private $app;
    private $db;
    private $auth;
    private $session;
    private $localization;
    private $user;
    private $quoteRequest;
    private $order;
    private $notification;
    private $validator;

    public function __construct($app)
    {
        $this->app = $app;
        $this->db = $app->getDatabase();
        $this->auth = $app->getAuth();
        $this->session = $app->getSession();
        $this->localization = $app->getLocalization();

        // Initialize models
        $this->user = new User($this->db);
        $this->quoteRequest = new QuoteRequest($this->db);
        $this->order = new Order($this->db);
        $this->notification = new Notification($this->db);
        $this->validator = new Validator([], $this->localization);

        // Check if user is logged in
        if (!$this->auth->isLoggedIn()) {
            header('Location: /auth/login');
            exit;
        }
    }

    /**
     * Display user dashboard
     */
    public function dashboard()
    {
        $currentUser = $this->auth->getCurrentUser();
        $language = $this->localization->getCurrentLanguage();

        // Get dashboard statistics
        $stats = [
            'total_quote_requests' => $this->quoteRequest->getUserRequestCount($currentUser['id']),
            'total_orders' => $this->order->getUserOrderCount($currentUser['id']),
            'pending_quotes' => $this->quoteRequest->getUserPendingCount($currentUser['id']),
            'active_orders' => $this->order->getUserActiveCount($currentUser['id'])
        ];

        // Get recent activities
        $recentQuotes = $this->quoteRequest->getUserRecent($currentUser['id'], 5);
        $recentOrders = $this->order->getUserRecent($currentUser['id'], 5);
        $notifications = $this->notification->getUserRecent($currentUser['id'], 10);

        // Get monthly activity data for charts
        $monthlyData = $this->getMonthlyActivityData($currentUser['id']);

        $this->renderView('user/dashboard', [
            'user' => $currentUser,
            'stats' => $stats,
            'recentQuotes' => $recentQuotes,
            'recentOrders' => $recentOrders,
            'notifications' => $notifications,
            'monthlyData' => $monthlyData,
            'pageTitle' => $this->localization->t('user.dashboard_title')
        ]);
    }

    /**
     * Display user profile page
     */
    public function profile()
    {
        $currentUser = $this->auth->getCurrentUser();

        // Handle profile update
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleProfileUpdate($currentUser['id']);
            return;
        }

        $this->renderView('user/profile', [
            'user' => $currentUser,
            'pageTitle' => $this->localization->t('user.profile_title')
        ]);
    }

    /**
     * Handle profile update
     */
    private function handleProfileUpdate($userId)
    {
        // Validate CSRF token
        if (!$this->session->validateCSRFToken($_POST['csrf_token'])) {
            $this->session->setFlash('error', $this->localization->t('general.invalid_token'));
            header('Location: /user/profile');
            exit;
        }

        // Define validation rules
        $rules = [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'max:20',
            'address' => 'max:500',
            'preferred_language' => 'required|in:en,ar'
        ];

        // Validate password if provided
        if (!empty($_POST['new_password'])) {
            $rules['current_password'] = 'required';
            $rules['new_password'] = 'required|min:8|max:255';
            $rules['confirm_password'] = 'required|matches:new_password';
        }

        $validator = new Validator($_POST, $this->localization);

        if (!$validator->validate($rules)) {
            $this->session->setFlash('error', implode('<br>', $validator->getErrors()));
            header('Location: /user/profile');
            exit;
        }

        // Check if email is already taken by another user
        if ($this->user->emailExists($_POST['email'], $userId)) {
            $this->session->setFlash('error', $this->localization->t('auth.email_taken'));
            header('Location: /user/profile');
            exit;
        }

        // Verify current password if changing password
        if (!empty($_POST['new_password'])) {
            $currentUser = $this->user->getById($userId);
            if (!password_verify($_POST['current_password'], $currentUser['password'])) {
                $this->session->setFlash('error', $this->localization->t('user.current_password_incorrect'));
                header('Location: /user/profile');
                exit;
            }
        }

        // Prepare update data
        $userData = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? null,
            'address' => $_POST['address'] ?? null,
            'preferred_language' => $_POST['preferred_language']
        ];

        // Add password if provided
        if (!empty($_POST['new_password'])) {
            $userData['password'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        }

        // Update user
        $success = $this->user->update($userId, $userData);

        if ($success) {
            // Update session if language changed
            if ($_POST['preferred_language'] !== $this->localization->getCurrentLanguage()) {
                $this->localization->setLanguage($_POST['preferred_language']);
            }

            $this->session->setFlash('success', $this->localization->t('user.profile_updated'));
        } else {
            $this->session->setFlash('error', $this->localization->t('user.profile_update_failed'));
        }

        header('Location: /user/profile');
        exit;
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $currentUser = $this->auth->getCurrentUser();
        $notificationId = $_POST['notification_id'] ?? null;

        if (!$notificationId) {
            echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
            exit;
        }

        // Verify notification belongs to user
        $notification = $this->notification->getById($notificationId);
        if (!$notification || $notification['user_id'] !== $currentUser['id']) {
            echo json_encode(['success' => false, 'message' => 'Notification not found']);
            exit;
        }

        $success = $this->notification->markAsRead($notificationId);

        echo json_encode(['success' => $success]);
        exit;
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $currentUser = $this->auth->getCurrentUser();
        $success = $this->notification->markAllAsRead($currentUser['id']);

        echo json_encode(['success' => $success]);
        exit;
    }

    /**
     * Get monthly activity data for charts
     */
    private function getMonthlyActivityData($userId)
    {
        $months = [];
        $quoteData = [];
        $orderData = [];

        // Get last 6 months data
        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $months[] = date('M Y', strtotime("-$i months"));

            $quoteData[] = $this->quoteRequest->getUserMonthlyCount($userId, $date);
            $orderData[] = $this->order->getUserMonthlyCount($userId, $date);
        }

        return [
            'months' => $months,
            'quotes' => $quoteData,
            'orders' => $orderData
        ];
    }

    /**
     * Render view with layout
     */
    private function renderView($view, $data = [])
    {
        // Add common data
        $data['isRtl'] = $this->localization->isRtl();
        $data['currentLanguage'] = $this->localization->getCurrentLanguage();
        $data['csrfToken'] = $this->session->generateCSRFToken();

        // Set layout
        $data['layout'] = 'dashboard';

        // Extract data for view
        extract($data);

        // Include layout
        include 'views/layouts/dashboard.php';
    }
}
?>
