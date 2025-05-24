<?php
/**
 * OrderController - Order Management Controller
 * File path: controllers/OrderController.php
 * Session: 7 - Quote Comparison & Order Placement
 */

class OrderController
{
    private $app;
    private $db;
    private $auth;
    private $session;
    private $localization;
    private $order;
    private $quote;
    private $quoteRequest;
    private $service;
    private $vendor;
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
        $this->order = new Order($this->db);
        $this->quote = new Quote($this->db);
        $this->quoteRequest = new QuoteRequest($this->db);
        $this->service = new Service($this->db);
        $this->vendor = new Vendor($this->db);
        $this->notification = new Notification($this->db);
        $this->validator = new Validator([], $this->localization);
    }

    /**
     * Display order placement page
     */
    public function place($quoteId)
    {
        $language = $this->localization->getCurrentLanguage();

        // Get quote details
        $quote = $this->quote->getById($quoteId, $language);
        if (!$quote) {
            $this->session->setFlash('error', $this->localization->t('quotes.quote_not_found'));
            header('Location: /');
            exit;
        }

        // Verify quote is accepted and belongs to user (if logged in)
        if ($this->auth->isLoggedIn()) {
            $currentUser = $this->auth->getCurrentUser();
            $quoteRequest = $this->quoteRequest->getById($quote['quote_request_id']);

            if ($quoteRequest['user_id'] !== $currentUser['id']) {
                $this->session->setFlash('error', $this->localization->t('general.access_denied'));
                header('Location: /');
                exit;
            }
        }

        // Get service and vendor details
        $service = $this->service->getById($quote['service_id'], $language);
        $vendor = $this->vendor->getById($quote['vendor_id'], $language);

        $this->renderView('orders/place', [
            'quote' => $quote,
            'service' => $service,
            'vendor' => $vendor,
            'pageTitle' => $this->localization->t('orders.place_order')
        ]);
    }

    /**
     * Process order placement
     */
    public function processOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        // Validate CSRF token
        if (!$this->session->validateCSRFToken($_POST['csrf_token'])) {
            $this->session->setFlash('error', $this->localization->t('general.invalid_token'));
            header('Location: /');
            exit;
        }

        $quoteId = $_POST['quote_id'] ?? null;
        if (!$quoteId) {
            $this->session->setFlash('error', $this->localization->t('quotes.quote_not_found'));
            header('Location: /');
            exit;
        }

        // Validate form data
        $validator = new Validator($_POST, $this->localization);
        $rules = [
            'contact_name' => 'required|min:2|max:100',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|min:10|max:20',
            'delivery_address' => 'required|min:10|max:500'
        ];

        if (!$validator->validate($rules)) {
            $this->session->setFlash('error', implode('<br>', $validator->getErrors()));
            header("Location: /orders/place/{$quoteId}");
            exit;
        }

        $language = $this->localization->getCurrentLanguage();

        // Get quote details
        $quote = $this->quote->getById($quoteId, $language);
        if (!$quote) {
            $this->session->setFlash('error', $this->localization->t('quotes.quote_not_found'));
            header('Location: /');
            exit;
        }

        // Get service details
        $service = $this->service->getById($quote['service_id'], $language);

        // Calculate estimated delivery date
        $estimatedDeliveryDate = null;
        if ($quote['estimated_delivery_days']) {
            $estimatedDeliveryDate = date('Y-m-d', strtotime("+{$quote['estimated_delivery_days']} days"));
        }

        // Prepare order data
        $orderData = [
            'quote_id' => $quoteId,
            'user_id' => $this->auth->isLoggedIn() ? $this->auth->getCurrentUser()['id'] : null,
            'vendor_id' => $quote['vendor_id'],
            'service_id' => $quote['service_id'],
            'total_amount' => $quote['price'],
            'delivery_address' => $_POST['delivery_address'],
            'contact_name' => $_POST['contact_name'],
            'contact_email' => $_POST['contact_email'],
            'contact_phone' => $_POST['contact_phone'],
            'status' => 'pending',
            'payment_status' => 'pending',
            'estimated_delivery_date' => $estimatedDeliveryDate,
            'notes' => $_POST['notes'] ?? null
        ];

        // Create order
        $orderId = $this->order->create($orderData);

        if ($orderId) {
            // Update quote status
            $this->quote->updateStatus($quoteId, 'accepted');

            // Create notification for vendor
            $vendor = $this->vendor->getById($quote['vendor_id'], $language);
            $notificationData = [
                'user_id' => $vendor['user_id'],
                'type' => 'new_order',
                'title_en' => 'New Order Received',
                'title_ar' => 'تم استلام طلب جديد',
                'message_en' => "You have received a new order for {$service['title_en']}.",
                'message_ar' => "لقد استلمت طلباً جديداً لـ {$service['title_ar']}.",
                'link' => '/vendor/orders/' . $orderId
            ];
            $this->notification->create($notificationData);

            // Create notification for user (if logged in)
            if ($this->auth->isLoggedIn()) {
                $currentUser = $this->auth->getCurrentUser();
                $userNotificationData = [
                    'user_id' => $currentUser['id'],
                    'type' => 'order_placed',
                    'title_en' => 'Order Placed Successfully',
                    'title_ar' => 'تم تقديم الطلب بنجاح',
                    'message_en' => "Your order for {$service['title_en']} has been placed successfully.",
                    'message_ar' => "تم تقديم طلبك لـ {$service['title_ar']} بنجاح.",
                    'link' => '/orders/' . $orderId
                ];
                $this->notification->create($userNotificationData);
            }

            $this->session->setFlash('success', $this->localization->t('orders.order_placed_successfully'));
            header("Location: /orders/{$orderId}");
        } else {
            $this->session->setFlash('error', $this->localization->t('orders.order_placement_failed'));
            header("Location: /orders/place/{$quoteId}");
        }
        exit;
    }

    /**
     * Display order details
     */
    public function detail($orderId)
    {
        $language = $this->localization->getCurrentLanguage();
        $order = $this->order->getById($orderId, $language);

        if (!$order) {
            $this->session->setFlash('error', $this->localization->t('orders.order_not_found'));
            header('Location: /');
            exit;
        }

        // Check access permissions
        if ($this->auth->isLoggedIn()) {
            $currentUser = $this->auth->getCurrentUser();

            // Allow access if user owns the order, is the vendor, or is admin
            if ($currentUser['role'] !== 'admin' &&
                $order['user_id'] !== $currentUser['id'] &&
                $order['vendor_id'] !== $currentUser['id']) {
                $this->session->setFlash('error', $this->localization->t('general.access_denied'));
                header('Location: /');
                exit;
            }
        }

        // Get quote request files if needed
        $quoteRequest = $this->quoteRequest->getById($order['quote_id']);
        $files = [];
        if ($quoteRequest) {
            $files = $this->quoteRequest->getFiles($quoteRequest['id']);
        }

        $this->renderView('orders/detail', [
            'order' => $order,
            'files' => $files,
            'pageTitle' => $this->localization->t('orders.order_details')
        ]);
    }

    /**
     * Display order tracking page
     */
    public function track($orderId)
    {
        $language = $this->localization->getCurrentLanguage();
        $order = $this->order->getById($orderId, $language);

        if (!$order) {
            $this->session->setFlash('error', $this->localization->t('orders.order_not_found'));
            header('Location: /');
            exit;
        }

        $this->renderView('orders/track', [
            'order' => $order,
            'pageTitle' => $this->localization->t('orders.track_order')
        ]);
    }

    /**
     * Display user order history
     */
    public function history()
    {
        if (!$this->auth->isLoggedIn()) {
            header('Location: /auth/login');
            exit;
        }

        $currentUser = $this->auth->getCurrentUser();
        $language = $this->localization->getCurrentLanguage();

        // Get filter parameters
        $status = $_GET['status'] ?? null;
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Get orders
        $orders = $this->order->getUserOrders($currentUser['id'], $status, $limit, $offset);

        // Get total count for pagination
        $totalOrders = $this->order->getUserOrderCount($currentUser['id']);
        $totalPages = ceil($totalOrders / $limit);

        $this->renderView('orders/history', [
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'selectedStatus' => $status,
            'pageTitle' => $this->localization->t('orders.order_history')
        ]);
    }

    /**
     * Update order status (for vendors)
     */
    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        if (!$this->auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $currentUser = $this->auth->getCurrentUser();
        $orderId = $_POST['order_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$orderId || !$status) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            exit;
        }

        $language = $this->localization->getCurrentLanguage();
        $order = $this->order->getById($orderId, $language);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            exit;
        }

        // Check if current user is the vendor or admin
        if ($currentUser['role'] !== 'admin' && $order['vendor_id'] !== $currentUser['id']) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }

        // Update status
        $success = $this->order->updateStatus($orderId, $status);

        if ($success && $order['user_id']) {
            // Create notification for user
            $statusName = $this->order->getStatusName($status, $language);
            $notificationData = [
                'user_id' => $order['user_id'],
                'type' => 'order_status',
                'title_en' => 'Order Status Updated',
                'title_ar' => 'تم تحديث حالة الطلب',
                'message_en' => "Your order #{$orderId} status has been updated to {$this->order->getStatusName($status, 'en')}.",
                'message_ar' => "تم تحديث حالة طلبك رقم #{$orderId} إلى {$this->order->getStatusName($status, 'ar')}.",
                'link' => '/orders/' . $orderId
            ];
            $this->notification->create($notificationData);
        }

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Status updated successfully' : 'Failed to update status'
        ]);
        exit;
    }

    /**
     * Cancel order
     */
    public function cancel()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        if (!$this->auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $currentUser = $this->auth->getCurrentUser();
        $orderId = $_POST['order_id'] ?? null;
        $reason = $_POST['reason'] ?? null;

        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Missing order ID']);
            exit;
        }

        $language = $this->localization->getCurrentLanguage();
        $order = $this->order->getById($orderId, $language);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            exit;
        }

        // Check if current user owns the order or is admin
        if ($currentUser['role'] !== 'admin' && $order['user_id'] !== $currentUser['id']) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }

        // Cancel order
        $success = $this->order->cancel($orderId, $reason);

        if ($success) {
            // Create notification for vendor
            $vendor = $this->vendor->getById($order['vendor_id'], $language);
            if ($vendor) {
                $notificationData = [
                    'user_id' => $vendor['user_id'],
                    'type' => 'order_cancelled',
                    'title_en' => 'Order Cancelled',
                    'title_ar' => 'تم إلغاء الطلب',
                    'message_en' => "Order #{$orderId} has been cancelled by the customer.",
                    'message_ar' => "تم إلغاء الطلب رقم #{$orderId} من قبل العميل.",
                    'link' => '/vendor/orders/' . $orderId
                ];
                $this->notification->create($notificationData);
            }
        }

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Order cancelled successfully' : 'Failed to cancel order'
        ]);
        exit;
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

        // Check if user is logged in
        if ($this->auth->isLoggedIn()) {
            $data['user'] = $this->auth->getCurrentUser();
            $data['layout'] = 'dashboard';
        } else {
            $data['layout'] = 'main';
        }

        // Extract data for view
        extract($data);

        // Include layout
        if ($data['layout'] === 'dashboard') {
            include 'views/layouts/dashboard.php';
        } else {
            include 'views/layouts/main.php';
        }
    }
}
?>
