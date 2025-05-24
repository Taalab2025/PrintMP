<?php
/**
 * Session - Session Management
 * Egypt Printing Services Marketplace
 */

class Session
{
    /**
     * Constructor - Initialize session
     */
    public function __construct()
    {
        // Set session name
        session_name(SESSION_NAME);

        // Set session cookie parameters
        session_set_cookie_params(
            SESSION_LIFETIME,
            SESSION_PATH,
            SESSION_DOMAIN,
            SESSION_SECURE,
            SESSION_HTTP_ONLY
        );

        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Initialize flash messages
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }
    }

    /**
     * Set a session variable
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session variable
     */
    public function get($key, $default = null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * Check if a session variable exists
     */
    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session variable
     */
    public function remove($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
            return true;
        }

        return false;
    }

    /**
     * Clear all session variables
     */
    public function clear()
    {
        session_unset();
    }

    /**
     * Destroy the session
     */
    public function destroy()
    {
        // Clear session data
        $this->clear();

        // Destroy session
        session_destroy();

        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
    }

    /**
     * Regenerate session ID
     */
    public function regenerate($deleteOldSession = true)
    {
        return session_regenerate_id($deleteOldSession);
    }

    /**
     * Set a flash message (available only for the next request)
     */
    public function setFlash($key, $value)
    {
        $_SESSION['flash_messages'][$key] = $value;
    }

    /**
     * Get a flash message and remove it
     */
    public function getFlash($key, $default = null)
    {
        if (isset($_SESSION['flash_messages'][$key])) {
            $value = $_SESSION['flash_messages'][$key];
            unset($_SESSION['flash_messages'][$key]);
            return $value;
        }

        return $default;
    }

    /**
     * Check if a flash message exists
     */
    public function hasFlash($key)
    {
        return isset($_SESSION['flash_messages'][$key]);
    }

    /**
     * Get all flash messages and remove them
     */
    public function getAllFlash()
    {
        $flash = $_SESSION['flash_messages'];
        $_SESSION['flash_messages'] = [];
        return $flash;
    }

    /**
     * Set CSRF token
     */
    public function setCsrfToken()
    {
        $token = bin2hex(random_bytes(32));
        $this->set('csrf_token', $token);
        return $token;
    }

    /**
     * Get CSRF token
     */
    public function getCsrfToken()
    {
        if (!$this->has('csrf_token')) {
            return $this->setCsrfToken();
        }

        return $this->get('csrf_token');
    }

    /**
     * Validate CSRF token
     */
    public function validateCsrfToken($token)
    {
        if (!$this->has('csrf_token')) {
            return false;
        }

        return hash_equals($this->get('csrf_token'), $token);
    }
}
