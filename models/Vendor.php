<?php
/**
 * Vendor Model
 * File path: models/Vendor.php
 *
 * Handles vendor-specific data and operations with enhanced multilingual support,
 * service listings, reviews functionality, and profile features
 *
 * @package Egypt Printing Services Marketplace
 * @author  Development Team
 */

class Vendor {
    /**
     * @var Database Database instance
     */
    private $db;

    /**
     * @var array Vendor data
     */
    private $data = [];

    /**
     * @var string Vendors table name
     */
    private $table = 'vendors';

    /**
     * @var string Users table name
     */
    private $userTable = 'users';

    /**
     * @var string Services table name
     */
    private $serviceTable = 'services';

    /**
     * @var string Reviews table name
     */
    private $reviewTable = 'reviews';

    /**
     * Constructor
     *
     * @param Database $db Database instance
     */
    public function __construct(Database $db) {
        $this->db = $db;
    }

    /**
     * Get vendor by ID
     *
     * @param int $id Vendor ID
     * @param string $language Current language
     * @return Vendor|null This instance or null if not found
     */
    public function getById(int $id, string $language = 'en'): ?Vendor {
        $sql = "SELECT v.*,
                v.company_name_{$language} as company_name,
                v.description_{$language} as description,
                u.name as owner_name,
                u.email,
                (SELECT COUNT(*) FROM {$this->serviceTable} WHERE vendor_id = v.id AND status = 'active') as service_count,
                (SELECT AVG(rating) FROM {$this->reviewTable} WHERE vendor_id = v.id) as rating,
                (SELECT COUNT(*) FROM {$this->reviewTable} WHERE vendor_id = v.id) as review_count
                FROM {$this->table} v
                JOIN {$this->userTable} u ON v.user_id = u.id
                WHERE v.id = ?";

        $vendor = $this->db->fetchOne($sql, [$id]);

        if ($vendor) {
            $this->data = $vendor;
            return $this;
        }

        return null;
    }

    /**
     * Get vendor by user ID
     *
     * @param int $userId User ID
     * @param string $language Current language
     * @return Vendor|null This instance or null if not found
     */
    public function getByUserId(int $userId, string $language = 'en'): ?Vendor {
        $sql = "SELECT v.*,
                v.company_name_{$language} as company_name,
                v.description_{$language} as description,
                u.name as owner_name,
                u.email,
                (SELECT COUNT(*) FROM {$this->serviceTable} WHERE vendor_id = v.id AND status = 'active') as service_count,
                (SELECT AVG(rating) FROM {$this->reviewTable} WHERE vendor_id = v.id) as rating,
                (SELECT COUNT(*) FROM {$this->reviewTable} WHERE vendor_id = v.id) as review_count
                FROM {$this->table} v
                JOIN {$this->userTable} u ON v.user_id = u.id
                WHERE v.user_id = ?";

        $vendor = $this->db->fetchOne($sql, [$userId]);

        if ($vendor) {
            $this->data = $vendor;
            return $this;
        }

        return null;
    }

    /**
     * Create a new vendor
     *
     * @param array $data Vendor data
     * @return int|bool New vendor ID or false on failure
     */
    public function create(array $data) {
        // Set creation date
        $data['created_at'] = date('Y-m-d H:i:s');

        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'pending'; // Vendors require approval
        }

        // Set default subscription fields
        if (!isset($data['subscription_status'])) {
            $data['subscription_status'] = 'free';
        }

        if (!isset($data['quote_requests_count'])) {
            $data['quote_requests_count'] = 0;
        }

        if (!isset($data['quote_requests_limit'])) {
            $data['quote_requests_limit'] = 10; // Default free tier limit
        }

        // Set default rating fields
        if (!isset($data['rating'])) {
            $data['rating'] = 0;
        }

        if (!isset($data['rating_count'])) {
            $data['rating_count'] = 0;
        }

        // Insert vendor
        $vendorId = $this->db->insert($this->table, $data);

        if ($vendorId) {
            $this->data = $this->getById($vendorId)->data;
            return $vendorId;
        }

        return false;
    }

    /**
     * Update vendor
     *
     * @param int $id Vendor ID
     * @param array $data Vendor data
     * @return bool True on success
     */
    public function update(int $id, array $data): bool {
        // Set update date
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Update vendor
        $result = $this->db->update($this->table, $data, ['id' => $id]);

        if ($result && isset($this->data['id']) && $this->data['id'] == $id) {
            // Refresh vendor data
            $this->data = $this->getById($id)->data;
        }

        return $result;
    }

    /**
     * Get all vendors with optional filtering
     *
     * @param string $language Current language
     * @param array $filters Optional filters
     * @param int $limit Limit results
     * @param int $offset Pagination offset
     * @return array Vendors array
     */
    public function getAll(string $language = 'en', array $filters = [], int $limit = 20, int $offset = 0): array {
        $sql = "SELECT v.*,
                v.company_name_{$language} as company_name,
                v.description_{$language} as description,
                u.name as owner_name,
                (SELECT COUNT(*) FROM {$this->serviceTable} WHERE vendor_id = v.id AND status = 'active') as service_count,
                (SELECT AVG(rating) FROM {$this->reviewTable} WHERE vendor_id = v.id) as rating,
                (SELECT COUNT(*) FROM {$this->reviewTable} WHERE vendor_id = v.id) as review_count
                FROM {$this->table} v
                JOIN {$this->userTable} u ON v.user_id = u.id
                WHERE v.status = 'active'";

        $params = [];

        // Apply filters
        if (!empty($filters)) {
            if (isset($filters['search_term'])) {
                $searchTerm = '%' . $filters['search_term'] . '%';
                $sql .= " AND (v.company_name_{$language} LIKE ? OR v.description_{$language} LIKE ?)";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            if (isset($filters['location'])) {
                $sql .= " AND v.location LIKE ?";
                $params[] = '%' . $filters['location'] . '%';
            }

            if (isset($filters['category_id'])) {
                $sql .= " AND EXISTS (SELECT 1 FROM {$this->serviceTable} WHERE vendor_id = v.id AND category_id = ? AND status = 'active')";
                $params[] = $filters['category_id'];
            }

            if (isset($filters['min_rating'])) {
                $sql .= " AND (SELECT AVG(rating) FROM {$this->reviewTable} WHERE vendor_id = v.id) >= ?";
                $params[] = $filters['min_rating'];
            }

            if (isset($filters['subscription_status'])) {
                $sql .= " AND v.subscription_status = ?";
                $params[] = $filters['subscription_status'];
            }
        }

        // Add sorting
        if (isset($filters['sort'])) {
            switch ($filters['sort']) {
                case 'rating_high':
                    $sql .= " ORDER BY rating DESC";
                    break;
                case 'service_count':
                    $sql .= " ORDER BY service_count DESC";
                    break;
                case 'name':
                    $sql .= " ORDER BY company_name ASC";
                    break;
                default:
                    $sql .= " ORDER BY v.id DESC";
            }
        } else {
            $sql .= " ORDER BY v.id DESC";
        }

        // Add pagination
        if ($limit > 0) {
            $sql .= " LIMIT ?";
            $params[] = $limit;

            if ($offset > 0) {
                $sql .= " OFFSET ?";
                $params[] = $offset;
            }
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Count vendors with optional filtering
     *
     * @param array $filters Optional filters
     * @return int Number of vendors
     */
    public function count(array $filters = []): int {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} v WHERE v.status = 'active'";
        $params = [];

        // Apply filters
        if (!empty($filters)) {
            if (isset($filters['location'])) {
                $sql .= " AND v.location LIKE ?";
                $params[] = '%' . $filters['location'] . '%';
            }

            if (isset($filters['category_id'])) {
                $sql .= " AND EXISTS (SELECT 1 FROM {$this->serviceTable} WHERE vendor_id = v.id AND category_id = ? AND status = 'active')";
                $params[] = $filters['category_id'];
            }

            if (isset($filters['min_rating'])) {
                $sql .= " AND (SELECT AVG(rating) FROM {$this->reviewTable} WHERE vendor_id = v.id) >= ?";
                $params[] = $filters['min_rating'];
            }

            if (isset($filters['search_term'])) {
                $searchTerm = '%' . $filters['search_term'] . '%';
                $sql .= " AND (v.company_name_en LIKE ? OR v.company_name_ar LIKE ? OR v.description_en LIKE ? OR v.description_ar LIKE ?)";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
        }

        $result = $this->db->fetchOne($sql, $params);
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Get featured vendors
     *
     * @param string $language Current language
     * @param int $limit Number of vendors to return
     * @return array Vendors array
     */
    public function getFeatured(string $language = 'en', int $limit = 5): array {
        $sql = "SELECT v.id,
                v.company_name_{$language} as company_name,
                v.logo,
                (SELECT AVG(rating) FROM {$this->reviewTable} WHERE vendor_id = v.id) as rating
                FROM {$this->table} v
                WHERE v.status = 'active' AND v.is_featured = 1
                ORDER BY rating DESC, v.id DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get services by vendor
     *
     * @param int $vendorId Vendor ID
     * @param string $language Current language
     * @param int $limit Limit results
     * @param int $offset Pagination offset
     * @return array Services array
     */
    public function getServices(int $vendorId, string $language = 'en', int $limit = 6, int $offset = 0): array {
        $titleField = "title_" . $language;

        $sql = "SELECT s.id,
                s.{$titleField} as title,
                s.slug,
                s.base_price,
                c.{$titleField} as category_name,
                c.slug as category_slug,
                (SELECT file_path FROM service_media WHERE service_id = s.id AND type = 'main' LIMIT 1) as main_image
                FROM {$this->serviceTable} s
                LEFT JOIN categories c ON s.category_id = c.id
                WHERE s.vendor_id = ? AND s.status = 'active'
                ORDER BY s.is_featured DESC, s.created_at DESC
                LIMIT ? OFFSET ?";

        return $this->db->fetchAll($sql, [
            $vendorId,
            $limit,
            $offset
        ]);
    }

    /**
     * Get reviews by vendor
     *
     * @param int $vendorId Vendor ID
     * @param int $limit Limit results
     * @param int $offset Pagination offset
     * @return array Reviews array
     */
    public function getReviews(int $vendorId, int $limit = 5, int $offset = 0): array {
        $sql = "SELECT r.*,
                u.name as user_name,
                o.id as order_id,
                s.title_en as service_title_en,
                s.title_ar as service_title_ar
                FROM {$this->reviewTable} r
                JOIN orders o ON r.order_id = o.id
                JOIN {$this->serviceTable} s ON o.service_id = s.id
                JOIN {$this->userTable} u ON r.user_id = u.id
                WHERE r.vendor_id = ?
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?";

        return $this->db->fetchAll($sql, [
            $vendorId,
            $limit,
            $offset
        ]);
    }

    /**
     * Update vendor subscription status
     *
     * @param int $id Vendor ID
     * @param string $status Subscription status (free, active, expired)
     * @param string|null $expiryDate Expiry date
     * @return bool Success flag
     */
    public function updateSubscription(int $id, string $status, ?string $expiryDate = null): bool {
        $updateData = [
            'subscription_status' => $status,
            'subscription_expires' => $expiryDate,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->update($this->table, $updateData, ['id' => $id]);
    }

    /**
     * Increment quote requests count
     *
     * @param int $id Vendor ID
     * @return bool Success flag
     */
    public function incrementQuoteRequestsCount(int $id): bool {
        $sql = "UPDATE {$this->table} SET
                quote_requests_count = quote_requests_count + 1,
                updated_at = ?
                WHERE id = ?";

        return $this->db->execute($sql, [
            date('Y-m-d H:i:s'),
            $id
        ]);
    }

    /**
     * Reset quote requests count (e.g., when subscription renews)
     *
     * @param int $id Vendor ID
     * @return bool Success flag
     */
    public function resetQuoteRequestsCount(int $id): bool {
        $updateData = [
            'quote_requests_count' => 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->update($this->table, $updateData, ['id' => $id]);
    }

    /**
     * Check if vendor has reached their free quote request limit
     *
     * @param int $id Vendor ID
     * @param int $freeLimit Free quota limit (default: 10)
     * @return bool True if limit reached
     */
    public function hasReachedFreeQuoteLimit(int $id, int $freeLimit = 10): bool {
        $vendor = $this->getById($id);

        if (!$vendor) {
            return false;
        }

        // If vendor has an active subscription, they can always respond to quotes
        if ($this->data['subscription_status'] === 'active') {
            return false;
        }

        // Check if they've reached the free quote limit
        return (int)$this->data['quote_requests_count'] >= $freeLimit;
    }

    /**
     * Update vendor rating based on a new review
     *
     * @param int $id Vendor ID
     * @param float $newRating New rating value
     * @return bool True on success
     */
    public function updateRating(int $id, float $newRating): bool {
        $vendor = $this->getById($id);

        if ($vendor) {
            $currentRating = (float)$this->data['rating'];
            $ratingCount = (int)$this->data['rating_count'];

            $updatedRating = ($currentRating * $ratingCount + $newRating) / ($ratingCount + 1);
            $updatedRatingCount = $ratingCount + 1;

            return $this->db->update($this->table, [
                'rating' => round($updatedRating, 2),
                'rating_count' => $updatedRatingCount,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id]);
        }

        return false;
    }

    /**
     * Get vendor data
     *
     * @return array Vendor data
     */
    public function getData(): array {
        return $this->data;
    }

    /**
     * Get vendors by category
     *
     * @param int $categoryId Category ID
     * @param string $language Current language
     * @param int $limit Limit results
     * @param int $offset Offset results
     * @return array Vendors in category
     */
    public function getByCategory(int $categoryId, string $language = 'en', int $limit = 0, int $offset = 0): array {
        $sql = "SELECT DISTINCT v.*,
                v.company_name_{$language} as company_name,
                v.description_{$language} as description,
                u.name as owner_name,
                (SELECT COUNT(*) FROM {$this->serviceTable} WHERE vendor_id = v.id AND status = 'active') as service_count,
                (SELECT AVG(rating) FROM {$this->reviewTable} WHERE vendor_id = v.id) as rating
                FROM {$this->table} v
                JOIN {$this->userTable} u ON v.user_id = u.id
                JOIN {$this->serviceTable} s ON s.vendor_id = v.id
                WHERE v.status = 'active' AND s.category_id = ? AND s.status = 'active'
                ORDER BY v.rating DESC, v.created_at DESC";

        $params = [$categoryId];

        // Apply limit and offset
        if ($limit > 0) {
            $sql .= " LIMIT ?";
            $params[] = $limit;

            if ($offset > 0) {
                $sql .= " OFFSET ?";
                $params[] = $offset;
            }
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Approve vendor
     *
     * @param int $id Vendor ID
     * @return bool True on success
     */
    public function approve(int $id): bool {
        return $this->db->update($this->table, [
            'status' => 'active',
            'updated_at' => date('Y-m-d H:i:s')
        ], ['id' => $id]);
    }

    /**
     * Reject vendor
     *
     * @param int $id Vendor ID
     * @return bool True on success
     */
    public function reject(int $id): bool {
        return $this->db->update($this->table, [
            'status' => 'rejected',
            'updated_at' => date('Y-m-d H:i:s')
        ], ['id' => $id]);
    }

    /**
     * Suspend vendor
     *
     * @param int $id Vendor ID
     * @return bool True on success
     */
    public function suspend(int $id): bool {
        return $this->db->update($this->table, [
            'status' => 'suspended',
            'updated_at' => date('Y-m-d H:i:s')
        ], ['id' => $id]);
    }

    /**
     * Mark vendor as featured
     *
     * @param int $id Vendor ID
     * @param bool $featured Featured status
     * @return bool True on success
     */
    public function setFeatured(int $id, bool $featured = true): bool {
        return $this->db->update($this->table, [
            'is_featured' => $featured ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ], ['id' => $id]);
    }

    /**
     * Get vendors with expiring subscriptions
     *
     * @param int $daysThreshold Days before expiration
     * @return array Vendors with expiring subscription
     */
    public function getWithExpiringSubscription(int $daysThreshold = 3): array {
        $expiryDate = date('Y-m-d', strtotime("+{$daysThreshold} days"));

        $sql = "SELECT v.*, u.name, u.email
                FROM {$this->table} v
                JOIN {$this->userTable} u ON v.user_id = u.id
                WHERE v.subscription_status = 'active'
                AND v.subscription_expires <= ?
                ORDER BY v.subscription_expires ASC";

        return $this->db->fetchAll($sql, [$expiryDate]);
    }
}
