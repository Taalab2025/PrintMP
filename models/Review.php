<?php
/**
 * Review Model
 * File path: models/Review.php
 * Handles review data operations for completed orders
 */

class Review
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Create a new review
     */
    public function create($reviewData)
    {
        try {
            $sql = "INSERT INTO reviews (
                order_id, user_id, vendor_id, rating, comment,
                status, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, 'active', NOW(), NOW())";

            $params = [
                $reviewData['order_id'],
                $reviewData['user_id'],
                $reviewData['vendor_id'],
                $reviewData['rating'],
                $reviewData['comment'] ?? null
            ];

            return $this->db->execute($sql, $params);
        } catch (Exception $e) {
            error_log("Error creating review: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get review by ID
     */
    public function getById($reviewId)
    {
        try {
            $sql = "SELECT r.*, u.name as user_name, v.company_name_en, v.company_name_ar,
                           o.service_id, s.title_en as service_title_en, s.title_ar as service_title_ar
                    FROM reviews r
                    JOIN users u ON r.user_id = u.id
                    JOIN vendors v ON r.vendor_id = v.id
                    JOIN orders o ON r.order_id = o.id
                    JOIN services s ON o.service_id = s.id
                    WHERE r.id = ?";

            return $this->db->fetchOne($sql, [$reviewId]);
        } catch (Exception $e) {
            error_log("Error getting review: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get reviews for a specific vendor
     */
    public function getByVendorId($vendorId, $limit = 10, $offset = 0, $status = 'active')
    {
        try {
            $sql = "SELECT r.*, u.name as user_name,
                           s.title_en as service_title_en, s.title_ar as service_title_ar
                    FROM reviews r
                    JOIN users u ON r.user_id = u.id
                    JOIN orders o ON r.order_id = o.id
                    JOIN services s ON o.service_id = s.id
                    WHERE r.vendor_id = ? AND r.status = ?
                    ORDER BY r.created_at DESC
                    LIMIT ? OFFSET ?";

            return $this->db->fetchAll($sql, [$vendorId, $status, $limit, $offset]);
        } catch (Exception $e) {
            error_log("Error getting vendor reviews: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get reviews by user ID
     */
    public function getByUserId($userId, $limit = 10, $offset = 0)
    {
        try {
            $sql = "SELECT r.*, v.company_name_en, v.company_name_ar,
                           s.title_en as service_title_en, s.title_ar as service_title_ar
                    FROM reviews r
                    JOIN vendors v ON r.vendor_id = v.id
                    JOIN orders o ON r.order_id = o.id
                    JOIN services s ON o.service_id = s.id
                    WHERE r.user_id = ?
                    ORDER BY r.created_at DESC
                    LIMIT ? OFFSET ?";

            return $this->db->fetchAll($sql, [$userId, $limit, $offset]);
        } catch (Exception $e) {
            error_log("Error getting user reviews: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if user can review an order
     */
    public function canReviewOrder($orderId, $userId)
    {
        try {
            // Check if order exists, is completed, and belongs to user
            $sql = "SELECT id FROM orders
                    WHERE id = ? AND user_id = ? AND status = 'delivered'";
            $order = $this->db->fetchOne($sql, [$orderId, $userId]);

            if (!$order) {
                return false;
            }

            // Check if review already exists
            $sql = "SELECT id FROM reviews WHERE order_id = ?";
            $existingReview = $this->db->fetchOne($sql, [$orderId]);

            return !$existingReview;
        } catch (Exception $e) {
            error_log("Error checking review eligibility: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get vendor average rating
     */
    public function getVendorAverageRating($vendorId)
    {
        try {
            $sql = "SELECT AVG(rating) as average_rating, COUNT(*) as total_reviews
                    FROM reviews
                    WHERE vendor_id = ? AND status = 'active'";

            $result = $this->db->fetchOne($sql, [$vendorId]);

            return [
                'average_rating' => $result['average_rating'] ? round($result['average_rating'], 1) : 0,
                'total_reviews' => $result['total_reviews']
            ];
        } catch (Exception $e) {
            error_log("Error getting vendor average rating: " . $e->getMessage());
            return ['average_rating' => 0, 'total_reviews' => 0];
        }
    }

    /**
     * Get all reviews for admin management
     */
    public function getAll($filters = [], $limit = 20, $offset = 0)
    {
        try {
            $sql = "SELECT r.*, u.name as user_name, v.company_name_en, v.company_name_ar,
                           s.title_en as service_title_en, s.title_ar as service_title_ar
                    FROM reviews r
                    JOIN users u ON r.user_id = u.id
                    JOIN vendors v ON r.vendor_id = v.id
                    JOIN orders o ON r.order_id = o.id
                    JOIN services s ON o.service_id = s.id
                    WHERE 1=1";

            $params = [];

            // Apply filters
            if (!empty($filters['status'])) {
                $sql .= " AND r.status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['rating'])) {
                $sql .= " AND r.rating = ?";
                $params[] = $filters['rating'];
            }

            if (!empty($filters['vendor_id'])) {
                $sql .= " AND r.vendor_id = ?";
                $params[] = $filters['vendor_id'];
            }

            if (!empty($filters['search'])) {
                $sql .= " AND (r.comment LIKE ? OR u.name LIKE ? OR v.company_name_en LIKE ? OR v.company_name_ar LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            $sql .= " ORDER BY r.created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            return $this->db->fetchAll($sql, $params);
        } catch (Exception $e) {
            error_log("Error getting all reviews: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update review status
     */
    public function updateStatus($reviewId, $status)
    {
        try {
            $sql = "UPDATE reviews SET status = ?, updated_at = NOW() WHERE id = ?";
            return $this->db->execute($sql, [$status, $reviewId]);
        } catch (Exception $e) {
            error_log("Error updating review status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete review
     */
    public function delete($reviewId)
    {
        try {
            $sql = "DELETE FROM reviews WHERE id = ?";
            return $this->db->execute($sql, [$reviewId]);
        } catch (Exception $e) {
            error_log("Error deleting review: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get review statistics
     */
    public function getReviewStats($vendorId = null)
    {
        try {
            $sql = "SELECT
                        COUNT(*) as total_reviews,
                        AVG(rating) as average_rating,
                        SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                        SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                        SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                        SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                        SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                    FROM reviews
                    WHERE status = 'active'";

            $params = [];

            if ($vendorId) {
                $sql .= " AND vendor_id = ?";
                $params[] = $vendorId;
            }

            $result = $this->db->fetchOne($sql, $params);

            return [
                'total_reviews' => $result['total_reviews'],
                'average_rating' => $result['average_rating'] ? round($result['average_rating'], 1) : 0,
                'rating_breakdown' => [
                    5 => $result['five_star'],
                    4 => $result['four_star'],
                    3 => $result['three_star'],
                    2 => $result['two_star'],
                    1 => $result['one_star']
                ]
            ];
        } catch (Exception $e) {
            error_log("Error getting review statistics: " . $e->getMessage());
            return [
                'total_reviews' => 0,
                'average_rating' => 0,
                'rating_breakdown' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0]
            ];
        }
    }

    /**
     * Get total count for pagination
     */
    public function getTotalCount($filters = [])
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM reviews r
                    JOIN users u ON r.user_id = u.id
                    JOIN vendors v ON r.vendor_id = v.id
                    WHERE 1=1";

            $params = [];

            // Apply same filters as getAll method
            if (!empty($filters['status'])) {
                $sql .= " AND r.status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['rating'])) {
                $sql .= " AND r.rating = ?";
                $params[] = $filters['rating'];
            }

            if (!empty($filters['vendor_id'])) {
                $sql .= " AND r.vendor_id = ?";
                $params[] = $filters['vendor_id'];
            }

            if (!empty($filters['search'])) {
                $sql .= " AND (r.comment LIKE ? OR u.name LIKE ? OR v.company_name_en LIKE ? OR v.company_name_ar LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            $result = $this->db->fetchOne($sql, $params);
            return $result['total'];
        } catch (Exception $e) {
            error_log("Error getting review count: " . $e->getMessage());
            return 0;
        }
    }
}
?>
