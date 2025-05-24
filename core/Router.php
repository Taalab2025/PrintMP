<?php
/**
 * Router - Request Routing System
 * Egypt Printing Services Marketplace
 */

class Router
{
    private $routes = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Default routes
        $this->addRoute('GET', '/', 'Home', 'index');
        $this->addRoute('GET', '/404', 'Error', 'notFound');
    }

    /**
     * Add a route
     */
    public function addRoute($method, $path, $controller, $action, $middleware = [])
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action,
            'middleware' => $middleware
        ];
    }

    /**
     * Add a GET route
     */
    public function get($path, $controller, $action, $middleware = [])
    {
        $this->addRoute('GET', $path, $controller, $action, $middleware);
    }

    /**
     * Add a POST route
     */
    public function post($path, $controller, $action, $middleware = [])
    {
        $this->addRoute('POST', $path, $controller, $action, $middleware);
    }

    /**
     * Add a PUT route
     */
    public function put($path, $controller, $action, $middleware = [])
    {
        $this->addRoute('PUT', $path, $controller, $action, $middleware);
    }

    /**
     * Add a DELETE route
     */
    public function delete($path, $controller, $action, $middleware = [])
    {
        $this->addRoute('DELETE', $path, $controller, $action, $middleware);
    }

    /**
     * Dispatch the request to the appropriate controller and action
     */
    public function dispatch()
    {
        // Get the request method and URI
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove base path if it exists
        $basePath = parse_url(BASE_URL, PHP_URL_PATH);
        if ($basePath && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        // Default values
        $uri = '/' . trim($uri, '/');

        // Match route
        $params = [];
        $matchedRoute = $this->matchRoute($method, $uri, $params);

        if ($matchedRoute) {
            // Get controller and action
            $controllerName = $matchedRoute['controller'];
            $actionName = $matchedRoute['action'];

            // Run middleware
            foreach ($matchedRoute['middleware'] as $middleware) {
                $middlewareFile = CORE_PATH . '/middleware/' . $middleware . '.php';
                if (file_exists($middlewareFile)) {
                    require_once $middlewareFile;
                    $middlewareInstance = new $middleware();
                    $result = $middlewareInstance->handle();

                    if (!$result) {
                        // Middleware rejected the request
                        return;
                    }
                }
            }

            // Get controller instance
            $controller = App::getInstance()->getController($controllerName);

            // Call the action
            call_user_func_array([$controller, $actionName], $params);
        } else {
            // Route not found
            header("HTTP/1.0 404 Not Found");
            $controller = App::getInstance()->getController('Error');
            $controller->notFound();
        }
    }

    /**
     * Match a route
     */
    private function matchRoute($method, $uri, &$params)
    {
        foreach ($this->routes as $route) {
            // Skip if method doesn't match
            if ($route['method'] !== $method) {
                continue;
            }

            // Convert route path to regex pattern
            $pattern = $this->convertRouteToRegex($route['path']);

            // Match the URI against the pattern
            if (preg_match($pattern, $uri, $matches)) {
                // Remove the full match
                array_shift($matches);

                // Assign matched parameters
                $params = $matches;

                return $route;
            }
        }

        return false;
    }

    /**
     * Convert a route path to a regex pattern
     */
    private function convertRouteToRegex($path)
    {
        // Replace {param} with a regex capture group
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $path);

        // Escape forward slashes and add start/end markers
        return '#^' . $pattern . '$#';
    }
}
