<?php
/**
 * Notification Model
 * File path: models/Notification.php
 * 
 * Handles user notifications with multilingual support
 */

class Notification {
    private $db;
    private $table = 'notifications';

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Create a new notification
     * 
     * @param array $data Notification data
     * @return int|false New notification ID or false on failure
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (user_id, vendor_id, type, title_en, title_ar, message_en, message_ar, 
                   link, is_read, created_at) 
                  VALUES (:user_id, :vendor_id, :type, :title_en, :title_ar, :message_en, 
                  :message_ar, :link, :is_read, NOW())";
        
        $params = [
            'user_id' => $data['user_id'] ?? null,
            'vendor_id' => $data['vendor_id'] ?? null,
            'type' => $data['type'],
            'title_en' => $data['title_en'],
            'title_ar' => $data['title_ar'],
            'message_en' => $data['message_en'],
            'message_ar' => $data['message_ar'],
            'link' => $data['link'] ?? null,
            'is_read' => $data['is_read'] ?? 0
        ];
        
        return $this->db->insert($query, $params);
    }
    
    /**
     * Get notifications for a user
     * 
     * @param int $userId User ID
     * @param bool $unreadOnly Get only unread notifications
     * @param int $limit Number of records to return
     * @param int $offset Offset for pagination
     * @return array Notifications
     */
    public function getForUser($userId, $unreadOnly = false, $limit = 10, $offset = 0) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE user_id = :user_id";
        
        $params = ['user_id' => $userId];
        
        if ($unreadOnly) {
            $query .= " AND is_read = 0";
        }
        
        $query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        return $this->db->select($query, $params);
    }
    
    /**
     * Get notifications for a vendor
     * 
     * @param int $vendorId Vendor ID
     * @param bool $unreadOnly Get only unread notifications
     * @param int $limit Number of records to return
     * @param int $offset Offset for pagination
     * @return array Notifications
     */
    public function getForVendor($vendorId, $unreadOnly = false, $limit = 10, $offset = 0) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE vendor_id = :vendor_id";
        
        $params = ['vendor_id' => $vendorId];
        
        if ($unreadOnly) {
            $query .= " AND is_read = 0";
        }
        
        $query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        return $this->db->select($query, $params);
    }
    
    /**
     * Mark notification as read
     * 
     * @param int $id Notification ID
     * @return bool Success status
     */
    public function markAsRead($id) {
        $query = "UPDATE {$this->table} SET is_read = 1 WHERE id = :id";
        $params = ['id' => $id];
        
        return $this->db->execute($query, $params);
    }
    
    /**
     * Mark all notifications as read for a user
     * 
     * @param int $userId User ID
     * @return bool Success status
     */
    public function markAllReadForUser($userId) {
        $query = "UPDATE {$this->table} SET is_read = 1 WHERE user_id = :user_id";
        $params = ['user_id' => $userId];
        
        return $this->db->execute($query, $params);
    }
    
    /**
     * Mark all notifications as read for a vendor
     * 
     * @param int $vendorId Vendor ID
     * @return bool Success status
     */
    public function markAllReadForVendor($vendorId) {
        $query = "UPDATE {$this->table} SET is_read = 1 WHERE vendor_id = :vendor_id";
        $params = ['vendor_id' => $vendorId];
        
        return $this->db->execute($query, $params);
    }
    
    /**
     * Delete a notification
     * 
     * @param int $id Notification ID
     * @return bool Success status
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $params = ['id' => $id];
        
        return $this->db->execute($query, $params);
    }
    
    /**
     * Count unread notifications for a user
     * 
     * @param int $userId User ID
     * @return int Count
     */
    public function countUnreadForUser($userId) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} 
                  WHERE user_id = :user_id AND is_read = 0";
        $params = ['user_id' => $userId];
        
        $result = $this->db->selectOne($query, $params);
        return $result ? $result['count'] : 0;
    }
    
    /**
     * Count unread notifications for a vendor
     * 
     * @param int $vendorId Vendor ID
     * @return int Count
     */
    public function countUnreadForVendor($vendorId) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} 
                  WHERE vendor_id = :vendor_id AND is_read = 0";
        $params = ['vendor_id' => $vendorId];
        
        $result = $this->db->selectOne($query, $params);
        return $result ? $result['count'] : 0;
    }
    
    /**
     * Create quote request notification for vendor
     * 
     * @param int $vendorId Vendor ID
     * @param int $quoteRequestId Quote request ID
     * @param string $serviceName Service name
     * @return int|false New notification ID or false on failure
     */
    public function createQuoteRequestNotification($vendorId, $quoteRequestId, $serviceName) {
        $data = [
            'vendor_id' => $vendorId,
            'type' => 'quote_request',
            'title_en' => 'New Quote Request',
            'title_ar' => 'طلب تسعير جديد',
            'message_en' => "You have received a new quote request for {$serviceName}.",
            'message_ar' => "لقد تلقيت طلب تسعير جديد لخدمة {$serviceName}.",
            'link' => "/vendor/quote-requests/{$quoteRequestId}"
        ];
        
        return $this->create($data);
    }
    
    /**
     * Create quote response notification for user
     * 
     * @param int $userId User ID
     * @param int $quoteRequestId Quote request ID
     * @param string $vendorName Vendor name
     * @return int|false New notification ID or false on failure
     */
    public function createQuoteResponseNotification($userId, $quoteRequestId, $vendorName) {
        $data = [
            'user_id' => $userId,
            'type' => 'quote_response',
            'title_en' => 'New Quote Received',
            'title_ar' => 'تم استلام عرض سعر جديد',
            'message_en' => "{$vendorName} has sent you a quote for your request.",
            'message_ar' => "قام {$vendorName} بإرسال عرض سعر لطلبك.",
            'link' => "/quotes/compare/{$quoteRequestId}"
        ];
        
        return $this->create($data);
    }
    
    /**
     * Create quote accepted notification for vendor
     * 
     * @param int $vendorId Vendor ID
     * @param int $quoteId Quote ID
     * @param string $serviceName Service name
     * @return int|false New notification ID or false on failure
     */
    public function createQuoteAcceptedNotification($vendorId, $quoteId, $serviceName) {
        $data = [
            'vendor_id' => $vendorId,
            'type' => 'quote_accepted',
            'title_en' => 'Quote Accepted',
            'title_ar' => 'تم قبول عرض السعر',
            'message_en' => "Your quote for {$serviceName} has been accepted!",
            'message_ar' => "تم قبول عرض السعر الخاص بك لخدمة {$serviceName}!",
            'link' => "/vendor/quotes/{$quoteId}"
        ];
        
        return $this->create($data);
    }
    
    /**
     * Create subscription limit notification for vendor
     * 
     * @param int $vendorId Vendor ID
     * @param int $used Number of requests used
     * @param int $limit Request limit
     * @return int|false New notification ID or false on failure
     */
    public function createSubscriptionLimitNotification($vendorId, $used, $limit) {
        $data = [
            'vendor_id' => $vendorId,
            'type' => 'subscription_limit',
            'title_en' => 'Subscription Limit Reached',
            'title_ar' => 'تم الوصول إلى حد الاشتراك',
            'message_en' => "You have used {$used} out of {$limit} quote requests in your free plan. Upgrade to respond to more requests.",
            'message_ar' => "لقد استخدمت {$used} من أصل {$limit} طلب تسعير في خطتك المجانية. قم بالترقية للرد على المزيد من الطلبات.",
            'link' => "/vendor/subscription"
        ];
        
        return $this->create($data);
    }
    
    /**
     * Clean up old notifications
     * 
     * @param int $daysOld Delete notifications older than this many days
     * @return int Number of notifications deleted
     */
    public function cleanupOldNotifications($daysOld = 30) {
        $query = "DELETE FROM {$this->table} 
                  WHERE created_at < DATE_SUB(NOW(), INTERVAL :days_old DAY)";
        $params = ['days_old' => $daysOld];
        
        return $this->db->executeWithRowCount($query, $params);
    }
}
