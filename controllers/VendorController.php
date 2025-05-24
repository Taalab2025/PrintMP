<?php
/**
 * Vendor Controller
 *
 * File path: controllers/VendorController.php
 *
 * Handles vendor profile display and listing
 */

class VendorController {
    private $db;
    private $localization;
    private $vendor;
    private $service;
    private $category;
    private $auth;
    private $session;

    /**
     * Constructor
     *
     * @param Database $db Database instance
     * @param Localization $localization Localization instance
     * @param Session $session Session instance
     * @param Auth $auth Auth instance
     */
    public function __construct($db, $localization, $session, $auth) {
        $this->db = $db;
        $this->localization = $localization;
        $this->session = $session;
        $this->auth = $auth;

        // Load models
        $this->vendor = new Vendor($db);
        $this->service = new Service($db);
        $this->category = new Category($db);
    }

    /**
     * Vendors index page
     *
     * @return void
     */
    public function index() {
        $language = $this->localization->getCurrentLanguage();
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12; // Vendors per page
        $offset = ($currentPage - 1) * $limit;

        // Get filter parameters
        $filters = [];

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $filters['search_term'] = $_GET['search'];
        }

        if (isset($_GET['location']) && !empty($_GET['location'])) {
            $filters['location'] = $_GET['location'];
        }

        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $category = $this->category->getBySlug($_GET['category'], $language);
            if ($category) {
                $filters['category_id'] = $category['id'];
            }
        }

        if (isset($_GET['min_rating']) && is_numeric($_GET['min_rating'])) {
            $filters['min_rating'] = (float)$_GET['min_rating'];
        }

        if (isset($_GET['sort']) && !empty($_GET['sort'])) {
            $filters['sort'] = $_GET['sort'];
        }

        // Get vendors with filters
        $vendors = $this->vendor->getAll($language, $filters, $limit, $offset);

        // Get total count for pagination
        $totalVendors = $this->vendor->count($filters);
        $totalPages = ceil($totalVendors / $limit);

        // Get all categories for filter sidebar
        $categories = $this->category->getAll($language);

        // Get filter descriptions for showing applied filters
        $filterDescriptions = $this->getFilterDescriptions($filters, $language);

        // Set variables for the view
        $viewData = [
            'vendors' => $vendors,
            'categories' => $categories,
            'filters' => $filters,
            'filterDescriptions' => $filterDescriptions,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalVendors' => $totalVendors,
            'title' => $this->localization->t('vendors.all_vendors')
        ];

        // If category filter is applied, use category name in title
        if (isset($filters['category_id']) && isset($category)) {
            $viewData['title'] = $this->localization->t('vendors.vendors_in_category', ['category' => $category['name']]);
            $viewData['currentCategory'] = $category;
        }

        // If location filter is applied, use location in title
        if (isset($filters['location'])) {
            $viewData['title'] = $this->localization->t('vendors.vendors_in_location', ['location' => $filters['location']]);
        }

        // If search filter is applied, use search term in title
        if (isset($filters['search_term'])) {
            $viewData['title'] = $this->localization->t('vendors.search_results', ['term' => $filters['search_term']]);
        }

        // Load the view
        require 'views/layouts/main.php';
        require 'views/pages/vendors/index.php';
    }

    /**
     * Show single vendor profile
     *
     * @param int $id Vendor ID
     * @return void
     */
    public function show($id) {
        $language = $this->localization->getCurrentLanguage();

        // Get vendor by ID
        $vendor = $this->vendor->getById($id, $language);

        if (!$vendor) {
            header('Location: /vendors');
            exit;
        }

        // Get vendor services
        $services = $this->vendor->getServices($id, $language, 6);

        // Get vendor reviews
        $reviews = $this->vendor->getReviews($id, 5);

        // Set page title
        $title = $vendor['company_name'];

        // Set variables for the view
        $viewData = [
            'vendor' => $vendor,
            'services' => $services,
            'reviews' => $reviews,
            'title' => $title
        ];

        // Load the view
        require 'views/layouts/main.php';
        require 'views/pages/vendors/profile.php';
    }

    /**
     * Search vendors
     *
     * @return void
     */
    public function search() {
        $searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

        if (empty($searchTerm)) {
            header('Location: /vendors');
            exit;
        }

        // Redirect to vendors index with search filter
        header('Location: /vendors?search=' . urlencode($searchTerm));
        exit;
    }

    /**
     * Get human-readable descriptions of applied filters
     *
     * @param array $filters Applied filters
     * @param string $language Current language
     * @return array Filter descriptions
     */
    private function getFilterDescriptions($filters, $language) {
        $descriptions = [];

        if (isset($filters['category_id'])) {
            $category = $this->category->getById($filters['category_id'], $language);
            if ($category) {
                $descriptions['category'] = [
                    'label' => $this->localization->t('vendors.category'),
                    'value' => $category['name'],
                    'param' => 'category',
                    'remove_url' => $this->removeFilterFromCurrentUrl('category')
                ];
            }
        }

        if (isset($filters['location'])) {
            $descriptions['location'] = [
                'label' => $this->localization->t('vendors.location'),
                'value' => $filters['location'],
                'param' => 'location',
                'remove_url' => $this->removeFilterFromCurrentUrl('location')
            ];
        }

        if (isset($filters['search_term'])) {
            $descriptions['search'] = [
                'label' => $this->localization->t('vendors.search'),
                'value' => $filters['search_term'],
                'param' => 'search',
                'remove_url' => $this->removeFilterFromCurrentUrl('search')
            ];
        }

        if (isset($filters['min_rating'])) {
            $descriptions['min_rating'] = [
                'label' => $this->localization->t('vendors.min_rating'),
                'value' => $filters['min_rating'] . ' ' . $this->localization->t('vendors.stars'),
                'param' => 'min_rating',
                'remove_url' => $this->removeFilterFromCurrentUrl('min_rating')
            ];
        }

        return $descriptions;
    }

    /**
     * Remove a filter from the current URL
     *
     * @param string|array $paramToRemove Parameter(s) to remove
     * @return string URL without the filter
     */
    private function removeFilterFromCurrentUrl($paramToRemove) {
        $params = $_GET;

        if (is_array($paramToRemove)) {
            foreach ($paramToRemove as $param) {
                if (isset($params[$param])) {
                    unset($params[$param]);
                }
            }
        } else {
            if (isset($params[$paramToRemove])) {
                unset($params[$paramToRemove]);
            }
        }

        // Keep page parameter only if it's not 1
        if (isset($params['page']) && $params['page'] == 1) {
            unset($params['page']);
        }

        $queryString = http_build_query($params);
        $baseUrl = strtok($_SERVER['REQUEST_URI'], '?');

        return $baseUrl . ($queryString ? '?' . $queryString : '');
    }
}
