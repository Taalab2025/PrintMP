<?php
/**
 * Quote Model
 * File path: models/Quote.php
 *
 * Handles quote data operations from vendors
 */

class Quote {
    private $db;
    private $table = 'quotes';

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Create a new quote
     *
     * @param array $data Quote data
     * @return int|false New quote ID or false on failure
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table}
                  (quote_request_id, vendor_id, price, estimated_delivery_days,
                   message, status, created_at, valid_until)
                  VALUES (:quote_request_id, :vendor_id, :price, :estimated_delivery_days,
                  :message, :status, NOW(), :valid_until)";

        // Set validity to 7 days by default if not specified
        if (!isset($data['valid_until'])) {
            $data['valid_until'] = date('Y-m-d H:i:s', strtotime('+7 days'));
        }

        $params = [
            'quote_request_id' => $data['quote_request_id'],
            'vendor_id' => $data['vendor_id'],
            'price' => $data['price'],
            'estimated_delivery_days' => $data['estimated_delivery_days'],
            'message' => $data['message'] ?? null,
            'status' => $data['status'] ?? 'offered',
            'valid_until' => $data['valid_until']
        ];

        $id = $this->db->insert($query, $params);

        // If quote was created successfully, update the quote request status
        if ($id) {
            $this->updateQuoteRequestStatus($data['quote_request_id'], 'quoted');
        }

        return $id;
    }

    /**
     * Update quote request status
     *
     * @param int $quoteRequestId Quote request ID
     * @param string $status New status
     * @return bool Success status
     */
    private function updateQuoteRequestStatus($quoteRequestId, $status) {
        $query = "UPDATE quote_requests SET status = :status WHERE id = :id";
        $params = ['id' => $quoteRequestId, 'status' => $status];

        return $this->db->execute($query, $params);
    }

    /**
     * Get quote by ID
     *
     * @param int $id Quote ID
     * @return array|false Quote data or false if not found
     */
    public function getById($id) {
        $query = "SELECT q.*, v.company_name_en, v.company_name_ar, v.logo, v.rating
                  FROM {$this->table} q
                  LEFT JOIN vendors v ON q.vendor_id = v.id
                  WHERE q.id = :id";

        $params = ['id' => $id];
        return $this->db->selectOne($query, $params);
    }

    /**
     * Get all quotes for a request
     *
     * @param int $quoteRequestId Quote request ID
     * @return array Quotes
     */
    public function getByRequestId($quoteRequestId) {
        $query = "SELECT q.*, v.company_name_en, v.company_name_ar, v.logo, v.rating
                  FROM {$this->table} q
                  LEFT JOIN vendors v ON q.vendor_id = v.id
                  WHERE q.quote_request_id = :quote_request_id
                  ORDER BY q.price ASC";

        $params = ['quote_request_id' => $quoteRequestId];
        return $this->db->select($query, $params);
    }

    /**
     * Check if a vendor has already quoted on a request
     *
     * @param int $quoteRequestId Quote request ID
     * @param int $vendorId Vendor ID
     * @return bool True if quoted, false otherwise
     */
    public function hasQuoted($quoteRequestId, $vendorId) {
        $query = "SELECT COUNT(*) as count
                  FROM {$this->table}
                  WHERE quote_request_id = :quote_request_id
                  AND vendor_id = :vendor_id";

        $params = [
            'quote_request_id' => $quoteRequestId,
            'vendor_id' => $vendorId
        ];

        $result = $this->db->selectOne($query, $params);
        return $result && $result['count'] > 0;
    }

    /**
     * Update a quote
     *
     * @param int $id Quote ID
     * @param array $data Quote data
     * @return bool Success status
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET
                  price = :price,
                  estimated_delivery_days = :estimated_delivery_days,
                  message = :message,
                  valid_until = :valid_until,
                  updated_at = NOW()
                  WHERE id = :id AND status = 'offered'";

        $params = [
            'id' => $id,
            'price' => $data['price'],
            'estimated_delivery_days' => $data['estimated_delivery_days'],
            'message' => $data['message'] ?? null,
            'valid_until' => $data['valid_until'] ?? date('Y-m-d H:i:s', strtotime('+7 days'))
        ];

        return $this->db->execute($query, $params);
    }

    /**
     * Accept a quote
     *
     * @param int $id Quote ID
     * @return bool Success status
     */
    public function accept($id) {
        // First get the quote and quote request IDs
        $quote = $this->getById($id);

        if (!$quote) {
            return false;
        }

        // Update this quote status to accepted
        $query = "UPDATE {$this->table} SET status = 'accepted', updated_at = NOW() WHERE id = :id";
        $params = ['id' => $id];

        $success = $this->db->execute($query, $params);

        if ($success) {
            // Update all other quotes for this request to declined
            $query = "UPDATE {$this->table}
                      SET status = 'declined', updated_at = NOW()
                      WHERE quote_request_id = :quote_request_id AND id != :id";

            $params = [
                'quote_request_id' => $quote['quote_request_id'],
                'id' => $id
            ];

            $this->db->execute($query, $params);

            // Update the quote request status
            $this->updateQuoteRequestStatus($quote['quote_request_id'], 'accepted');
        }

        return $success;
    }

    /**
     * Decline a quote
     *
     * @param int $id Quote ID
     * @return bool Success status
     */
    public function decline($id) {
        $query = "UPDATE {$this->table} SET status = 'declined', updated_at = NOW() WHERE id = :id";
        $params = ['id' => $id];

        return $this->db->execute($query, $params);
    }

    /**
     * Get quotes by vendor ID
     *
     * @param int $vendorId Vendor ID
     * @param string $status Optional status filter
     * @param int $limit Number of records to return
     * @param int $offset Offset for pagination
     * @return array Quotes
     */
    public function getByVendorId($vendorId, $status = null, $limit = 10, $offset = 0) {
        $query = "SELECT q.*, qr.service_id, s.title_en, s.title_ar,
                  qr.contact_name, qr.created_at as request_date
                  FROM {$this->table} q
                  LEFT JOIN quote_requests qr ON q.quote_request_id = qr.id
                  LEFT JOIN services s ON qr.service_id = s.id
                  WHERE q.vendor_id = :vendor_id";

        $params = ['vendor_id' => $vendorId];

        if ($status) {
            $query .= " AND q.status = :status";
            $params['status'] = $status;
        }

        $query .= " ORDER BY q.created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        return $this->db->select($query, $params);
    }

    /**
     * Count quotes by vendor ID
     *
     * @param int $vendorId Vendor ID
     * @param string $status Optional status filter
     * @return int Count
     */
    public function countByVendor($vendorId, $status = null) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE vendor_id = :vendor_id";
        $params = ['vendor_id' => $vendorId];

        if ($status) {
            $query .= " AND status = :status";
            $params['status'] = $status;
        }

        $result = $this->db->selectOne($query, $params);
        return $result ? $result['count'] : 0;
    }

    /**
     * Get quotes by user ID (through quote requests)
     *
     * @param int $userId User ID
     * @param string $status Optional status filter
     * @param int $limit Number of records to return
     * @param int $offset Offset for pagination
     * @return array Quotes
     */
    public function getByUserId($userId, $status = null, $limit = 10, $offset = 0) {
        $query = "SELECT q.*, v.company_name_en, v.company_name_ar, v.logo, v.rating,
                  s.title_en, s.title_ar, qr.created_at as request_date
                  FROM {$this->table} q
                  JOIN quote_requests qr ON q.quote_request_id = qr.id
                  LEFT JOIN vendors v ON q.vendor_id = v.id
                  LEFT JOIN services s ON qr.service_id = s.id
                  WHERE qr.user_id = :user_id";

        $params = ['user_id' => $userId];

        if ($status) {
            $query .= " AND q.status = :status";
            $params['status'] = $status;
        }

        $query .= " ORDER BY q.created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        return $this->db->select($query, $params);
    }

    /**
     * Check if a quote is expired
     *
     * @param array $quote Quote data with valid_until field
     * @return bool True if expired, false otherwise
     */
    public function isExpired($quote) {
        if ($quote['status'] !== 'offered') {
            return false; // Only offered quotes can expire
        }

        $validUntil = strtotime($quote['valid_until']);
        $now = time();

        return $validUntil < $now;
    }

    /**
     * Get vendor's quote for a specific request
     *
     * @param int $quoteRequestId Quote request ID
     * @param int $vendorId Vendor ID
     * @return array|false Quote data or false if not found
     */
    public function getVendorQuote($quoteRequestId, $vendorId) {
        $query = "SELECT * FROM {$this->table}
                  WHERE quote_request_id = :quote_request_id
                  AND vendor_id = :vendor_id";

        $params = [
            'quote_request_id' => $quoteRequestId,
            'vendor_id' => $vendorId
        ];

        return $this->db->selectOne($query, $params);
    }
}
