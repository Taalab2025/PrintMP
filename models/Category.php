<?php
/**
 * Category Model - Service Categories Management
 * File path: models/Category.php
 * Session: 4 - Service Catalog & Browsing
 */

class Category
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Get all categories with multilingual support
     */
    public function getAll($language = 'en', $parentId = null)
    {
        $sql = "SELECT
                    id,
                    name_en,
                    name_ar,
                    slug_en,
                    slug_ar,
                    description_en,
                    description_ar,
                    icon,
                    parent_id,
                    sort_order,
                    status,
                    created_at
                FROM categories
                WHERE status = 'active'";

        $params = [];

        if ($parentId !== null) {
            $sql .= " AND parent_id = ?";
            $params[] = $parentId;
        } else {
            $sql .= " AND parent_id IS NULL";
        }

        $sql .= " ORDER BY sort_order ASC, name_{$language} ASC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get category by ID with multilingual support
     */
    public function getById($id, $language = 'en')
    {
        $sql = "SELECT
                    id,
                    name_en,
                    name_ar,
                    slug_en,
                    slug_ar,
                    description_en,
                    description_ar,
                    icon,
                    parent_id,
                    sort_order,
                    status,
                    created_at
                FROM categories
                WHERE id = ? AND status = 'active'";

        return $this->db->fetch($sql, [$id]);
    }

    /**
     * Get category by slug
     */
    public function getBySlug($slug, $language = 'en')
    {
        $slugColumn = "slug_{$language}";

        $sql = "SELECT
                    id,
                    name_en,
                    name_ar,
                    slug_en,
                    slug_ar,
                    description_en,
                    description_ar,
                    icon,
                    parent_id,
                    sort_order,
                    status,
                    created_at
                FROM categories
                WHERE {$slugColumn} = ? AND status = 'active'";

        return $this->db->fetch($sql, [$slug]);
    }

    /**
     * Get category tree structure
     */
    public function getCategoryTree($language = 'en')
    {
        // Get all categories
        $allCategories = $this->getAll($language);

        // Build tree structure
        $tree = [];
        $indexed = [];

        // Index categories by ID
        foreach ($allCategories as $category) {
            $category['children'] = [];
            $indexed[$category['id']] = $category;
        }

        // Build tree
        foreach ($indexed as $category) {
            if ($category['parent_id'] === null) {
                $tree[] = $category;
            } else {
                if (isset($indexed[$category['parent_id']])) {
                    $indexed[$category['parent_id']]['children'][] = $category;
                }
            }
        }

        return $tree;
    }

    /**
     * Get subcategories for a parent category
     */
    public function getSubcategories($parentId, $language = 'en')
    {
        return $this->getAll($language, $parentId);
    }

    /**
     * Get category with service count
     */
    public function getWithServiceCount($language = 'en')
    {
        $sql = "SELECT
                    c.id,
                    c.name_en,
                    c.name_ar,
                    c.slug_en,
                    c.slug_ar,
                    c.description_en,
                    c.description_ar,
                    c.icon,
                    c.parent_id,
                    c.sort_order,
                    c.status,
                    c.created_at,
                    COUNT(s.id) as service_count
                FROM categories c
                LEFT JOIN services s ON c.id = s.category_id AND s.status = 'active'
                WHERE c.status = 'active'
                GROUP BY c.id
                ORDER BY c.sort_order ASC, c.name_{$language} ASC";

        return $this->db->fetchAll($sql);
    }

    /**
     * Get popular categories (with most services)
     */
    public function getPopular($limit = 6, $language = 'en')
    {
        $sql = "SELECT
                    c.id,
                    c.name_en,
                    c.name_ar,
                    c.slug_en,
                    c.slug_ar,
                    c.description_en,
                    c.description_ar,
                    c.icon,
                    c.parent_id,
                    COUNT(s.id) as service_count
                FROM categories c
                LEFT JOIN services s ON c.id = s.category_id AND s.status = 'active'
                WHERE c.status = 'active' AND c.parent_id IS NULL
                GROUP BY c.id
                HAVING service_count > 0
                ORDER BY service_count DESC, c.name_{$language} ASC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Search categories
     */
    public function search($searchTerm, $language = 'en', $limit = 20)
    {
        $nameColumn = "name_{$language}";
        $descColumn = "description_{$language}";

        $sql = "SELECT
                    id,
                    name_en,
                    name_ar,
                    slug_en,
                    slug_ar,
                    description_en,
                    description_ar,
                    icon,
                    parent_id,
                    sort_order,
                    status,
                    created_at
                FROM categories
                WHERE status = 'active'
                AND ({$nameColumn} LIKE ? OR {$descColumn} LIKE ?)
                ORDER BY {$nameColumn} ASC
                LIMIT ?";

        $searchPattern = "%{$searchTerm}%";
        return $this->db->fetchAll($sql, [$searchPattern, $searchPattern, $limit]);
    }

    /**
     * Create new category (Admin function)
     */
    public function create($data)
    {
        $sql = "INSERT INTO categories (
                    name_en, name_ar, slug_en, slug_ar,
                    description_en, description_ar, icon,
                    parent_id, sort_order, status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $params = [
            $data['name_en'],
            $data['name_ar'],
            $data['slug_en'],
            $data['slug_ar'],
            $data['description_en'] ?? null,
            $data['description_ar'] ?? null,
            $data['icon'] ?? null,
            $data['parent_id'] ?? null,
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active'
        ];

        return $this->db->insert($sql, $params);
    }

    /**
     * Update category (Admin function)
     */
    public function update($id, $data)
    {
        $sql = "UPDATE categories SET
                    name_en = ?, name_ar = ?, slug_en = ?, slug_ar = ?,
                    description_en = ?, description_ar = ?, icon = ?,
                    parent_id = ?, sort_order = ?, status = ?,
                    updated_at = NOW()
                WHERE id = ?";

        $params = [
            $data['name_en'],
            $data['name_ar'],
            $data['slug_en'],
            $data['slug_ar'],
            $data['description_en'] ?? null,
            $data['description_ar'] ?? null,
            $data['icon'] ?? null,
            $data['parent_id'] ?? null,
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active',
            $id
        ];

        return $this->db->execute($sql, $params);
    }

    /**
     * Delete category (Admin function)
     */
    public function delete($id)
    {
        // Check if category has subcategories
        $subcategories = $this->getSubcategories($id);
        if (!empty($subcategories)) {
            return false; // Cannot delete category with subcategories
        }

        // Check if category has services
        $sql = "SELECT COUNT(*) as count FROM services WHERE category_id = ?";
        $result = $this->db->fetch($sql, [$id]);

        if ($result['count'] > 0) {
            return false; // Cannot delete category with services
        }

        // Safe to delete
        $sql = "DELETE FROM categories WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    /**
     * Update category status (Admin function)
     */
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE categories SET status = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$status, $id]);
    }

    /**
     * Get category breadcrumb
     */
    public function getBreadcrumb($categoryId, $language = 'en')
    {
        $breadcrumb = [];
        $currentId = $categoryId;

        while ($currentId) {
            $category = $this->getById($currentId, $language);
            if (!$category) break;

            array_unshift($breadcrumb, $category);
            $currentId = $category['parent_id'];
        }

        return $breadcrumb;
    }

    /**
     * Get total category count (Admin function)
     */
    public function getTotalCount()
    {
        $sql = "SELECT COUNT(*) as count FROM categories";
        $result = $this->db->fetch($sql);
        return $result['count'];
    }

    /**
     * Generate unique slug
     */
    public function generateSlug($name, $language = 'en', $id = null)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $originalSlug = $slug;
        $counter = 1;

        $slugColumn = "slug_{$language}";

        while (true) {
            $sql = "SELECT id FROM categories WHERE {$slugColumn} = ?";
            $params = [$slug];

            if ($id) {
                $sql .= " AND id != ?";
                $params[] = $id;
            }

            $existing = $this->db->fetch($sql, $params);

            if (!$existing) {
                break;
            }

            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
?>
