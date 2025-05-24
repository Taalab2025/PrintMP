<?php
/**
 * ErrorController - Handles error pages and responses
 * File path: controllers/ErrorController.php
 */

class ErrorController
{
    private $app;
    private $localization;

    public function __construct($app)
    {
        $this->app = $app;
        $this->localization = $app->getLocalization();
    }

    /**
     * Display 404 Not Found page
     */
    public function notFound()
    {
        http_response_code(404);
        $this->renderError('404');
    }

    /**
     * Display 500 Internal Server Error page
     */
    public function serverError($error = null)
    {
        http_response_code(500);

        // Log the error if provided
        if ($error) {
            $this->logError('500', $error);
        }

        $this->renderError('500');
    }

    /**
     * Display 403 Forbidden page
     */
    public function forbidden()
    {
        http_response_code(403);
        $this->renderError('403');
    }

    /**
     * Display 503 Service Unavailable page (maintenance mode)
     */
    public function maintenance()
    {
        http_response_code(503);
        $this->renderError('maintenance');
    }

    /**
     * Handle AJAX error responses
     */
    public function ajaxError($code, $message = null)
    {
        http_response_code($code);

        header('Content-Type: application/json');

        $response = [
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message ?: $this->getDefaultErrorMessage($code)
            ]
        ];

        echo json_encode($response);
        exit;
    }

    /**
     * Handle API error responses
     */
    public function apiError($code, $message = null, $details = null)
    {
        http_response_code($code);

        header('Content-Type: application/json');

        $response = [
            'error' => [
                'code' => $code,
                'message' => $message ?: $this->getDefaultErrorMessage($code),
                'timestamp' => date('c')
            ]
        ];

        if ($details) {
            $response['error']['details'] = $details;
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Render error page
     */
    private function renderError($errorType)
    {
        $errorFile = "views/errors/{$errorType}.php";

        if (file_exists($errorFile)) {
            include $errorFile;
        } else {
            // Fallback to basic error page
            $this->renderBasicError($errorType);
        }

        exit;
    }

    /**
     * Render basic error page as fallback
     */
    private function renderBasicError($errorType)
    {
        $errorMessages = [
            '404' => 'Page Not Found',
            '403' => 'Access Forbidden',
            '500' => 'Internal Server Error',
            'maintenance' => 'Service Unavailable'
        ];

        $title = $errorMessages[$errorType] ?? 'Error';
        $isRtl = $this->localization->isRtl();

        echo "<!DOCTYPE html>
<html lang=\"{$this->localization->getCurrentLanguage()}\" dir=\"" . ($isRtl ? 'rtl' : 'ltr') . "\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{$title}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            color: #374151;
            text-align: " . ($isRtl ? 'right' : 'left') . ";
            direction: " . ($isRtl ? 'rtl' : 'ltr') . ";
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            font-size: 4rem;
            margin: 0;
            color: #ef4444;
        }
        h2 {
            font-size: 1.5rem;
            margin: 20px 0;
            color: #374151;
        }
        p {
            margin: 20px 0;
            color: #6b7280;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
        }
        .btn:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
    <div class=\"container\">
        <h1>" . strtoupper($errorType) . "</h1>
        <h2>{$title}</h2>
        <p>We apologize for the inconvenience. Please try again later.</p>
        <a href=\"/\" class=\"btn\">Go Home</a>
        <a href=\"javascript:history.back()\" class=\"btn\">Go Back</a>
    </div>
</body>
</html>";
    }

    /**
     * Log error details
     */
    private function logError($type, $error)
    {
        $logFile = 'logs/errors.log';
        $logDir = dirname($logFile);

        // Create logs directory if it doesn't exist
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';

        $logMessage = "[{$timestamp}] {$type} Error\n";
        $logMessage .= "URI: {$requestUri}\n";
        $logMessage .= "IP: {$remoteAddr}\n";
        $logMessage .= "User Agent: {$userAgent}\n";

        if ($error instanceof Exception) {
            $logMessage .= "Exception: " . get_class($error) . "\n";
            $logMessage .= "Message: " . $error->getMessage() . "\n";
            $logMessage .= "File: " . $error->getFile() . ":" . $error->getLine() . "\n";
            $logMessage .= "Stack Trace:\n" . $error->getTraceAsString() . "\n";
        } elseif (is_string($error)) {
            $logMessage .= "Error: {$error}\n";
        }

        $logMessage .= str_repeat('-', 80) . "\n";

        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }

    /**
     * Get default error message for HTTP status code
     */
    private function getDefaultErrorMessage($code)
    {
        $messages = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            422 => 'Unprocessable Entity',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout'
        ];

        return $messages[$code] ?? 'Unknown Error';
    }

    /**
     * Check if request expects JSON response
     */
    private function expectsJson()
    {
        $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? '';
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        return $isAjax ||
               strpos($acceptHeader, 'application/json') !== false ||
               strpos($contentType, 'application/json') !== false;
    }

    /**
     * Handle uncaught exceptions
     */
    public function handleException($exception)
    {
        // Log the exception
        $this->logError('EXCEPTION', $exception);

        // Return appropriate response based on request type
        if ($this->expectsJson()) {
            $this->ajaxError(500, 'An unexpected error occurred');
        } else {
            $this->serverError($exception);
        }
    }

    /**
     * Handle PHP errors
     */
    public function handleError($errno, $errstr, $errfile, $errline)
    {
        // Don't handle suppressed errors
        if (!(error_reporting() & $errno)) {
            return false;
        }

        $errorTypes = [
            E_ERROR => 'Fatal Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict Standards',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated'
        ];

        $errorType = $errorTypes[$errno] ?? 'Unknown Error';
        $errorMessage = "{$errorType}: {$errstr} in {$errfile} on line {$errline}";

        // Log error
        $this->logError('PHP_ERROR', $errorMessage);

        // For fatal errors, display error page
        if (in_array($errno, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
            if ($this->expectsJson()) {
                $this->ajaxError(500, 'A system error occurred');
            } else {
                $this->serverError($errorMessage);
            }
        }

        return true;
    }

    /**
     * Set up error handlers
     */
    public static function register($app)
    {
        $errorController = new self($app);

        // Set exception handler
        set_exception_handler([$errorController, 'handleException']);

        // Set error handler
        set_error_handler([$errorController, 'handleError']);

        return $errorController;
    }
}
?>
