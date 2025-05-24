<?php
/**
 * Order Model - Order Management System
 * File path: models/Order.php
 * Session: 7 - Quote Comparison & Order Placement
 */

class Order
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Create new order from accepted quote
     */
    public function create($data)
    {
        $sql = "INSERT INTO orders (
                    quote_id, user_id, vendor_id, service_id,
                    total_amount, delivery_address, contact_name,
                    contact_email, contact_phone, status, payment_status,
                    estimated_delivery_date, notes, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $params = [
            $data['quote_id'],
            $data['user_id'],
            $data['vendor_id'],
            $data['service_id'],
            $data['total_amount'],
            $data['delivery_address'],
            $data['contact_name'],
            $data['contact_email'],
            $data['contact_phone'],
            $data['status'] ?? 'pending',
            $data['payment_status'] ?? 'pending',
            $data['estimated_delivery_date'] ?? null,
            $data['notes'] ?? null
        ];

        return $this->db->insert($sql, $params);
    }

    /**
     * Get order by ID with related information
     */
    public function getById($id, $language = 'en')
    {
        $sql = "SELECT
                    o.*,
                    u.name as user_name,
                    u.email as user_email,
                    v.company_name_en as vendor_name_en,
                    v.company_name_ar as vendor_name_ar,
                    s.title_en as service_title_en,
                    s.title_ar as service_title_ar,
                    q.price as quote_price,
                    q.estimated_delivery_days,
                    q.message as quote_message
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN vendors v ON o.vendor_id = v.id
                LEFT JOIN services s ON o.service_id = s.id
                LEFT JOIN quotes q ON o.quote_id = q.id
                WHERE o.id = ?";

        return $this->db->fetch($sql, [$id]);
    }

    /**
     * Get orders for a specific user
     */
    public function getUserOrders($userId, $status = null, $limit = 20, $offset = 0)
    {
        $sql = "SELECT
                    o.*,
                    v.company_name_en as vendor_name_en,
                    v.company_name_ar as vendor_name_ar,
                    s.title_en as service_title_en,
                    s.title_ar as service_title_ar
                FROM orders o
                LEFT JOIN vendors v ON o.vendor_id = v.id
                LEFT JOIN services s ON o.service_id = s.id
                WHERE o.user_id = ?";

        $params = [$userId];

        if ($status) {
            $sql .= " AND o.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get orders for a specific vendor
     */
    public function getVendorOrders($vendorId, $status = null, $limit = 20, $offset = 0)
    {
        $sql = "SELECT
                    o.*,
                    u.name as user_name,
                    u.email as user_email,
                    s.title_en as service_title_en,
                    s.title_ar as service_title_ar
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN services s ON o.service_id = s.id
                WHERE o.vendor_id = ?";

        $params = [$vendorId];

        if ($status) {
            $sql .= " AND o.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Update order status
     */
    public function updateStatus($orderId, $status)
    {
        $sql = "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$status, $orderId]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($orderId, $paymentStatus)
    {
        $sql = "UPDATE orders SET payment_status = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$paymentStatus, $orderId]);
    }

    /**
     * Update delivery information
     */
    public function updateDelivery($orderId, $deliveryInfo)
    {
        $sql = "UPDATE orders SET
                    tracking_number = ?,
                    delivery_method = ?,
                    estimated_delivery_date = ?,
                    actual_delivery_date = ?,
                    updated_at = NOW()
                WHERE id = ?";

        $params = [
            $deliveryInfo['tracking_number'] ?? null,
            $deliveryInfo['delivery_method'] ?? null,
            $deliveryInfo['estimated_delivery_date'] ?? null,
            $deliveryInfo['actual_delivery_date'] ?? null,
            $orderId
        ];

        return $this->db->execute($sql, $params);
    }

    /**
     * Get order statistics for user
     */
    public function getUserOrderStats($userId)
    {
        $sql = "SELECT
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
                    SUM(total_amount) as total_spent
                FROM orders
                WHERE user_id = ?";

        return $this->db->fetch($sql, [$userId]);
    }

    /**
     * Get order statistics for vendor
     */
    public function getVendorOrderStats($vendorId)
    {
        $sql = "SELECT
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
                    SUM(total_amount) as total_revenue
                FROM orders
                WHERE vendor_id = ?";

        return $this->db->fetch($sql, [$vendorId]);
    }

    /**
     * Get recent orders for user
     */
    public function getUserRecent($userId, $limit = 5)
    {
        $sql = "SELECT
                    o.*,
                    v.company_name_en as vendor_name_en,
                    v.company_name_ar as vendor_name_ar,
                    s.title_en as service_title_en,
                    s.title_ar as service_title_ar
                FROM orders o
                LEFT JOIN vendors v ON o.vendor_id = v.id
                LEFT JOIN services s ON o.service_id = s.id
                WHERE o.user_id = ?
                ORDER BY o.created_at DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$userId, $limit]);
    }

    /**
     * Get recent orders for vendor
     */
    public function getVendorRecent($vendorId, $limit = 5)
    {
        $sql = "SELECT
                    o.*,
                    u.name as user_name,
                    s.title_en as service_title_en,
                    s.title_ar as service_title_ar
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN services s ON o.service_id = s.id
                WHERE o.vendor_id = ?
                ORDER BY o.created_at DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$vendorId, $limit]);
    }

    /**
     * Get user order count
     */
    public function getUserOrderCount($userId)
    {
        $sql = "SELECT COUNT(*) as count FROM orders WHERE user_id = ?";
        $result = $this->db->fetch($sql, [$userId]);
        return $result['count'];
    }

    /**
     * Get user active order count
     */
    public function getUserActiveCount($userId)
    {
        $sql = "SELECT COUNT(*) as count FROM orders
                WHERE user_id = ? AND status IN ('pending', 'processing', 'shipped')";
        $result = $this->db->fetch($sql, [$userId]);
        return $result['count'];
    }

    /**
     * Get monthly order count for user (for charts)
     */
    public function getUserMonthlyCount($userId, $yearMonth)
    {
        $sql = "SELECT COUNT(*) as count FROM orders
                WHERE user_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?";
        $result = $this->db->fetch($sql, [$userId, $yearMonth]);
        return $result['count'];
    }

    /**
     * Get monthly order data for vendor analytics
     */
    public function getVendorMonthlyData($vendorId, $months = 12)
    {
        $sql = "SELECT
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as order_count,
                    SUM(total_amount) as revenue
                FROM orders
                WHERE vendor_id = ?
                AND created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month ASC";

        return $this->db->fetchAll($sql, [$vendorId, $months]);
    }

    /**
     * Cancel order (if allowed)
     */
    public function cancel($orderId, $reason = null)
    {
        // Check if order can be cancelled (only pending orders)
        $order = $this->getById($orderId);
        if (!$order || $order['status'] !== 'pending') {
            return false;
        }

        $sql = "UPDATE orders SET
                    status = 'cancelled',
                    cancellation_reason = ?,
                    cancelled_at = NOW(),
                    updated_at = NOW()
                WHERE id = ?";

        return $this->db->execute($sql, [$reason, $orderId]);
    }

    /**
     * Get all orders for admin (with pagination and filters)
     */
    public function getAllOrders($filters = [], $limit = 20, $offset = 0)
    {
        $sql = "SELECT
                    o.*,
                    u.name as user_name,
                    u.email as user_email,
                    v.company_name_en as vendor_name_en,
                    v.company_name_ar as vendor_name_ar,
                    s.title_en as service_title_en,
                    s.title_ar as service_title_ar
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN vendors v ON o.vendor_id = v.id
                LEFT JOIN services s ON o.service_id = s.id
                WHERE 1=1";

        $params = [];

        // Apply filters
        if (!empty($filters['status'])) {
            $sql .= " AND o.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['payment_status'])) {
            $sql .= " AND o.payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        if (!empty($filters['vendor_id'])) {
            $sql .= " AND o.vendor_id = ?";
            $params[] = $filters['vendor_id'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(o.created_at) >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(o.created_at) <= ?";
            $params[] = $filters['date_to'];
        }

        $sql .= " ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get total order count (for admin)
     */
    public function getTotalCount($filters = [])
    {
        $sql = "SELECT COUNT(*) as count FROM orders o WHERE 1=1";
        $params = [];

        // Apply same filters as getAllOrders
        if (!empty($filters['status'])) {
            $sql .= " AND o.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['payment_status'])) {
            $sql .= " AND o.payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        if (!empty($filters['vendor_id'])) {
            $sql .= " AND o.vendor_id = ?";
            $params[] = $filters['vendor_id'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(o.created_at) >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(o.created_at) <= ?";
            $params[] = $filters['date_to'];
        }

        $result = $this->db->fetch($sql, $params);
        return $result['count'];
    }

    /**
     * Check if user can review order
     */
    public function canReview($orderId, $userId)
    {
        $sql = "SELECT o.id FROM orders o
                LEFT JOIN reviews r ON o.id = r.order_id
                WHERE o.id = ? AND o.user_id = ?
                AND o.status = 'completed' AND r.id IS NULL";

        $result = $this->db->fetch($sql, [$orderId, $userId]);
        return $result !== null;
    }

    /**
     * Get order status name for display
     */
    public function getStatusName($status, $language = 'en')
    {
        $statusNames = [
            'en' => [
                'pending' => 'Pending',
                'confirmed' => 'Confirmed',
                'processing' => 'Processing',
                'shipped' => 'Shipped',
                'delivered' => 'Delivered',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled'
            ],
            'ar' => [
                'pending' => 'معلق',
                'confirmed' => 'مؤكد',
                'processing' => 'قيد التنفيذ',
                'shipped' => 'تم الشحن',
                'delivered' => 'تم التوصيل',
                'completed' => 'مكتمل',
                'cancelled' => 'ملغي'
            ]
        ];

        return $statusNames[$language][$status] ?? $status;
    }
}
?>
