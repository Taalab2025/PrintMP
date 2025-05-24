<?php
/**
 * Search Controller
 *
 * File path: controllers/SearchController.php
 *
 * Handles search functionality across services and vendors
 */

class SearchController {
    private $db;
    private $localization;
    private $service;
    private $vendor;
    private $category;
    private $session;

    /**
     * Constructor
     *
     * @param Database $db Database instance
     * @param Localization $localization Localization instance
     * @param Session $session Session instance
     */
    public function __construct($db, $localization, $session) {
        $this->db = $db;
        $this->localization = $localization;
        $this->session = $session;

        // Load models
        $this->service = new Service($db);
        $this->vendor = new Vendor($db);
        $this->category = new Category($db);
    }

    /**
     * Global search
     *
     * @return void
     */
    public function search() {
        $searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
        $searchType = isset($_GET['type']) ? $_GET['type'] : 'all';

        if (empty($searchTerm)) {
            header('Location: /');
            exit;
        }

        $language = $this->localization->getCurrentLanguage();
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12; // Results per page
        $offset = ($currentPage - 1) * $limit;

        // Get results based on search type
        switch ($searchType) {
            case 'services':
                $filters = ['search_term' => $searchTerm];
                $results = $this->service->getAll($language, $filters, $limit, $offset);
                $totalResults = $this->service->count($filters);
                $resultType = 'services';
                break;

            case 'vendors':
                $filters = ['search_term' => $searchTerm];
                $results = $this->vendor->getAll($language, $filters, $limit, $offset);
                $totalResults = $this->vendor->count($filters);
                $resultType = 'vendors';
                break;

            case 'all':
            default:
                // Get both services and vendors, then combine
                $serviceFilters = ['search_term' => $searchTerm];
                $vendorFilters = ['search_term' => $searchTerm];

                $services = $this->service->getAll($language, $serviceFilters, $limit / 2, 0);
                $vendors = $this->vendor->getAll($language, $vendorFilters, $limit / 2, 0);

                $totalServices = $this->service->count($serviceFilters);
                $totalVendors = $this->vendor->count($vendorFilters);

                $results = [
                    'services' => $services,
                    'vendors' => $vendors
                ];

                $totalResults = $totalServices + $totalVendors;
                $resultType = 'all';
                break;
        }

        $totalPages = ceil($totalResults / $limit);

        // Set page title based on search term
        $title = $this->localization->t('search.results_for', ['term' => $searchTerm]);

        // Set variables for the view
        $viewData = [
            'searchTerm' => $searchTerm,
            'searchType' => $searchType,
            'results' => $results,
            'resultType' => $resultType,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalResults' => $totalResults,
            'title' => $title
        ];

        // Load the view
        require 'views/layouts/main.php';
        require 'views/pages/search/results.php';
    }

    /**
     * Ajax search suggestions
     *
     * @return void
     */
    public function suggestions() {
        $searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

        if (empty($searchTerm) || strlen($searchTerm) < 2) {
            echo json_encode(['suggestions' => []]);
            exit;
        }

        $language = $this->localization->getCurrentLanguage();

        // Get top services matching the search term
        $serviceFilters = ['search_term' => $searchTerm];
        $services = $this->service->getAll($language, $serviceFilters, 5, 0);

        // Get top vendors matching the search term
        $vendorFilters = ['search_term' => $searchTerm];
        $vendors = $this->vendor->getAll($language, $vendorFilters, 3, 0);

        // Get top categories matching the search term
        $categoryFilters = ['search_term' => $searchTerm];
        $categories = $this->category->getAll($language, $categoryFilters, 3, 0);

        // Format suggestions
        $suggestions = [];

        // Add services
        foreach ($services as $service) {
            $suggestions[] = [
                'id' => $service['id'],
                'title' => $service['title'],
                'url' => '/services/' . $service['slug'],
                'type' => 'service',
                'image' => !empty($service['main_image']) ? $service['main_image'] : null,
                'price' => $service['base_price'],
                'category' => isset($service['category_name']) ? $service['category_name'] : null
            ];
        }

        // Add vendors
        foreach ($vendors as $vendor) {
            $suggestions[] = [
                'id' => $vendor['id'],
                'title' => $vendor['company_name'],
                'url' => '/vendors/' . $vendor['id'],
                'type' => 'vendor',
                'image' => !empty($vendor['logo']) ? $vendor['logo'] : null,
                'rating' => isset($vendor['rating']) ? $vendor['rating'] : null,
                'service_count' => isset($vendor['service_count']) ? $vendor['service_count'] : 0
            ];
        }

        // Add categories
        foreach ($categories as $category) {
            $suggestions[] = [
                'id' => $category['id'],
                'title' => $category['name'],
                'url' => '/services?category=' . $category['slug'],
                'type' => 'category',
                'icon' => !empty($category['icon']) ? $category['icon'] : null
            ];
        }

        // Return suggestions as JSON
        header('Content-Type: application/json');
        echo json_encode([
            'suggestions' => $suggestions,
            'totalResults' => [
                'services' => $this->service->count($serviceFilters),
                'vendors' => $this->vendor->count($vendorFilters)
            ],
            'query' => $searchTerm
        ]);
        exit;
    }
}
