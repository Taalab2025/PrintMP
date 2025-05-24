<?php
/**
 * Subscription Model
 * File path: models/Subscription.php
 * Handles vendor subscription management and freemium model enforcement
 */

class Subscription
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Create or update vendor subscription
     */
    public function createOrUpdate($subscriptionData)
    {
        try {
            // Check if subscription already exists
            $existing = $this->getByVendorId($subscriptionData['vendor_id']);

            if ($existing) {
                return $this->update($subscriptionData);
            } else {
                return $this->create($subscriptionData);
            }
        } catch (Exception $e) {
            error_log("Error creating/updating subscription: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create new subscription
     */
    private function create($subscriptionData)
    {
        try {
            $sql = "INSERT INTO subscriptions (
                vendor_id, plan, status, start_date, end_date,
                monthly_quote_limit, quote_count_used, features,
                created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $params = [
                $subscriptionData['vendor_id'],
                $subscriptionData['plan'],
                $subscriptionData['status'] ?? 'active',
                $subscriptionData['start_date'],
                $subscriptionData['end_date'] ?? null,
                $subscriptionData['monthly_quote_limit'] ?? $this->getPlanQuoteLimit($subscriptionData['plan']),
                $subscriptionData['quote_count_used'] ?? 0,
                json_encode($subscriptionData['features'] ?? $this->getPlanFeatures($subscriptionData['plan']))
            ];

            return $this->db->execute($sql, $params);
        } catch (Exception $e) {
            error_log("Error creating subscription: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update existing subscription
     */
    private function update($subscriptionData)
    {
        try {
            $sql = "UPDATE subscriptions SET
                        plan = ?, status = ?, start_date = ?, end_date = ?,
                        monthly_quote_limit = ?, features = ?, updated_at = NOW()
                    WHERE vendor_id = ?";

            $params = [
                $subscriptionData['plan'],
                $subscriptionData['status'] ?? 'active',
                $subscriptionData['start_date'],
                $subscriptionData['end_date'] ?? null,
                $subscriptionData['monthly_quote_limit'] ?? $this->getPlanQuoteLimit($subscriptionData['plan']),
                json_encode($subscriptionData['features'] ?? $this->getPlanFeatures($subscriptionData['plan'])),
                $subscriptionData['vendor_id']
            ];

            return $this->db->execute($sql, $params);
        } catch (Exception $e) {
            error_log("Error updating subscription: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get subscription by vendor ID
     */
    public function getByVendorId($vendorId)
    {
        try {
            $sql = "SELECT * FROM subscriptions WHERE vendor_id = ?";
            $subscription = $this->db->fetchOne($sql, [$vendorId]);

            if ($subscription && $subscription['features']) {
                $subscription['features'] = json_decode($subscription['features'], true);
            }

            return $subscription;
        } catch (Exception $e) {
            error_log("Error getting subscription: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get vendor subscription status with quote usage
     */
    public function getVendorStatus($vendorId)
    {
        try {
            $subscription = $this->getByVendorId($vendorId);

            if (!$subscription) {
                // Create default free subscription
                $this->createOrUpdate([
                    'vendor_id' => $vendorId,
                    'plan' => 'free',
                    'status' => 'active',
                    'start_date' => date('Y-m-d')
                ]);

                $subscription = $this->getByVendorId($vendorId);
            }

            // Check if subscription is expired
            if ($subscription['end_date'] && $subscription['end_date'] < date('Y-m-d')) {
                $this->updateStatus($vendorId, 'expired');
                $subscription['status'] = 'expired';
            }

            // Get current month quote usage
            $currentQuoteCount = $this->getCurrentMonthQuoteCount($vendorId);

            return [
                'plan' => $subscription['plan'],
                'status' => $subscription['status'],
                'monthly_quote_limit' => $subscription['monthly_quote_limit'],
                'quote_count_used' => $currentQuoteCount,
                'quotes_remaining' => max(0, $subscription['monthly_quote_limit'] - $currentQuoteCount),
                'can_respond_to_quotes' => $this->canRespondToQuotes($vendorId),
                'features' => $subscription['features'] ?? [],
                'start_date' => $subscription['start_date'],
                'end_date' => $subscription['end_date']
            ];
        } catch (Exception $e) {
            error_log("Error getting vendor status: " . $e->getMessage());
            return $this->getDefaultStatus();
        }
    }

    /**
     * Check if vendor can respond to quotes
     */
    public function canRespondToQuotes($vendorId)
    {
        try {
            $subscription = $this->getByVendorId($vendorId);

            if (!$subscription || $subscription['status'] !== 'active') {
                return false;
            }

            // Unlimited for premium plans
            if ($subscription['plan'] !== 'free') {
                return true;
            }

            // Check quote limit for free plan
            $currentCount = $this->getCurrentMonthQuoteCount($vendorId);
            return $currentCount < $subscription['monthly_quote_limit'];
        } catch (Exception $e) {
            error_log("Error checking quote response eligibility: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Increment quote count when vendor responds
     */
    public function incrementQuoteCount($vendorId)
    {
        try {
            $sql = "UPDATE subscriptions
                    SET quote_count_used = quote_count_used + 1, updated_at = NOW()
                    WHERE vendor_id = ?";

            return $this->db->execute($sql, [$vendorId]);
        } catch (Exception $e) {
            error_log("Error incrementing quote count: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get current month quote count from quotes table
     */
    private function getCurrentMonthQuoteCount($vendorId)
    {
        try {
            $sql = "SELECT COUNT(*) as count
                    FROM quotes
                    WHERE vendor_id = ?
                    AND YEAR(created_at) = YEAR(CURDATE())
                    AND MONTH(created_at) = MONTH(CURDATE())";

            $result = $this->db->fetchOne($sql, [$vendorId]);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Error getting current month quote count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Update subscription status
     */
    public function updateStatus($vendorId, $status)
    {
        try {
            $sql = "UPDATE subscriptions SET status = ?, updated_at = NOW() WHERE vendor_id = ?";
            return $this->db->execute($sql, [$status, $vendorId]);
        } catch (Exception $e) {
            error_log("Error updating subscription status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Log subscription change
     */
    public function logSubscriptionChange($vendorId, $oldPlan, $newPlan, $reason = null)
    {
        try {
            $sql = "INSERT INTO subscription_logs (
                vendor_id, old_plan, new_plan, reason, created_at
            ) VALUES (?, ?, ?, ?, NOW())";

            return $this->db->execute($sql, [$vendorId, $oldPlan, $newPlan, $reason]);
        } catch (Exception $e) {
            error_log("Error logging subscription change: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get subscription plans configuration
     */
    public function getPlans()
    {
        return [
            'free' => [
                'name_en' => 'Free Plan',
                'name_ar' => 'الخطة المجانية',
                'price' => 0,
                'quote_limit' => 10,
                'features' => [
                    'basic_listing' => true,
                    'quote_responses' => true,
                    'basic_analytics' => true,
                    'featured_listing' => false,
                    'priority_support' => false,
                    'advanced_analytics' => false
                ],
                'description_en' => 'Perfect for getting started with up to 10 quote responses per month',
                'description_ar' => 'مثالية للبداية مع ما يصل إلى 10 ردود على طلبات الأسعار شهرياً'
            ],
            'basic' => [
                'name_en' => 'Basic Plan',
                'name_ar' => 'الخطة الأساسية',
                'price' => 99,
                'quote_limit' => -1, // Unlimited
                'features' => [
                    'basic_listing' => true,
                    'quote_responses' => true,
                    'basic_analytics' => true,
                    'featured_listing' => false,
                    'priority_support' => false,
                    'advanced_analytics' => true
                ],
                'description_en' => 'Unlimited quote responses with advanced analytics',
                'description_ar' => 'ردود غير محدودة على طلبات الأسعار مع تحليلات متقدمة'
            ],
            'premium' => [
                'name_en' => 'Premium Plan',
                'name_ar' => 'الخطة المميزة',
                'price' => 199,
                'quote_limit' => -1, // Unlimited
                'features' => [
                    'basic_listing' => true,
                    'quote_responses' => true,
                    'basic_analytics' => true,
                    'featured_listing' => true,
                    'priority_support' => true,
                    'advanced_analytics' => true
                ],
                'description_en' => 'Everything in Basic plus featured listing and priority support',
                'description_ar' => 'كل ما في الخطة الأساسية بالإضافة للإدراج المميز والدعم الأولوي'
            ]
        ];
    }

    /**
     * Get quote limit for plan
     */
    private function getPlanQuoteLimit($plan)
    {
        $plans = $this->getPlans();
        return $plans[$plan]['quote_limit'] ?? 10;
    }

    /**
     * Get features for plan
     */
    private function getPlanFeatures($plan)
    {
        $plans = $this->getPlans();
        return $plans[$plan]['features'] ?? [];
    }

    /**
     * Get default subscription status
     */
    private function getDefaultStatus()
    {
        return [
            'plan' => 'free',
            'status' => 'active',
            'monthly_quote_limit' => 10,
            'quote_count_used' => 0,
            'quotes_remaining' => 10,
            'can_respond_to_quotes' => true,
            'features' => $this->getPlanFeatures('free'),
            'start_date' => date('Y-m-d'),
            'end_date' => null
        ];
    }

    /**
     * Get all subscriptions for admin
     */
    public function getAll($filters = [], $limit = 20, $offset = 0)
    {
        try {
            $sql = "SELECT s.*, v.company_name_en, v.company_name_ar, u.name as user_name, u.email
                    FROM subscriptions s
                    JOIN vendors v ON s.vendor_id = v.id
                    JOIN users u ON v.user_id = u.id
                    WHERE 1=1";

            $params = [];

            if (!empty($filters['plan'])) {
                $sql .= " AND s.plan = ?";
                $params[] = $filters['plan'];
            }

            if (!empty($filters['status'])) {
                $sql .= " AND s.status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['search'])) {
                $sql .= " AND (v.company_name_en LIKE ? OR v.company_name_ar LIKE ? OR u.name LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            $sql .= " ORDER BY s.created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            return $this->db->fetchAll($sql, $params);
        } catch (Exception $e) {
            error_log("Error getting all subscriptions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get subscription statistics
     */
    public function getSubscriptionStats()
    {
        try {
            $sql = "SELECT
                        plan,
                        COUNT(*) as count,
                        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count
                    FROM subscriptions
                    GROUP BY plan";

            $results = $this->db->fetchAll($sql);

            $stats = [
                'total_subscriptions' => 0,
                'active_subscriptions' => 0,
                'by_plan' => []
            ];

            foreach ($results as $result) {
                $stats['total_subscriptions'] += $result['count'];
                $stats['active_subscriptions'] += $result['active_count'];
                $stats['by_plan'][$result['plan']] = [
                    'total' => $result['count'],
                    'active' => $result['active_count']
                ];
            }

            return $stats;
        } catch (Exception $e) {
            error_log("Error getting subscription statistics: " . $e->getMessage());
            return [
                'total_subscriptions' => 0,
                'active_subscriptions' => 0,
                'by_plan' => []
            ];
        }
    }

    /**
     * Reset monthly quote counts (to be called monthly via cron)
     */
    public function resetMonthlyQuoteCounts()
    {
        try {
            $sql = "UPDATE subscriptions SET quote_count_used = 0, updated_at = NOW()";
            return $this->db->execute($sql);
        } catch (Exception $e) {
            error_log("Error resetting monthly quote counts: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total count for pagination
     */
    public function getTotalCount($filters = [])
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM subscriptions s
                    JOIN vendors v ON s.vendor_id = v.id
                    JOIN users u ON v.user_id = u.id
                    WHERE 1=1";

            $params = [];

            if (!empty($filters['plan'])) {
                $sql .= " AND s.plan = ?";
                $params[] = $filters['plan'];
            }

            if (!empty($filters['status'])) {
                $sql .= " AND s.status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['search'])) {
                $sql .= " AND (v.company_name_en LIKE ? OR v.company_name_ar LIKE ? OR u.name LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            $result = $this->db->fetchOne($sql, $params);
            return $result['total'];
        } catch (Exception $e) {
            error_log("Error getting subscription count: " . $e->getMessage());
            return 0;
        }
    }
}
?>
