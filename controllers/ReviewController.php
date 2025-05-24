<?php
/**
 * Review Controller Methods
 * File path: controllers/ReviewController.php
 * Handles review submission and management
 */

// Add these methods to the existing OrderController or create a new ReviewController

/**
 * Show review submission form
 */
public function showReviewForm($orderId)
{
    // Check if user is logged in
    if (!$this->auth->isLoggedIn()) {
        header('Location: /auth/login');
        exit;
    }

    $userId = $this->auth->getCurrentUser()['id'];

    // Get order details
    $order = $this->order->getById($orderId);

    if (!$order || $order['user_id'] != $userId) {
        $this->session->setFlash('error', $this->localization->t('orders.order_not_found'));
        header('Location: /orders/history');
        exit;
    }

    // Check if order is completed
    if ($order['status'] !== 'delivered') {
        $this->session->setFlash('error', $this->localization->t('reviews.order_not_completed'));
        header('Location: /orders/' . $orderId);
        exit;
    }

    // Check if review already exists
    if (!$this->review->canReviewOrder($orderId, $userId)) {
        $this->session->setFlash('error', $this->localization->t('reviews.already_reviewed'));
        header('Location: /orders/' . $orderId);
        exit;
    }

    // Get vendor and service information
    $vendor = $this->vendor->getById($order['vendor_id']);
    $service = $this->service->getById($order['service_id']);

    $this->render('reviews/form', [
        'order' => $order,
        'vendor' => $vendor,
        'service' => $service,
        'pageTitle' => $this->localization->t('reviews.write_review')
    ]);
}

/**
 * Handle review submission
 */
public function submitReview($orderId)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /orders/' . $orderId . '/review');
        exit;
    }

    // Check if user is logged in
    if (!$this->auth->isLoggedIn()) {
        header('Location: /auth/login');
        exit;
    }

    $userId = $this->auth->getCurrentUser()['id'];

    if (!$this->session->validateCSRFToken($_POST['csrf_token'])) {
        $this->session->setFlash('error', $this->localization->t('general.invalid_token'));
        header('Location: /orders/' . $orderId . '/review');
        exit;
    }

    // Validate form data
    $validator = new Validator($_POST, $this->localization);

    $rules = [
        'rating' => 'required|numeric|between:1,5',
        'comment' => 'max:1000'
    ];

    if (!$validator->validate($rules)) {
        $this->session->setFlash('error', $this->localization->t('general.validation_failed'));
        $this->session->setFlash('validation_errors', $validator->getErrors());
        header('Location: /orders/' . $orderId . '/review');
        exit;
    }

    // Get order details
    $order = $this->order->getById($orderId);

    if (!$order || $order['user_id'] != $userId) {
        $this->session->setFlash('error', $this->localization->t('orders.order_not_found'));
        header('Location: /orders/history');
        exit;
    }

    // Check if can review
    if (!$this->review->canReviewOrder($orderId, $userId)) {
        $this->session->setFlash('error', $this->localization->t('reviews.cannot_review'));
        header('Location: /orders/' . $orderId);
        exit;
    }

    // Create review
    $reviewData = [
        'order_id' => $orderId,
        'user_id' => $userId,
        'vendor_id' => $order['vendor_id'],
        'rating' => (int)$_POST['rating'],
        'comment' => trim($_POST['comment']) ?: null
    ];

    $success = $this->review->create($reviewData);

    if ($success) {
        // Create notification for vendor
        $language = $this->localization->getCurrentLanguage();
        $vendor = $this->vendor->getById($order['vendor_id']);
        $user = $this->auth->getCurrentUser();

        $notificationData = [
            'user_id' => $vendor['user_id'],
            'type' => 'new_review',
            'title_en' => 'New Review Received',
            'title_ar' => 'تم استلام تقييم جديد',
            'message_en' => $user['name'] . ' has left a review for your service.',
            'message_ar' => 'قام ' . $user['name'] . ' بترك تقييم لخدمتك.',
            'link' => '/vendor/reviews'
        ];

        $this->notification->create($notificationData);

        $this->session->setFlash('success', $this->localization->t('reviews.review_submitted'));
        header('Location: /orders/' . $orderId);
    } else {
        $this->session->setFlash('error', $this->localization->t('reviews.submission_failed'));
        header('Location: /orders/' . $orderId . '/review');
    }

    exit;
}

/**
 * Show user's reviews
 */
public function userReviews()
{
    if (!$this->auth->isLoggedIn()) {
        header('Location: /auth/login');
        exit;
    }

    $userId = $this->auth->getCurrentUser()['id'];
    $page = (int)($_GET['page'] ?? 1);
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $reviews = $this->review->getByUserId($userId, $limit, $offset);
    $totalReviews = count($this->review->getByUserId($userId, 1000, 0)); // Get total count
    $totalPages = ceil($totalReviews / $limit);

    $this->render('user/reviews', [
        'reviews' => $reviews,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'pageTitle' => $this->localization->t('reviews.my_reviews')
    ]);
}
?>
