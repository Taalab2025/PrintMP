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
        $this->localization = new Localization(); // Correct: No arguments needed as it reads from cookie/browser
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
     * @param string $modelName Name of the model class
     * @return object Instance of the model
     * @throws Exception if model file not found
     */
    public function getModel($modelName)
    {
        $modelFile = MODELS_PATH . '/' . $modelName . '.php';

        if (!file_exists($modelFile)) {
            // Log this error or handle more gracefully in production
            throw new Exception("Model not found: $modelName at $modelFile");
        }

        require_once $modelFile;
        // Ensure class name matches model name
        if (!class_exists($modelName)) {
             throw new Exception("Model class not found: $modelName in $modelFile");
        }
        return new $modelName($this->db);
    }

    /**
     * Helper to get a controller instance
     * @param string $controllerName Name of the controller class (without 'Controller' suffix)
     * @return object Instance of the controller
     * @throws Exception if controller file or class not found
     */
    public function getController($controllerName)
    {
        $controllerFile = CONTROLLERS_PATH . '/' . $controllerName . 'Controller.php';
        $controllerClass = $controllerName . 'Controller';

        if (!file_exists($controllerFile)) {
            // Log this error or handle more gracefully in production
            throw new Exception("Controller file not found: $controllerName at $controllerFile");
        }

        require_once $controllerFile;
        if (!class_exists($controllerClass)) {
            throw new Exception("Controller class not found: $controllerClass in $controllerFile");
        }
        return new $controllerClass($this); // Pass the App instance
    }

    /**
     * Render a view
     * @param string $view The path to the view file relative to VIEWS_PATH (e.g., 'pages/home')
     * @param array $data Data to be extracted and made available to the view
     * @return string The rendered HTML content
     * @throws Exception if view file not found
     */
    public function renderView($view, $data = [])
    {
        $viewFile = VIEWS_PATH . '/' . $view . '.php';

        if (!file_exists($viewFile)) {
            // In a production environment, you might want to show a generic error page
            // or log this and show a user-friendly message.
            // For debugging, an exception is clear.
            // If ErrorController is robust and doesn't itself call renderView in a loop:
            // $errorController = $this->getController('Error');
            // $errorController->notFound("View file not found: " . $viewFile);
            // return ''; // Or exit if ErrorController handles output and exit
            throw new Exception("View not found: $viewFile (looking for view: $view)");
        }

        // Make the App instance available as $app in the view's scope
        $data['app'] = $this;

        // Extract data to make keys available as variables in the view
        extract($data);

        // Start output buffering
        ob_start();

        // Include the view file
        // All variables from extract($data) and $app are now in scope for $viewFile
        include $viewFile;

        // Get the content and clean the buffer
        $content = ob_get_clean();

        return $content;
    }
}
