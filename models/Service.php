<?php
/**
 * Service Model
 *
 * File path: models/Service.php
 *
 * Handles service data operations with multilingual support
 */

class Service {
    private $db;
    private $table = 'services';
    private $mediaTable = 'service_media';

    /**
     * Constructor
     *
     * @param Database $db Database instance
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Get all services with optional filtering
     *
     * @param string $language Current language
     * @param array $filters Optional filters (category_id, vendor_id, search_term, etc.)
     * @param int $limit Limit results
     * @param int $offset Pagination offset
     * @return array Services array
     */
    public function getAll($language = 'en', $filters = [], $limit = 20, $offset = 0) {
        $titleField = "title_" . $language;
        $descriptionField = "description_" . $language;

        $sql = "SELECT s.*,
                s.{$titleField} as title,
                s.{$descriptionField} as description,
                c.{$titleField} as category_name,
                v.company_name_{$language} as vendor_name,
                u.name as vendor_owner_name,
                (SELECT COUNT(*) FROM {$this->mediaTable} WHERE service_id = s.id) as media_count,
                (SELECT file_path FROM {$this->mediaTable} WHERE service_id = s.id AND type = 'main' LIMIT 1) as main_image
                FROM {$this->table} s
                LEFT JOIN categories c ON s.category_id = c.id
                LEFT JOIN vendors v ON s.vendor_id = v.id
                LEFT JOIN users u ON v.user_id = u.id
                WHERE s.status = 'active'";

        $params = [];

        // Apply filters
        if (!empty($filters)) {
            if (isset($filters['category_id'])) {
                $sql .= " AND s.category_id = :category_id";
                $params[':category_id'] = $filters['category_id'];
            }

            if (isset($filters['vendor_id'])) {
                $sql .= " AND s.vendor_id = :vendor_id";
                $params[':vendor_id'] = $filters['vendor_id'];
            }

            if (isset($filters['search_term'])) {
                $searchTerm = '%' . $filters['search_term'] . '%';
                $sql .= " AND (s.{$titleField} LIKE :search_term OR s.{$descriptionField} LIKE :search_term)";
                $params[':search_term'] = $searchTerm;
            }

            if (isset($filters['min_price'])) {
                $sql .= " AND s.base_price >= :min_price";
                $params[':min_price'] = $filters['min_price'];
            }

            if (isset($filters['max_price'])) {
                $sql .= " AND s.base_price <= :max_price";
                $params[':max_price'] = $filters['max_price'];
            }
        }

        // Add sorting
        $sql .= " ORDER BY s.created_at DESC";

        // Add pagination
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params[':limit'] = $limit;
            $params[':offset'] = $offset;
        }

        $services = $this->db->fetchAll($sql, $params);

        // Load options for each service
        if (!empty($services)) {
            foreach ($services as &$service) {
                $service['options'] = $this->getServiceOptions($service['id'], $language);
                $service['media'] = $this->getServiceMedia($service['id']);
            }
        }

        return $services;
    }

    /**
     * Get services by category
     *
     * @param int $categoryId Category ID
     * @param string $language Current language
     * @param int $limit Limit results
     * @param int $offset Pagination offset
     * @return array Services array
     */
    public function getByCategoryId($categoryId, $language = 'en', $limit = 20, $offset = 0) {
        $filters = ['category_id' => $categoryId];
        return $this->getAll($language, $filters, $limit, $offset);
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
    public function getByVendorId($vendorId, $language = 'en', $limit = 20, $offset = 0) {
        $filters = ['vendor_id' => $vendorId];
        return $this->getAll($language, $filters, $limit, $offset);
    }

    /**
     * Search services
     *
     * @param string $searchTerm Search term
     * @param string $language Current language
     * @param int $limit Limit results
     * @param int $offset Pagination offset
     * @return array Services array
     */
    public function search($searchTerm, $language = 'en', $limit = 20, $offset = 0) {
        $filters = ['search_term' => $searchTerm];
        return $this->getAll($language, $filters, $limit, $offset);
    }

    /**
     * Get service by ID
     *
     * @param int $id Service ID
     * @param string $language Current language
     * @return array|false Service data or false if not found
     */
    public function getById($id, $language = 'en') {
        $titleField = "title_" . $language;
        $descriptionField = "description_" . $language;

        $sql = "SELECT s.*,
                s.{$titleField} as title,
                s.{$descriptionField} as description,
                c.{$titleField} as category_name,
                c.slug as category_slug,
                v.company_name_{$language} as vendor_name,
                v.description_{$language} as vendor_description,
                v.id as vendor_id,
                u.name as vendor_owner_name,
                (SELECT AVG(rating) FROM reviews WHERE vendor_id = v.id) as vendor_rating
                FROM {$this->table} s
                LEFT JOIN categories c ON s.category_id = c.id
                LEFT JOIN vendors v ON s.vendor_id = v.id
                LEFT JOIN users u ON v.user_id = u.id
                WHERE s.id = :id AND s.status = 'active'";

        $service = $this->db->fetch($sql, [':id' => $id]);

        if ($service) {
            // Load service options
            $service['options'] = $this->getServiceOptions($id, $language);

            // Load service media
            $service['media'] = $this->getServiceMedia($id);

            // Load related services from same category
            $relatedSql = "SELECT s.id,
                          s.{$titleField} as title,
                          s.base_price,
                          (SELECT file_path FROM {$this->mediaTable} WHERE service_id = s.id AND type = 'main' LIMIT 1) as main_image
                          FROM {$this->table} s
                          WHERE s.category_id = :category_id
                          AND s.id != :service_id
                          AND s.status = 'active'
                          ORDER BY s.created_at DESC
                          LIMIT 4";

            $service['related_services'] = $this->db->fetchAll($relatedSql, [
                ':category_id' => $service['category_id'],
                ':service_id' => $id
            ]);
        }

        return $service;
    }

    /**
     * Get service by slug
     *
     * @param string $slug Service slug
     * @param string $language Current language
     * @return array|false Service data or false if not found
     */
    public function getBySlug($slug, $language = 'en') {
        $sql = "SELECT id FROM {$this->table} WHERE slug = :slug AND status = 'active'";
        $result = $this->db->fetch($sql, [':slug' => $slug]);

        if ($result) {
            return $this->getById($result['id'], $language);
        }

        return false;
    }

    /**
     * Get featured services
     *
     * @param string $language Current language
     * @param int $limit Number of services to return
     * @return array Services array
     */
    public function getFeatured($language = 'en', $limit = 6) {
        $titleField = "title_" . $language;

        $sql = "SELECT s.id,
                s.{$titleField} as title,
                s.slug,
                s.base_price,
                c.{$titleField} as category_name,
                v.company_name_{$language} as vendor_name,
                (SELECT file_path FROM {$this->mediaTable} WHERE service_id = s.id AND type = 'main' LIMIT 1) as main_image
                FROM {$this->table} s
                LEFT JOIN categories c ON s.category_id = c.id
                LEFT JOIN vendors v ON s.vendor_id = v.id
                WHERE s.is_featured = 1 AND s.status = 'active'
                ORDER BY s.created_at DESC
                LIMIT :limit";

        return $this->db->fetchAll($sql, [':limit' => $limit]);
    }

    /**
     * Count services with optional filtering
     *
     * @param array $filters Optional filters
     * @return int Number of services
     */
    public function count($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE status = 'active'";
        $params = [];

        if (!empty($filters)) {
            if (isset($filters['category_id'])) {
                $sql .= " AND category_id = :category_id";
                $params[':category_id'] = $filters['category_id'];
            }

            if (isset($filters['vendor_id'])) {
                $sql .= " AND vendor_id = :vendor_id";
                $params[':vendor_id'] = $filters['vendor_id'];
            }
        }

        $result = $this->db->fetch($sql, $params);
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Create new service
     *
     * @param array $data Service data
     * @return int|false New service ID or false on failure
     */
    public function create($data) {
        // Generate slug from title_en
        $slug = $this->generateSlug($data['title_en']);

        $insertData = [
            'vendor_id' => $data['vendor_id'],
            'category_id' => $data['category_id'],
            'title_en' => $data['title_en'],
            'title_ar' => $data['title_ar'],
            'description_en' => $data['description_en'],
            'description_ar' => $data['description_ar'],
            'slug' => $slug,
            'base_price' => $data['base_price'],
            'min_order_qty' => $data['min_order_qty'] ?? 1,
            'production_time' => $data['production_time'] ?? 1,
            'status' => $data['status'] ?? 'active',
            'is_featured' => $data['is_featured'] ?? 0,
            'options_json' => isset($data['options']) ? json_encode($data['options']) : '{}',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $serviceId = $this->db->insert($this->table, $insertData);

        if ($serviceId && isset($data['media']) && is_array($data['media'])) {
            $this->saveServiceMedia($serviceId, $data['media']);
        }

        return $serviceId;
    }

    /**
     * Update service
     *
     * @param int $id Service ID
     * @param array $data Service data
     * @return bool Success flag
     */
    public function update($id, $data) {
        $updateData = [
            'category_id' => $data['category_id'] ?? null,
            'title_en' => $data['title_en'] ?? null,
            'title_ar' => $data['title_ar'] ?? null,
            'description_en' => $data['description_en'] ?? null,
            'description_ar' => $data['description_ar'] ?? null,
            'base_price' => $data['base_price'] ?? null,
            'min_order_qty' => $data['min_order_qty'] ?? null,
            'production_time' => $data['production_time'] ?? null,
            'status' => $data['status'] ?? null,
            'is_featured' => $data['is_featured'] ?? null,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Remove null values
        $updateData = array_filter($updateData, function($value) {
            return $value !== null;
        });

        // Update options if provided
        if (isset($data['options'])) {
            $updateData['options_json'] = json_encode($data['options']);
        }

        $result = $this->db->update($this->table, $updateData, "id = :id", [':id' => $id]);

        // Update media if provided
        if ($result && isset($data['media']) && is_array($data['media'])) {
            $this->saveServiceMedia($id, $data['media']);
        }

        return $result;
    }

    /**
     * Delete service
     *
     * @param int $id Service ID
     * @return bool Success flag
     */
    public function delete($id) {
        // First delete service media
        $this->db->delete($this->mediaTable, "service_id = :service_id", [':service_id' => $id]);

        // Then delete service
        return $this->db->delete($this->table, "id = :id", [':id' => $id]);
    }

    /**
     * Generate a unique slug from title
     *
     * @param string $title Service title
     * @return string Unique slug
     */
    private function generateSlug($title) {
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9-]+/', '-', $title), '-'));

        // Check if slug exists
        $count = 0;
        $originalSlug = $slug;

        while (true) {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE slug = :slug";
            $result = $this->db->fetch($sql, [':slug' => $slug]);

            if ($result['count'] == 0) {
                break;
            }

            $count++;
            $slug = $originalSlug . '-' . $count;
        }

        return $slug;
    }

    /**
     * Save service media files
     *
     * @param int $serviceId Service ID
     * @param array $media Media data
     * @return bool Success flag
     */
    private function saveServiceMedia($serviceId, $media) {
        // First delete existing media if updating
        $this->db->delete($this->mediaTable, "service_id = :service_id", [':service_id' => $serviceId]);

        // Then add new media
        foreach ($media as $item) {
            $mediaData = [
                'service_id' => $serviceId,
                'file_path' => $item['file_path'],
                'type' => $item['type'] ?? 'image',
                'sort_order' => $item['sort_order'] ?? 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert($this->mediaTable, $mediaData);
        }

        return true;
    }

    /**
     * Get service media
     *
     * @param int $serviceId Service ID
     * @return array Media files
     */
    private function getServiceMedia($serviceId) {
        $sql = "SELECT * FROM {$this->mediaTable} WHERE service_id = :service_id ORDER BY sort_order ASC";
        return $this->db->fetchAll($sql, [':service_id' => $serviceId]);
    }

    /**
     * Get service options
     *
     * @param int $serviceId Service ID
     * @param string $language Current language
     * @return array Service options
     */
    private function getServiceOptions($serviceId, $language = 'en') {
        $sql = "SELECT options_json FROM {$this->table} WHERE id = :id";
        $result = $this->db->fetch($sql, [':id' => $serviceId]);

        if ($result && !empty($result['options_json'])) {
            $options = json_decode($result['options_json'], true);

            // Process options for language
            if (is_array($options)) {
                foreach ($options as &$option) {
                    if (isset($option['name_en']) && isset($option['name_ar'])) {
                        $option['name'] = $option['name_' . $language];
                    }

                    if (isset($option['values']) && is_array($option['values'])) {
                        foreach ($option['values'] as &$value) {
                            if (isset($value['label_en']) && isset($value['label_ar'])) {
                                $value['label'] = $value['label_' . $language];
                            }
                        }
                    }
                }
            }

            return $options;
        }

        return [];
    }
}
