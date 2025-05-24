<?php
/**
 * HomeController - Handles home and general pages
 * Egypt Printing Services Marketplace
 */

class HomeController
{
    private $app;

    /**
     * Constructor
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Home page
     */
    public function index()
    {
        // Get categories for homepage
        $db = $this->app->getDB();
        $localization = $this->app->getLocalization();
        $lang = $localization->getLanguage();

        // Get featured categories
        $categories = $db->fetchAll(
            "SELECT id, name_$lang as name, image, slug
             FROM categories
             WHERE status = 'active' AND parent_id IS NULL
             ORDER BY display_order
             LIMIT 8"
        );

        // Get featured services
        $services = $db->fetchAll(
            "SELECT s.id, s.title_$lang as title, s.base_price, v.company_name_$lang as company_name,
                    (SELECT file_path FROM service_media WHERE service_id = s.id ORDER BY display_order LIMIT 1) as image
             FROM services s
             JOIN vendors v ON s.vendor_id = v.id
             WHERE s.status = 'active' AND s.is_featured = 1
             ORDER BY s.created_at DESC
             LIMIT 8"
        );

        // Get featured vendors
        $vendors = $db->fetchAll(
            "SELECT v.id, v.company_name_$lang as company_name, v.logo, v.avg_rating,
                    (SELECT COUNT(*) FROM services WHERE vendor_id = v.id AND status = 'active') as service_count
             FROM vendors v
             JOIN users u ON v.user_id = u.id
             WHERE u.status = 'active' AND v.subscription_status = 'paid'
             ORDER BY v.avg_rating DESC
             LIMIT 6"
        );

        // Render the view
        $data = [
            'categories' => $categories,
            'services' => $services,
            'vendors' => $vendors,
            'title' => $localization->t('welcome')
        ];

        echo $this->app->renderView('layouts/main', [
            'content' => $this->app->renderView('pages/home', $data),
            'title' => $localization->t('welcome')
        ]);
    }

    /**
     * About page
     */
    public function about()
    {
        $localization = $this->app->getLocalization();

        echo $this->app->renderView('layouts/main', [
            'content' => $this->app->renderView('pages/about'),
            'title' => $localization->t('about')
        ]);
    }

    /**
     * Contact page
     */
    public function contact()
    {
        $localization = $this->app->getLocalization();

        echo $this->app->renderView('layouts/main', [
            'content' => $this->app->renderView('pages/contact'),
            'title' => $localization->t('contact')
        ]);
    }

    /**
     * Submit contact form
     */
    public function submitContact()
    {
        $session = $this->app->getSession();
        $localization = $this->app->getLocalization();

        // Get form data
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

        // Validate form data
        if (empty($name) || empty($email) || empty($message)) {
            $session->setFlash('error', 'All fields are required');
            header('Location: /contact');
            exit;
        }

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $session->setFlash('error', 'Invalid email address');
            header('Location: /contact');
            exit;
        }

        // TODO: Send email

        // Set success message
        $session->setFlash('success', 'Your message has been sent successfully');

        // Redirect back to contact page
        header('Location: /contact');
        exit;
    }

    /**
     * Set language
     */
    public function setLanguage($lang)
    {
        $localization = $this->app->getLocalization();
        $session = $this->app->getSession();

        // Set language
        if ($localization->setLanguage($lang)) {
            $session->setFlash('success', 'Language changed successfully');
        } else {
            $session->setFlash('error', 'Invalid language');
        }

        // Redirect back to previous page
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL;
        header('Location: ' . $referer);
        exit;
    }
}
