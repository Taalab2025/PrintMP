<?php
/**
 * Quote Controller
 * File path: controllers/QuoteController.php
 *
 * Handles quote request, submission, and comparison operations
 */

class QuoteController {
    private $db;
    private $session;
    private $auth;
    private $localization;
    private $validator;
    private $fileUpload;
    private $quoteRequest;
    private $quote;
    private $service;
    private $vendor;
    private $notification;

    public function __construct($app) {
        $this->db = $app->getDatabase();
        $this->session = $app->getSession();
        $this->auth = $app->getAuth();
        $this->localization = $app->getLocalization();
        $this->validator = $app->getValidator();
        $this->fileUpload = new FileUpload('uploads/quotes');

        // Load models
        $this->quoteRequest = new QuoteRequest($this->db);
        $this->quote = new Quote($this->db);
        $this->service = new Service($this->db);
        $this->vendor = new Vendor($this->db);
        $this->notification = new Notification($this->db);
    }

    /**
     * Show the quote request form for a service
     */
    public function requestForm() {
        // Get service ID from URL
        $serviceId = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;

        if (!$serviceId) {
            // Redirect to services if no service ID provided
            header('Location: /services');
            exit;
        }

        // Get service details with options
        $language = $this->localization->getCurrentLanguage();
        $service = $this->service->getById($serviceId, $language);

        if (!$service) {
            // Service not found
            header('Location: /services');
            exit;
        }

        // Get vendor details
        $vendor = $this->vendor->getById($service['vendor_id'], $language);

        // Get user details if logged in
        $user = null;
        if ($this->auth->isLoggedIn()) {
            $user = $this->auth->getCurrentUser();
        }

        // Load the request form view
        include 'views/pages/quotes/request.php';
    }

    /**
     * Submit a quote request
     */
    public function submitRequest() {
        // Verify CSRF token
        if (!$this->session->verifyCsrfToken($_POST['csrf_token'])) {
            $this->session->setFlash('error', $this->localization->t('general.invalid_request'));
            header('Location: /services');
            exit;
        }

        // Get service ID
        $serviceId = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;

        if (!$serviceId) {
            $this->session->setFlash('error', $this->localization->t('quotes.invalid_service'));
            header('Location: /services');
            exit;
        }

        // Get service and vendor details
        $language = $this->localization->getCurrentLanguage();
        $service = $this->service->getById($serviceId, $language);

        if (!$service) {
            $this->session->setFlash('error', $this->localization->t('quotes.invalid_service'));
            header('Location: /services');
            exit;
        }

        // Prepare validation rules
        $rules = [
            'contact_name' => 'required',
            'contact_email' => 'required|email',
            'contact_phone' => 'required'
        ];

        // Get service options and add to validation rules
        $options = $this->service->getOptions($serviceId);
        foreach ($options as $option) {
            if ($option['is_required']) {
                $rules["option_{$option['id']}"] = 'required';
            }
        }

        // Validate form data
        if (!$this->validator->validate($_POST, $rules)) {
            $errors = $this->validator->getErrors();
            $this->session->setFlash('errors', $errors);
            $this->session->setFlash('old_input', $_POST);
            header("Location: /quotes/request?service_id={$serviceId}");
            exit;
        }

        // Prepare quote request data
        $quoteRequestData = [
            'service_id' => $serviceId,
            'vendor_id' => $service['vendor_id'],
            'contact_name' => $_POST['contact_name'],
            'contact_email' => $_POST['contact_email'],
            'contact_phone' => $_POST['contact_phone'],
            'message' => $_POST['message'] ?? null,
            'delivery_address' => $_POST['delivery_address'] ?? null,
            'options' => []
        ];

        // Add user ID if logged in
        if ($this->auth->isLoggedIn()) {
            $user = $this->auth->getCurrentUser();
            $quoteRequestData['user_id'] = $user['id'];
        }

        // Process options
        foreach ($options as $option) {
            $optionField = "option_{$option['id']}";
            if (isset($_POST[$optionField])) {
                $quoteRequestData['options'][] = [
                    'name' => $option['name'],
                    'value' => $_POST[$optionField]
                ];
            }
        }

        // Create quote request
        $quoteRequestId = $this->quoteRequest->create($quoteRequestData);

        if (!$quoteRequestId) {
            $this->session->setFlash('error', $this->localization->t('quotes.request_failed'));
            header("Location: /quotes/request?service_id={$serviceId}");
            exit;
        }

        // Process file uploads if any
        if (!empty($_FILES['design_files']['name'][0])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/zip', 'application/x-rar-compressed'];
            $maxSize = 10 * 1024 * 1024; // 10MB

            $uploadResults = $this->fileUpload->uploadMultiple('design_files', $allowedTypes, $maxSize);

            foreach ($uploadResults as $result) {
                if ($result['success']) {
                    // Add file to quote request
                    $this->quoteRequest->addFile(
                        $quoteRequestId,
                        $result['path'],
                        $result['name'],
                        $result['type']
                    );
                } else {
                    // Log error but continue
                    error_log("File upload error: {$result['error']}");
                }
            }
        }

        // Create notification for vendor
        $serviceName = $service["title_{$language}"];
        $this->notification->createQuoteRequestNotification(
            $service['vendor_id'],
            $quoteRequestId,
            $serviceName
        );

        // Set success message
        $this->session->setFlash('success', $this->localization->t('quotes.request_success'));

        // Redirect to tracking page or confirmation page
        if ($this->auth->isLoggedIn()) {
            header("Location: /quotes/track/{$quoteRequestId}");
        } else {
            header("Location: /quotes/confirmation/{$quoteRequestId}");
        }
        exit;
    }

    /**
     * Show quote request confirmation page for non-logged in users
     */
    public function confirmation($id) {
        $quoteRequestId = (int)$id;

        // Get quote request details
        $quoteRequest = $this->quoteRequest->getById($quoteRequestId);

        if (!$quoteRequest) {
            $this->session->setFlash('error', $this->localization->t('quotes.request_not_found'));
            header('Location: /services');
            exit;
        }

        // Get service details
        $language = $this->localization->getCurrentLanguage();
        $service = $this->service->getById($quoteRequest['service_id'], $language);

        // Load the confirmation view
        include 'views/pages/quotes/confirmation.php';
    }

    /**
     * Track a quote request
     */
    public function track($id) {
        $quoteRequestId = (int)$id;

        // Get quote request details
        $quoteRequest = $this->quoteRequest->getById($quoteRequestId);

        if (!$quoteRequest) {
            $this->session->setFlash('error', $this->localization->t('quotes.request_not_found'));
            header('Location: /quotes/history');
            exit;
        }

        // Check if the user owns this request if they're logged in
        if ($this->auth->isLoggedIn()) {
            $user = $this->auth->getCurrentUser();
            if ($quoteRequest['user_id'] && $quoteRequest['user_id'] != $user['id']) {
                $this->session->setFlash('error', $this->localization->t('quotes.not_authorized'));
                header('Location: /quotes/history');
                exit;
            }
        } else {
            // For guest users, they can only track via email validation or direct link
            // This could be enhanced with email verification
            if (!isset($_GET['email']) || $_GET['email'] != $quoteRequest['contact_email']) {
                $this->session->setFlash('error', $this->localization->t('quotes.not_authorized'));
                header('Location: /services');
                exit;
            }
        }

        // Get service details
        $language = $this->localization->getCurrentLanguage();
        $service = $this->service->getById($quoteRequest['service_id'], $language);

        // Get vendor details
        $vendor = $this->vendor->getById($quoteRequest['vendor_id'], $language);

        // Get quotes for this request
        $quotes = $this->quote->getByRequestId($quoteRequestId);

        // Load the tracking view
        include 'views/pages/quotes/track.php';
    }

    /**
     * Show all quote requests for the current user
     */
    public function history() {
        // Ensure user is logged in
        if (!$this->auth->isLoggedIn()) {
            $this->session->setFlash('error', $this->localization->t('auth.login_required'));
            header('Location: /login?redirect=/quotes/history');
            exit;
        }

        $user = $this->auth->getCurrentUser();
        $language = $this->localization->getCurrentLanguage();

        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Get quote requests
        $quoteRequests = $this->quoteRequest->getByUserId($user['id'], $limit, $offset);
        $totalRequests = $this->quoteRequest->countByUser($user['id']);

        $totalPages = ceil($totalRequests / $limit);

        // Load the history view
        include 'views/pages/quotes/history.php';
    }

    /**
     * Compare quotes for a request
     */
    public function compare($id) {
        $quoteRequestId = (int)$id;

        // Get quote request details
        $quoteRequest = $this->quoteRequest->getById($quoteRequestId);

        if (!$quoteRequest) {
            $this->session->setFlash('error', $this->localization->t('quotes.request_not_found'));
            header('Location: /quotes/history');
            exit;
        }

        // Check if the user owns this request if they're logged in
        if ($this->auth->isLoggedIn()) {
            $user = $this->auth->getCurrentUser();
            if ($quoteRequest['user_id'] && $quoteRequest['user_id'] != $user['id']) {
                $this->session->setFlash('error', $this->localization->t('quotes.not_authorized'));
                header('Location: /quotes/history');
                exit;
            }
        } else {
            // For guest users, they can only compare via email validation or direct link
            if (!isset($_GET['email']) || $_GET['email'] != $quoteRequest['contact_email']) {
                $this->session->setFlash('error', $this->localization->t('quotes.not_authorized'));
                header('Location: /services');
                exit;
            }
        }

        // Get service details
        $language = $this->localization->getCurrentLanguage();
        $service = $this->service->getById($quoteRequest['service_id'], $language);

        // Get quotes for this request
        $quotes = $this->quote->getByRequestId($quoteRequestId);

        // Load the compare view
        include 'views/pages/quotes/compare.php';
    }

    /**
     * Accept a quote
     */
    public function acceptQuote() {
        // Verify CSRF token
        if (!$this->session->verifyCsrfToken($_POST['csrf_token'])) {
            $this->session->setFlash('error', $this->localization->t('general.invalid_request'));
            header('Location: /quotes/history');
            exit;
        }

        $quoteId = isset($_POST['quote_id']) ? (int)$_POST['quote_id'] : 0;

        if (!$quoteId) {
            $this->session->setFlash('error', $this->localization->t('quotes.invalid_quote'));
            header('Location: /quotes/history');
            exit;
        }

        // Get quote details
        $quote = $this->quote->getById($quoteId);

        if (!$quote) {
            $this->session->setFlash('error', $this->localization->t('quotes.quote_not_found'));
            header('Location: /quotes/history');
            exit;
        }

        // Get quote request details
        $quoteRequest = $this->quoteRequest->getById($quote['quote_request_id']);

        // Check if the user owns this request if they're logged in
        if ($this->auth->isLoggedIn()) {
            $user = $this->auth->getCurrentUser();
            if ($quoteRequest['user_id'] && $quoteRequest['user_id'] != $user['id']) {
                $this->session->setFlash('error', $this->localization->t('quotes.not_authorized'));
                header('Location: /quotes/history');
                exit;
            }
        } else {
            // For guest users, they can only accept via email validation
            if (!isset($_POST['email']) || $_POST['email'] != $quoteRequest['contact_email']) {
                $this->session->setFlash('error', $this->localization->t('quotes.not_authorized'));
                header('Location: /services');
                exit;
            }
        }

        // Check if quote is not expired and is still offerable
        if ($quote['status'] !== 'offered' || $this->quote->isExpired($quote)) {
            $this->session->setFlash('error', $this->localization->t('quotes.quote_expired'));
            header("Location: /quotes/compare/{$quote['quote_request_id']}");
            exit;
        }

        // Accept the quote
        $success = $this->quote->accept($quoteId);

        if (!$success) {
            $this->session->setFlash('error', $this->localization->t('quotes.accept_failed'));
            header("Location: /quotes/compare/{$quote['quote_request_id']}");
            exit;
        }

        // Get service details for notification
        $language = $this->localization->getCurrentLanguage();
        $service = $this->service->getById($quoteRequest['service_id'], $language);

        // Create notification for vendor
        $serviceName = $service["title_{$language}"];
        $this->notification->createQuoteAcceptedNotification(
            $quote['vendor_id'],
            $quoteId,
            $serviceName
        );

        // Set success message
        $this->session->setFlash('success', $this->localization->t('quotes.accept_success'));

        // Redirect to place order page or confirmation
        if ($this->auth->isLoggedIn()) {
            header("Location: /orders/place?quote_id={$quoteId}");
        } else {
            header("Location: /orders/guest-place?quote_id={$quoteId}&email={$quoteRequest['contact_email']}");
        }
        exit;
    }

    /**
     * Vendor response to quote request
     */
    public function vendorResponse() {
        // Ensure vendor is logged in
        if (!$this->auth->isLoggedIn() || $this->auth->getUserRole() !== 'vendor') {
            $this->session->setFlash('error', $this->localization->t('auth.vendor_login_required'));
            header('Location: /login?redirect=/vendor/dashboard');
            exit;
        }

        // Verify CSRF token
        if (!$this->session->verifyCsrfToken($_POST['csrf_token'])) {
            $this->session->setFlash('error', $this->localization->t('general.invalid_request'));
            header('Location: /vendor/quote-requests');
            exit;
        }

        $quoteRequestId = isset($_POST['quote_request_id']) ? (int)$_POST['quote_request_id'] : 0;

        if (!$quoteRequestId) {
            $this->session->setFlash('error', $this->localization->t('quotes.invalid_request'));
            header('Location: /vendor/quote-requests');
            exit;
        }

        // Get quote request details
        $quoteRequest = $this->quoteRequest->getById($quoteRequestId);

        if (!$quoteRequest) {
            $this->session->setFlash('error', $this->localization->t('quotes.request_not_found'));
            header('Location: /vendor/quote-requests');
            exit;
        }

        // Get vendor details
        $user = $this->auth->getCurrentUser();
        $vendor = $this->vendor->getByUserId($user['id']);

        // Check if this vendor is eligible to respond to this request
        if ($quoteRequest['vendor_id'] != $vendor['id'] && $quoteRequest['service_id']) {
            $service = $this->service->getById($quoteRequest['service_id']);
            if ($service['vendor_id'] != $vendor['id']) {
                $this->session->setFlash('error', $this->localization->t('quotes.not_authorized'));
                header('Location: /vendor/quote-requests');
                exit;
            }
        }

        // Check if vendor has already responded
        if ($this->quote->hasQuoted($quoteRequestId, $vendor['id'])) {
            // Get existing quote and update it
            $existingQuote = $this->quote->getVendorQuote($quoteRequestId, $vendor['id']);

            if ($existingQuote['status'] !== 'offered') {
                $this->session->setFlash('error', $this->localization->t('quotes.cannot_update'));
                header('Location: /vendor/quote-requests');
                exit;
            }

            // Validate form data
            $rules = [
                'price' => 'required|numeric',
                'estimated_delivery_days' => 'required|numeric'
            ];

            if (!$this->validator->validate($_POST, $rules)) {
                $errors = $this->validator->getErrors();
                $this->session->setFlash('errors', $errors);
                $this->session->setFlash('old_input', $_POST);
                header("Location: /vendor/quote-requests/{$quoteRequestId}");
                exit;
            }

            // Update quote
            $quoteData = [
                'price' => $_POST['price'],
                'estimated_delivery_days' => $_POST['estimated_delivery_days'],
                'message' => $_POST['message'] ?? null,
                'valid_until' => date('Y-m-d H:i:s', strtotime('+7 days'))
            ];

            $success = $this->quote->update($existingQuote['id'], $quoteData);

            if (!$success) {
                $this->session->setFlash('error', $this->localization->t('quotes.update_failed'));
                header("Location: /vendor/quote-requests/{$quoteRequestId}");
                exit;
            }

            $this->session->setFlash('success', $this->localization->t('quotes.update_success'));
            header('Location: /vendor/quote-requests');
            exit;
        }

        // Check if vendor has reached their subscription limit
        $isFreemiumLimitReached = $this->vendor->isFreemiumLimitReached($vendor['id']);
        if ($isFreemiumLimitReached && !$this->vendor->hasActiveSubscription($vendor['id'])) {
            $this->session->setFlash('error', $this->localization->t('quotes.subscription_limit_reached'));
            header('Location: /vendor/subscription');
            exit;
        }

        // Validate form data
        $rules = [
            'price' => 'required|numeric',
            'estimated_delivery_days' => 'required|numeric'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            $errors = $this->validator->getErrors();
            $this->session->setFlash('errors', $errors);
            $this->session->setFlash('old_input', $_POST);
            header("Location: /vendor/quote-requests/{$quoteRequestId}");
            exit;
        }

        // Create quote
        $quoteData = [
            'quote_request_id' => $quoteRequestId,
            'vendor_id' => $vendor['id'],
            'price' => $_POST['price'],
            'estimated_delivery_days' => $_POST['estimated_delivery_days'],
            'message' => $_POST['message'] ?? null,
            'valid_until' => date('Y-m-d H:i:s', strtotime('+7 days'))
        ];

        $quoteId = $this->quote->create($quoteData);

        if (!$quoteId) {
            $this->session->setFlash('error', $this->localization->t('quotes.response_failed'));
            header("Location: /vendor/quote-requests/{$quoteRequestId}");
            exit;
        }

        // Create notification for the user
        $language = $this->localization->getCurrentLanguage();
        $vendorName = $vendor["company_name_{$language}"];

        if ($quoteRequest['user_id']) {
            $this->notification->createQuoteResponseNotification(
                $quoteRequest['user_id'],
                $quoteRequestId,
                $vendorName
            );
        }

        // Update vendor request count
        $this->vendor->incrementQuoteRequestCount($vendor['id']);

        // Check if vendor is approaching free limit
        $usedCount = $this->vendor->getUsedQuoteRequestCount($vendor['id']);
        $freeLimit = $this->vendor->getFreemiumRequestLimit();

        if ($usedCount >= ($freeLimit - 2) && !$this->vendor->hasActiveSubscription($vendor['id'])) {
            // Create warning notification for the vendor
            $this->notification->createSubscriptionLimitNotification(
                $vendor['id'],
                $usedCount,
                $freeLimit
            );
        }

        $this->session->setFlash('success', $this->localization->t('quotes.response_success'));
        header('Location: /vendor/quote-requests');
        exit;
    }

    /**
     * Show vendor quote requests
     */
    public function vendorQuoteRequests() {
        // Ensure vendor is logged in
        if (!$this->auth->isLoggedIn() || $this->auth->getUserRole() !== 'vendor') {
            $this->session->setFlash('error', $this->localization->t('auth.vendor_login_required'));
            header('Location: /login?redirect=/vendor/dashboard');
            exit;
        }

        $user = $this->auth->getCurrentUser();
        $vendor = $this->vendor->getByUserId($user['id']);
        $language = $this->localization->getCurrentLanguage();

        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Status filter
        $status = isset($_GET['status']) ? $_GET['status'] : null;

        // Get quote requests
        $quoteRequests = $this->quoteRequest->getByVendorId($vendor['id'], $status, $limit, $offset);
        $totalRequests = $this->quoteRequest->countByVendor($vendor['id'], $status);

        $totalPages = ceil($totalRequests / $limit);

        // Check subscription status
        $isFreemiumLimitReached = $this->vendor->isFreemiumLimitReached($vendor['id']);
        $hasActiveSubscription = $this->vendor->hasActiveSubscription($vendor['id']);
        $usedQuoteCount = $this->vendor->getUsedQuoteRequestCount($vendor['id']);
        $freeLimit = $this->vendor->getFreemiumRequestLimit();

        // Load the vendor quote requests view
        include 'views/pages/vendor-dashboard/quote-requests.php';
    }

    /**
     * Show vendor quote request details
     */
    public function vendorQuoteRequestDetail($id) {
        // Ensure vendor is logged in
        if (!$this->auth->isLoggedIn() || $this->auth->getUserRole() !== 'vendor') {
            $this->session->setFlash('error', $this->localization->t('auth.vendor_login_required'));
            header('Location: /login?redirect=/vendor/dashboard');
            exit;
        }

        $quoteRequestId = (int)$id;
        $user = $this->auth->getCurrentUser();
        $vendor = $this->vendor->getByUserId($user['id']);
        $language = $this->localization->getCurrentLanguage();

        // Get quote request details
        $quoteRequest = $this->quoteRequest->getById($quoteRequestId);

        if (!$quoteRequest) {
            $this->session->setFlash('error', $this->localization->t('quotes.request_not_found'));
            header('Location: /vendor/quote-requests');
            exit;
        }

        // Check if this vendor is eligible to view this request
        if ($quoteRequest['vendor_id'] != $vendor['id'] && $quoteRequest['service_id']) {
            $service = $this->service->getById($quoteRequest['service_id']);
            if ($service['vendor_id'] != $vendor['id']) {
                $this->session->setFlash('error', $this->localization->t('quotes.not_authorized'));
                header('Location: /vendor/quote-requests');
                exit;
            }
        }

        // Get service details
        $service = $this->service->getById($quoteRequest['service_id'], $language);

        // Check if vendor has already responded
        $hasQuoted = $this->quote->hasQuoted($quoteRequestId, $vendor['id']);
        $vendorQuote = null;

        if ($hasQuoted) {
            $vendorQuote = $this->quote->getVendorQuote($quoteRequestId, $vendor['id']);
        }

        // Check subscription status
        $isFreemiumLimitReached = $this->vendor->isFreemiumLimitReached($vendor['id']);
        $hasActiveSubscription = $this->vendor->hasActiveSubscription($vendor['id']);

        // Load the vendor quote request detail view
        include 'views/pages/vendor-dashboard/quote-request-detail.php';
    }

    /**
     * Show vendor quotes
     */
    public function vendorQuotes() {
        // Ensure vendor is logged in
        if (!$this->auth->isLoggedIn() || $this->auth->getUserRole() !== 'vendor') {
            $this->session->setFlash('error', $this->localization->t('auth.vendor_login_required'));
            header('Location: /login?redirect=/vendor/dashboard');
            exit;
        }

        $user = $this->auth->getCurrentUser();
        $vendor = $this->vendor->getByUserId($user['id']);
        $language = $this->localization->getCurrentLanguage();

        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Status filter
        $status = isset($_GET['status']) ? $_GET['status'] : null;

        // Get quotes
        $quotes = $this->quote->getByVendorId($vendor['id'], $status, $limit, $offset);
        $totalQuotes = $this->quote->countByVendor($vendor['id'], $status);

        $totalPages = ceil($totalQuotes / $limit);

        // Load the vendor quotes view
        include 'views/pages/vendor-dashboard/quotes.php';
    }

    /**
     * Show vendor quote details
     */
    public function vendorQuoteDetail($id) {
        // Ensure vendor is logged in
        if (!$this->auth->isLoggedIn() || $this->auth->getUserRole() !== 'vendor') {
            $this->session->setFlash('error', $this->localization->t('auth.vendor_login_required'));
            header('Location: /login?redirect=/vendor/dashboard');
            exit;
        }

        $quoteId = (int)$id;
        $user = $this->auth->getCurrentUser();
        $vendor = $this->vendor->getByUserId($user['id']);
        $language = $this->localization->getCurrentLanguage();

        // Get quote details
        $quote = $this->quote->getById($quoteId);

        if (!$quote) {
            $this->session->setFlash('error', $this->localization->t('quotes.quote_not_found'));
            header('Location: /vendor/quotes');
            exit;
        }

        // Check if this vendor owns this quote
        if ($quote['vendor_id'] != $vendor['id']) {
            $this->session->setFlash('error', $this->localization->t('quotes.not_authorized'));
            header('Location: /vendor/quotes');
            exit;
        }

        // Get quote request details
        $quoteRequest = $this->quoteRequest->getById($quote['quote_request_id']);

        // Get service details
        $service = $this->service->getById($quoteRequest['service_id'], $language);

        // Load the vendor quote detail view
        include 'views/pages/vendor-dashboard/quote-detail.php';
    }
}
