<?php
/**
 * Admin Controller
 * File path: controllers/AdminController.php
 */

class AdminController
{
    private $db;
    private $auth;
    private $session;
    private $localization;
    private $validator;
    private $user;
    private $vendor;
    private $service;
    private $order;
    private $category;
    private $quoteRequest;

    public function __construct($app)
    {
        $this->db = $app->getDatabase();
        $this->auth = $app->getAuth();
        $this->session = $app->getSession();
        $this->localization = $app->getLocalization();
        $this->validator = new Validator([], $this->localization);

        // Initialize models
        $this->user = new User($this->db);
        $this->vendor = new Vendor($this->db);
        $this->service = new Service($this->db);
        $this->order = new Order($this->db);
        $this->category = new Category($this->db);
        $this->quoteRequest = new QuoteRequest($this->db);

        // Check admin access
        if (!$this->auth->isLoggedIn() || $this->auth->getCurrentUser()['role'] !== 'admin') {
            header('Location: /auth/login');
            exit;
        }
    }

    public function index()
    {
        $language = $this->localization->getCurrentLanguage();

        // Get dashboard statistics
        $stats = [
            'total_users' => $this->user->getTotalCount(),
            'total_vendors' => $this->vendor->getTotalCount(),
            'total_services' => $this->service->getTotalCount(),
            'total_orders' => $this->order->getTotalCount(),
            'total_quote_requests' => $this->quoteRequest->getTotalCount(),
            'pending_vendors' => $this->vendor->getPendingCount(),
            'recent_orders' => $this->order->getRecent(5),
            'recent_vendors' => $this->vendor->getRecent(5),
            'monthly_stats' => $this->getMonthlyStats()
        ];

        $this->renderView('admin/index', [
            'stats' => $stats,
            'currentPage' => 'dashboard'
        ]);
    }

    public function users()
    {
        $language = $this->localization->getCurrentLanguage();
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Handle filters
        $filters = [
            'role' => $_GET['role'] ?? null,
            'status' => $_GET['status'] ?? null,
            'search_term' => $_GET['q'] ?? null
        ];

        $users = $this->user->getAll($filters, $limit, $offset);
        $totalUsers = $this->user->getTotalCount($filters);
        $totalPages = ceil($totalUsers / $limit);

        // Handle user actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleUserAction();
            return;
        }

        $this->renderView('admin/users', [
            'users' => $users,
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers,
            'activeMenu' => 'users'
        ]);
    }

    public function vendors()
    {
        $language = $this->localization->getCurrentLanguage();
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Handle filters
        $filters = [
            'status' => $_GET['status'] ?? null,
            'subscription_status' => $_GET['subscription'] ?? null,
            'search_term' => $_GET['q'] ?? null
        ];

        $vendors = $this->vendor->getAll($language, $filters, $limit, $offset);
        $totalVendors = $this->vendor->getTotalCount($filters);
        $totalPages = ceil($totalVendors / $limit);

        // Handle vendor actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleVendorAction();
            return;
        }

        $this->renderView('admin/vendors', [
            'vendors' => $vendors,
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalVendors' => $totalVendors,
            'activeMenu' => 'vendors'
        ]);
    }

    public function services()
    {
        $language = $this->localization->getCurrentLanguage();
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Handle filters
        $filters = [
            'category_id' => $_GET['category'] ?? null,
            'vendor_id' => $_GET['vendor'] ?? null,
            'status' => $_GET['status'] ?? null,
            'search_term' => $_GET['q'] ?? null
        ];

        $services = $this->service->getAll($language, $filters, $limit, $offset);
        $totalServices = $this->service->getTotalCount($filters);
        $totalPages = ceil($totalServices / $limit);

        // Get categories for filter dropdown
        $categories = $this->category->getAll($language);

        // Handle service actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleServiceAction();
            return;
        }

        $this->renderView('admin/services', [
            'services' => $services,
            'categories' => $categories,
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalServices' => $totalServices,
            'activeMenu' => 'services'
        ]);
    }

    public function orders()
    {
        $language = $this->localization->getCurrentLanguage();
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Handle filters
        $filters = [
            'status' => $_GET['status'] ?? null,
            'payment_status' => $_GET['payment_status'] ?? null,
            'vendor_id' => $_GET['vendor'] ?? null,
            'search_term' => $_GET['q'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null
        ];

        $orders = $this->order->getAll($filters, $limit, $offset);
        $totalOrders = $this->order->getTotalCount($filters);
        $totalPages = ceil($totalOrders / $limit);

        // Handle order actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleOrderAction();
            return;
        }

        $this->renderView('admin/orders', [
            'orders' => $orders,
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalOrders' => $totalOrders,
            'activeMenu' => 'orders'
        ]);
    }

    public function reports()
    {
        $language = $this->localization->getCurrentLanguage();

        // Get date range from request
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01'); // First day of current month
        $dateTo = $_GET['date_to'] ?? date('Y-m-d'); // Today

        // Generate reports
        $reports = [
            'overview' => $this->generateOverviewReport($dateFrom, $dateTo),
            'vendors' => $this->generateVendorReport($dateFrom, $dateTo),
            'services' => $this->generateServiceReport($dateFrom, $dateTo),
            'orders' => $this->generateOrderReport($dateFrom, $dateTo),
            'quotes' => $this->generateQuoteReport($dateFrom, $dateTo)
        ];

        $this->renderView('admin/reports', [
            'reports' => $reports,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'activeMenu' => 'reports'
        ]);
    }

    public function settings()
    {
        // Handle settings update
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleSettingsUpdate();
            return;
        }

        // Get current settings
        $settings = $this->getSystemSettings();

        $this->renderView('admin/settings', [
            'settings' => $settings,
            'activeMenu' => 'settings'
        ]);
    }

    private function handleUserAction()
    {
        if (!$this->session->validateCSRFToken($_POST['csrf_token'])) {
            $this->session->setFlash('error', $this->localization->t('general.invalid_token'));
            return;
        }

        $action = $_POST['action'] ?? '';
        $userId = $_POST['user_id'] ?? null;

        switch ($action) {
            case 'activate':
                $success = $this->user->updateStatus($userId, 'active');
                $message = $success ? 'admin.user_activated' : 'admin.action_failed';
                break;

            case 'deactivate':
                $success = $this->user->updateStatus($userId, 'inactive');
                $message = $success ? 'admin.user_deactivated' : 'admin.action_failed';
                break;

            case 'delete':
                $success = $this->user->delete($userId);
                $message = $success ? 'admin.user_deleted' : 'admin.action_failed';
                break;

            default:
                $message = 'admin.invalid_action';
                $success = false;
        }

        $type = $success ? 'success' : 'error';
        $this->session->setFlash($type, $this->localization->t($message));

        header('Location: /admin/users');
        exit;
    }

    private function handleVendorAction()
    {
        if (!$this->session->validateCSRFToken($_POST['csrf_token'])) {
            $this->session->setFlash('error', $this->localization->t('general.invalid_token'));
            return;
        }

        $action = $_POST['action'] ?? '';
        $vendorId = $_POST['vendor_id'] ?? null;

        switch ($action) {
            case 'approve':
                $success = $this->vendor->updateStatus($vendorId, 'active');
                $message = $success ? 'admin.vendor_approved' : 'admin.action_failed';
                break;

            case 'suspend':
                $success = $this->vendor->updateStatus($vendorId, 'suspended');
                $message = $success ? 'admin.vendor_suspended' : 'admin.action_failed';
                break;

            case 'activate':
                $success = $this->vendor->updateStatus($vendorId, 'active');
                $message = $success ? 'admin.vendor_activated' : 'admin.action_failed';
                break;

            case 'delete':
                $success = $this->vendor->delete($vendorId);
                $message = $success ? 'admin.vendor_deleted' : 'admin.action_failed';
                break;

            default:
                $message = 'admin.invalid_action';
                $success = false;
        }

        $type = $success ? 'success' : 'error';
        $this->session->setFlash($type, $this->localization->t($message));

        header('Location: /admin/vendors');
        exit;
    }

    private function handleServiceAction()
    {
        if (!$this->session->validateCSRFToken($_POST['csrf_token'])) {
            $this->session->setFlash('error', $this->localization->t('general.invalid_token'));
            return;
        }

        $action = $_POST['action'] ?? '';
        $serviceId = $_POST['service_id'] ?? null;

        switch ($action) {
            case 'approve':
                $success = $this->service->updateStatus($serviceId, 'active');
                $message = $success ? 'admin.service_approved' : 'admin.action_failed';
                break;

            case 'suspend':
                $success = $this->service->updateStatus($serviceId, 'suspended');
                $message = $success ? 'admin.service_suspended' : 'admin.action_failed';
                break;

            case 'delete':
                $success = $this->service->delete($serviceId);
                $message = $success ? 'admin.service_deleted' : 'admin.action_failed';
                break;

            default:
                $message = 'admin.invalid_action';
                $success = false;
        }

        $type = $success ? 'success' : 'error';
        $this->session->setFlash($type, $this->localization->t($message));

        header('Location: /admin/services');
        exit;
    }

    private function handleOrderAction()
    {
        if (!$this->session->validateCSRFToken($_POST['csrf_token'])) {
            $this->session->setFlash('error', $this->localization->t('general.invalid_token'));
            return;
        }

        $action = $_POST['action'] ?? '';
        $orderId = $_POST['order_id'] ?? null;

        switch ($action) {
            case 'update_status':
                $status = $_POST['status'] ?? '';
                $success = $this->order->updateStatus($orderId, $status);
                $message = $success ? 'admin.order_updated' : 'admin.action_failed';
                break;

            case 'update_payment':
                $paymentStatus = $_POST['payment_status'] ?? '';
                $success = $this->order->updatePaymentStatus($orderId, $paymentStatus);
                $message = $success ? 'admin.payment_updated' : 'admin.action_failed';
                break;

            case 'cancel':
                $success = $this->order->updateStatus($orderId, 'cancelled');
                $message = $success ? 'admin.order_cancelled' : 'admin.action_failed';
                break;

            default:
                $message = 'admin.invalid_action';
                $success = false;
        }

        $type = $success ? 'success' : 'error';
        $this->session->setFlash($type, $this->localization->t($message));

        header('Location: /admin/orders');
        exit;
    }

    private function handleSettingsUpdate()
    {
        if (!$this->session->validateCSRFToken($_POST['csrf_token'])) {
            $this->session->setFlash('error', $this->localization->t('general.invalid_token'));
            return;
        }

        $settings = [
            'site_name' => $_POST['site_name'] ?? '',
            'site_description' => $_POST['site_description'] ?? '',
            'contact_email' => $_POST['contact_email'] ?? '',
            'contact_phone' => $_POST['contact_phone'] ?? '',
            'default_language' => $_POST['default_language'] ?? 'en',
            'vendor_free_quotes' => $_POST['vendor_free_quotes'] ?? 10,
            'subscription_price' => $_POST['subscription_price'] ?? 99,
            'maintenance_mode' => isset($_POST['maintenance_mode']) ? 1 : 0
        ];

        $success = $this->updateSystemSettings($settings);

        $type = $success ? 'success' : 'error';
        $message = $success ? 'admin.settings_updated' : 'admin.settings_failed';
        $this->session->setFlash($type, $this->localization->t($message));

        header('Location: /admin/settings');
        exit;
    }

    private function getMonthlyStats()
    {
        $stmt = $this->db->prepare("
            SELECT
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count,
                'users' as type
            FROM users
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')

            UNION ALL

            SELECT
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count,
                'orders' as type
            FROM orders
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')

            ORDER BY month DESC
        ");

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function generateOverviewReport($dateFrom, $dateTo)
    {
        $stmt = $this->db->prepare("
            SELECT
                (SELECT COUNT(*) FROM users WHERE created_at BETWEEN ? AND ?) as new_users,
                (SELECT COUNT(*) FROM vendors WHERE created_at BETWEEN ? AND ?) as new_vendors,
                (SELECT COUNT(*) FROM orders WHERE created_at BETWEEN ? AND ?) as total_orders,
                (SELECT SUM(total_amount) FROM orders WHERE created_at BETWEEN ? AND ? AND status = 'completed') as total_revenue,
                (SELECT COUNT(*) FROM quote_requests WHERE created_at BETWEEN ? AND ?) as total_quotes
        ");

        $stmt->execute([$dateFrom, $dateTo, $dateFrom, $dateTo, $dateFrom, $dateTo, $dateFrom, $dateTo, $dateFrom, $dateTo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function generateVendorReport($dateFrom, $dateTo)
    {
        $stmt = $this->db->prepare("
            SELECT
                v.id,
                v.company_name_en,
                v.company_name_ar,
                COUNT(DISTINCT s.id) as services_count,
                COUNT(DISTINCT o.id) as orders_count,
                COALESCE(SUM(o.total_amount), 0) as total_revenue,
                AVG(r.rating) as avg_rating
            FROM vendors v
            LEFT JOIN services s ON v.id = s.vendor_id
            LEFT JOIN orders o ON v.id = o.vendor_id AND o.created_at BETWEEN ? AND ?
            LEFT JOIN reviews r ON v.id = r.vendor_id
            GROUP BY v.id
            ORDER BY total_revenue DESC
            LIMIT 20
        ");

        $stmt->execute([$dateFrom, $dateTo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function generateServiceReport($dateFrom, $dateTo)
    {
        $stmt = $this->db->prepare("
            SELECT
                s.id,
                s.title_en,
                s.title_ar,
                c.name_en as category_name,
                v.company_name_en as vendor_name,
                COUNT(DISTINCT qr.id) as quote_requests,
                COUNT(DISTINCT o.id) as orders_count,
                COALESCE(SUM(o.total_amount), 0) as total_revenue
            FROM services s
            LEFT JOIN categories c ON s.category_id = c.id
            LEFT JOIN vendors v ON s.vendor_id = v.id
            LEFT JOIN quote_requests qr ON s.id = qr.service_id AND qr.created_at BETWEEN ? AND ?
            LEFT JOIN orders o ON s.id = o.service_id AND o.created_at BETWEEN ? AND ?
            GROUP BY s.id
            ORDER BY quote_requests DESC
            LIMIT 20
        ");

        $stmt->execute([$dateFrom, $dateTo, $dateFrom, $dateTo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function generateOrderReport($dateFrom, $dateTo)
    {
        $stmt = $this->db->prepare("
            SELECT
                status,
                COUNT(*) as count,
                SUM(total_amount) as total_amount,
                AVG(total_amount) as avg_amount
            FROM orders
            WHERE created_at BETWEEN ? AND ?
            GROUP BY status
        ");

        $stmt->execute([$dateFrom, $dateTo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function generateQuoteReport($dateFrom, $dateTo)
    {
        $stmt = $this->db->prepare("
            SELECT
                COUNT(qr.id) as total_requests,
                COUNT(q.id) as total_responses,
                COUNT(CASE WHEN q.status = 'accepted' THEN 1 END) as accepted_quotes,
                ROUND((COUNT(CASE WHEN q.status = 'accepted' THEN 1 END) / COUNT(q.id)) * 100, 2) as conversion_rate
            FROM quote_requests qr
            LEFT JOIN quotes q ON qr.id = q.quote_request_id
            WHERE qr.created_at BETWEEN ? AND ?
        ");

        $stmt->execute([$dateFrom, $dateTo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getSystemSettings()
    {
        $stmt = $this->db->prepare("SELECT setting_key, setting_value FROM settings");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        // Default values if not set
        $defaults = [
            'site_name' => 'Print Services Marketplace',
            'site_description' => 'Connect with the best printing services in Egypt',
            'contact_email' => 'info@printservices.com',
            'contact_phone' => '+20 123 456 7890',
            'default_language' => 'en',
            'vendor_free_quotes' => '10',
            'subscription_price' => '99',
            'maintenance_mode' => '0'
        ];

        return array_merge($defaults, $settings);
    }

    private function updateSystemSettings($settings)
    {
        try {
            $this->db->beginTransaction();

            foreach ($settings as $key => $value) {
                $stmt = $this->db->prepare("
                    INSERT INTO settings (setting_key, setting_value)
                    VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE setting_value = ?
                ");
                $stmt->execute([$key, $value, $value]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    private function renderView($view, $data = [])
    {
        // Add common data
        $data['auth'] = $this->auth;
        $data['session'] = $this->session;
        $data['localization'] = $this->localization;
        $data['isRtl'] = $this->localization->isRtl();
        $data['currentLanguage'] = $this->localization->getCurrentLanguage();
        $data['csrfToken'] = $this->session->generateCSRFToken();

        // Load the view
        $viewFile = __DIR__ . "/../views/pages/{$view}.php";
        if (file_exists($viewFile)) {
            extract($data);
            include $viewFile;
        } else {
            throw new Exception("View file not found: {$view}");
        }
    }
}
