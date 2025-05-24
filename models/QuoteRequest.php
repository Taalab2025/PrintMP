<?php
/**
 * QuoteRequest Model
 * File path: models/QuoteRequest.php
 *
 * Handles quote request data operations
 */

class QuoteRequest {
    private $db;
    private $table = 'quote_requests';
    private $filesTable = 'quote_request_files';
    private $optionsTable = 'quote_request_options';

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Create a new quote request
     *
     * @param array $data Quote request data
     * @return int|false New quote request ID or false on failure
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table}
                  (user_id, service_id, vendor_id, status, message, delivery_address, contact_name, contact_email,
                   contact_phone, created_at)
                  VALUES (:user_id, :service_id, :vendor_id, :status, :message, :delivery_address, :contact_name,
                  :contact_email, :contact_phone, NOW())";

        $params = [
            'user_id' => $data['user_id'] ?? null,
            'service_id' => $data['service_id'],
            'vendor_id' => $data['vendor_id'],
            'status' => $data['status'] ?? 'pending',
            'message' => $data['message'] ?? null,
            'delivery_address' => $data['delivery_address'] ?? null,
            'contact_name' => $data['contact_name'],
            'contact_email' => $data['contact_email'],
            'contact_phone' => $data['contact_phone'] ?? null
        ];

        $id = $this->db->insert($query, $params);

        if ($id && isset($data['options']) && is_array($data['options'])) {
            $this->saveOptions($id, $data['options']);
        }

        return $id;
    }

    /**
     * Save quote request options
     *
     * @param int $quoteRequestId Quote request ID
     * @param array $options Array of options
     * @return bool Success status
     */
    private function saveOptions($quoteRequestId, $options) {
        $success = true;

        foreach ($options as $option) {
            $query = "INSERT INTO {$this->optionsTable}
                     (quote_request_id, option_name, option_value)
                     VALUES (:quote_request_id, :option_name, :option_value)";

            $params = [
                'quote_request_id' => $quoteRequestId,
                'option_name' => $option['name'],
                'option_value' => $option['value']
            ];

            if (!$this->db->execute($query, $params)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Add file to quote request
     *
     * @param int $quoteRequestId Quote request ID
     * @param string $filePath Path to uploaded file
     * @param string $fileName Original file name
     * @param string $fileType File MIME type
     * @return int|false New file ID or false on failure
     */
    public function addFile($quoteRequestId, $filePath, $fileName, $fileType) {
        $query = "INSERT INTO {$this->filesTable}
                 (quote_request_id, file_path, file_name, file_type, uploaded_at)
                 VALUES (:quote_request_id, :file_path, :file_name, :file_type, NOW())";

        $params = [
            'quote_request_id' => $quoteRequestId,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType
        ];

        return $this->db->insert($query, $params);
    }

    /**
     * Get quote request by ID
     *
     * @param int $id Quote request ID
     * @return array|false Quote request data or false if not found
     */
    public function getById($id) {
        $query = "SELECT qr.*, s.title_en, s.title_ar, v.company_name_en, v.company_name_ar
                  FROM {$this->table} qr
                  LEFT JOIN services s ON qr.service_id = s.id
                  LEFT JOIN vendors v ON qr.vendor_id = v.id
                  WHERE qr.id = :id";

        $params = ['id' => $id];
        $quoteRequest = $this->db->selectOne($query, $params);

        if ($quoteRequest) {
            $quoteRequest['options'] = $this->getOptions($id);
            $quoteRequest['files'] = $this->getFiles($id);
            $quoteRequest['quotes'] = $this->getQuotes($id);
        }

        return $quoteRequest;
    }

    /**
     * Get quote request options
     *
     * @param int $quoteRequestId Quote request ID
     * @return array Options
     */
    private function getOptions($quoteRequestId) {
        $query = "SELECT option_name, option_value
                  FROM {$this->optionsTable}
                  WHERE quote_request_id = :quote_request_id";

        $params = ['quote_request_id' => $quoteRequestId];
        return $this->db->select($query, $params);
    }

    /**
     * Get quote request files
     *
     * @param int $quoteRequestId Quote request ID
     * @return array Files
     */
    private function getFiles($quoteRequestId) {
        $query = "SELECT id, file_path, file_name, file_type, uploaded_at
                  FROM {$this->filesTable}
                  WHERE quote_request_id = :quote_request_id";

        $params = ['quote_request_id' => $quoteRequestId];
        return $this->db->select($query, $params);
    }

    /**
     * Get quotes for a quote request
     *
     * @param int $quoteRequestId Quote request ID
     * @return array Quotes
     */
    private function getQuotes($quoteRequestId) {
        $query = "SELECT q.*
                  FROM quotes q
                  WHERE q.quote_request_id = :quote_request_id";

        $params = ['quote_request_id' => $quoteRequestId];
        return $this->db->select($query, $params);
    }

    /**
     * Get quote requests by user ID
     *
     * @param int $userId User ID
     * @param int $limit Number of records to return
     * @param int $offset Offset for pagination
     * @return array Quote requests
     */
    public function getByUserId($userId, $limit = 10, $offset = 0) {
        $query = "SELECT qr.*, s.title_en, s.title_ar, v.company_name_en, v.company_name_ar,
                  (SELECT COUNT(*) FROM quotes q WHERE q.quote_request_id = qr.id) as quote_count
                  FROM {$this->table} qr
                  LEFT JOIN services s ON qr.service_id = s.id
                  LEFT JOIN vendors v ON qr.vendor_id = v.id
                  WHERE qr.user_id = :user_id
                  ORDER BY qr.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $params = [
            'user_id' => $userId,
            'limit' => $limit,
            'offset' => $offset
        ];

        return $this->db->select($query, $params);
    }

    /**
     * Get quote requests by vendor ID
     *
     * @param int $vendorId Vendor ID
     * @param string $status Optional status filter
     * @param int $limit Number of records to return
     * @param int $offset Offset for pagination
     * @return array Quote requests
     */
    public function getByVendorId($vendorId, $status = null, $limit = 10, $offset = 0) {
        $query = "SELECT qr.*, s.title_en, s.title_ar,
                  (SELECT COUNT(*) FROM quotes q WHERE q.quote_request_id = qr.id AND q.vendor_id = :vendor_id) as has_quoted
                  FROM {$this->table} qr
                  LEFT JOIN services s ON qr.service_id = s.id
                  WHERE (qr.vendor_id = :vendor_id OR s.vendor_id = :vendor_id)";

        $params = ['vendor_id' => $vendorId];

        if ($status) {
            $query .= " AND qr.status = :status";
            $params['status'] = $status;
        }

        $query .= " ORDER BY qr.created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        return $this->db->select($query, $params);
    }

    /**
     * Update quote request status
     *
     * @param int $id Quote request ID
     * @param string $status New status
     * @return bool Success status
     */
    public function updateStatus($id, $status) {
        $query = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $params = ['id' => $id, 'status' => $status];

        return $this->db->execute($query, $params);
    }

    /**
     * Count total quote requests for a user
     *
     * @param int $userId User ID
     * @return int Count
     */
    public function countByUser($userId) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = :user_id";
        $params = ['user_id' => $userId];

        $result = $this->db->selectOne($query, $params);
        return $result ? $result['count'] : 0;
    }

    /**
     * Count total quote requests for a vendor
     *
     * @param int $vendorId Vendor ID
     * @param string $status Optional status filter
     * @return int Count
     */
    public function countByVendor($vendorId, $status = null) {
        $query = "SELECT COUNT(*) as count
                 FROM {$this->table} qr
                 LEFT JOIN services s ON qr.service_id = s.id
                 WHERE (qr.vendor_id = :vendor_id OR s.vendor_id = :vendor_id)";

        $params = ['vendor_id' => $vendorId];

        if ($status) {
            $query .= " AND qr.status = :status";
            $params['status'] = $status;
        }

        $result = $this->db->selectOne($query, $params);
        return $result ? $result['count'] : 0;
    }

    /**
     * Delete a quote request file
     *
     * @param int $fileId File ID
     * @return bool Success status
     */
    public function deleteFile($fileId) {
        // First get the file info to delete the actual file
        $query = "SELECT file_path FROM {$this->filesTable} WHERE id = :id";
        $params = ['id' => $fileId];

        $file = $this->db->selectOne($query, $params);

        if ($file && file_exists($file['file_path'])) {
            unlink($file['file_path']);
        }

        $query = "DELETE FROM {$this->filesTable} WHERE id = :id";
        return $this->db->execute($query, $params);
    }

    /**
     * Get total quote requests in the period for a vendor
     * Used for freemium model tracking
     *
     * @param int $vendorId Vendor ID
     * @param string $startDate Start date (Y-m-d)
     * @param string $endDate End date (Y-m-d)
     * @return int Count
     */
    public function countVendorRequestsInPeriod($vendorId, $startDate, $endDate) {
        $query = "SELECT COUNT(*) as count
                 FROM {$this->table} qr
                 LEFT JOIN services s ON qr.service_id = s.id
                 WHERE (qr.vendor_id = :vendor_id OR s.vendor_id = :vendor_id)
                 AND qr.created_at BETWEEN :start_date AND :end_date";

        $params = [
            'vendor_id' => $vendorId,
            'start_date' => $startDate . ' 00:00:00',
            'end_date' => $endDate . ' 23:59:59'
        ];

        $result = $this->db->selectOne($query, $params);
        return $result ? $result['count'] : 0;
    }
}
