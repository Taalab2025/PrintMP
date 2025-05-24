<?php
/**
 * App - Main Application Class
 * Egypt Printing Services Marketplace
 */

class App
{
    private static $instance = null;
    private $router;
    private $db;
    private $session;
    private $auth;
    private $localization;

    /**
     * Private constructor to prevent instantiation
     */
    private function __construct()
    {
        // Initialize components
        $this->initializeSession();
        $this->initializeDatabase();
        $this->initializeLocalization();
        $this->initializeAuth();
        $this->initializeRouter();
    }

    /**
     * Get the application instance (Singleton)
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialize the session
     */
    private function initializeSession()
    {
        require_once CORE_PATH . '/Session.php';
        $this->session = new Session();
    }

    /**
     * Initialize the database connection
     */
    private function initializeDatabase()
    {
        require_once CORE_PATH . '/Database.php';
        $this->db = new Database();
    }

    /**
     * Initialize the localization system
     */
    private function initializeLocalization()
    {
        require_once CORE_PATH . '/Localization.php';
        $this->localization = new Localization($this->session);
    }

    /**
     * Initialize the authentication system
     */
    private function initializeAuth()
    {
        require_once CORE_PATH . '/Auth.php';
        $this->auth = new Auth($this->db, $this->session);
    }

    /**
     * Initialize the router
     */
    private function initializeRouter()
    {
        require_once CORE_PATH . '/Router.php';
        $this->router = new Router();
    }

    /**
     * Run the application
     */
    public function run()
    {
        // Set default timezone
        date_default_timezone_set(DEFAULT_TIMEZONE);

        // Load routes
        require_once CONFIG_PATH . '/routes.php';

        // Dispatch the request
        $this->router->dispatch();
    }

    /**
     * Get the database connection
     */
    public function getDB()
    {
        return $this->db;
    }

    /**
     * Get the session manager
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Get the authentication system
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * Get the localization system
     */
    public function getLocalization()
    {
        return $this->localization;
    }

    /**
     * Get the router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Helper to get a model instance
     */
    public function getModel($modelName)
    {
        $modelFile = MODELS_PATH . '/' . $modelName . '.php';

        if (!file_exists($modelFile)) {
            throw new Exception("Model not found: $modelName");
        }

        require_once $modelFile;
        return new $modelName($this->db);
    }

    /**
     * Helper to get a controller instance
     */
    public function getController($controllerName)
    {
        $controllerFile = CONTROLLERS_PATH . '/' . $controllerName . 'Controller.php';
        $controllerClass = $controllerName . 'Controller';

        if (!file_exists($controllerFile)) {
            throw new Exception("Controller not found: $controllerName");
        }

        require_once $controllerFile;
        return new $controllerClass($this);
    }

    /**
     * Render a view
     */
    public function renderView($view, $data = [])
    {
        $viewFile = VIEWS_PATH . '/' . $view . '.php';

        if (!file_exists($viewFile)) {
            throw new Exception("View not found: $view");
        }

        // Extract data for view
        extract($data);

        // Start output buffering
        ob_start();

        // Include the view
        include $viewFile;

        // Return the output
        return ob_get_clean();
    }
}
