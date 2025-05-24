<?php
/**
 * Authentication Controller
 * File path: controllers/AuthController.php
 *
 * Handles user authentication, registration, and password reset
 *
 * @package Egypt Printing Services Marketplace
 * @author  Development Team
 */

class AuthController {
    /**
     * @var Database Database instance
     */
    private $db;

    /**
     * @var Session Session instance
     */
    private $session;

    /**
     * @var Auth Auth instance
     */
    private $auth;

    /**
     * @var Localization Localization instance
     */
    private $localization;

    /**
     * Constructor
     *
     * @param Database $db Database instance
     * @param Session $session Session instance
     * @param Auth $auth Auth instance
     * @param Localization $localization Localization instance
     */
    public function __construct(Database $db, Session $session, Auth $auth, Localization $localization) {
        $this->db = $db;
        $this->session = $session;
        $this->auth = $auth;
        $this->localization = $localization;
    }

    /**
     * Show login form
     */
    public function showLogin() {
        // If user is already logged in, redirect to dashboard
        if ($this->auth->check()) {
            header('Location: /');
            exit;
        }

        // Get flash messages for showing errors/success
        $error = $this->session->getFlash('error');
        $success = $this->session->getFlash('success');

        // Render login view
        include 'views/pages/auth/login.php';
    }

    /**
     * Process login form
     */
    public function login() {
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        // Validate form data
        $validator = new Validator($_POST, $this->localization);
        if (!$validator->validate([
            'email' => 'required|email',
            'password' => 'required'
        ])) {
            $this->session->setFlash('error', $validator->getErrors()['email'] ?? $validator->getErrors()['password'] ?? $this->localization->t('auth.invalid_credentials'));
            header('Location: /login');
            exit;
        }

        // Attempt login
        $email = $_POST['email'];
        $password = $_POST['password'];
        $remember = isset($_POST['remember']) ? true : false;

        if ($this->auth->login($email, $password, $remember)) {
            // Redirect based on user role
            $user = $this->auth->user();

            if ($user['role'] === 'admin') {
                header('Location: /admin');
            } elseif ($user['role'] === 'vendor') {
                header('Location: /vendor');
            } else {
                // Check if there was a previous page before login
                if ($this->session->get('redirect_after_login')) {
                    $redirect = $this->session->get('redirect_after_login');
                    $this->session->remove('redirect_after_login');
                    header("Location: $redirect");
                } else {
                    header('Location: /');
                }
            }
            exit;
        }

        // Login failed
        $this->session->setFlash('error', $this->localization->t('auth.invalid_credentials'));
        header('Location: /login');
        exit;
    }

    /**
     * Logout user
     */
    public function logout() {
        $this->auth->logout();
        $this->session->setFlash('success', $this->localization->t('auth.logout_success'));
        header('Location: /login');
        exit;
    }

    /**
     * Show registration form
     */
    public function showRegister() {
        // If user is already logged in, redirect to dashboard
        if ($this->auth->check()) {
            header('Location: /');
            exit;
        }

        // Get flash messages for showing errors/success
        $error = $this->session->getFlash('error');
        $success = $this->session->getFlash('success');

        // Render register view
        include 'views/pages/auth/register.php';
    }

    /**
     * Process registration form
     */
    public function register() {
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /register');
            exit;
        }

        // Validate form data
        $validator = new Validator($_POST, $this->localization);
        if (!$validator->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'password_confirm' => 'required|matches:password',
            'role' => 'required'
        ])) {
            $this->session->setFlash('error', reset($validator->getErrors()));
            $this->session->setFlash('form_data', $_POST);
            header('Location: /register');
            exit;
        }

        // Check if email already exists
        $user = new User($this->db);
        if ($user->getByEmail($_POST['email'])) {
            $this->session->setFlash('error', $this->localization->t('auth.email_taken'));
            $this->session->setFlash('form_data', $_POST);
            header('Location: /register');
            exit;
        }

        // Create user
        $userData = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'role' => $_POST['role'],
            'language' => $this->localization->getLanguage(),
            'require_email_verification' => true
        ];

        $userId = $user->create($userData);

        if (!$userId) {
            $this->session->setFlash('error', $this->localization->t('auth.registration_failed'));
            $this->session->setFlash('form_data', $_POST);
            header('Location: /register');
            exit;
        }

        // If user is registering as vendor, create vendor profile
        if ($_POST['role'] === 'vendor') {
            $vendor = new Vendor($this->db);
            $vendorData = [
                'user_id' => $userId,
                'company_name_en' => $_POST['company_name'] ?? $_POST['name'],
                'company_name_ar' => $_POST['company_name_ar'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'address' => $_POST['address'] ?? '',
                'description_en' => '',
                'description_ar' => '',
                'status' => 'pending', // Vendors require approval
            ];

            $vendor->create($vendorData);
        }

        // Send verification email
        // This would be implemented in a real-world application
        // For now, we'll just simulate it
        $verificationToken = $user->getByEmail($_POST['email'])->getData()['verification_token'];
        $verificationUrl = "http://localhost/verify-email?token=$verificationToken";

        // Set success message and redirect
        $this->session->setFlash('success', $this->localization->t('auth.registration_success'));
        header('Location: /login');
        exit;
    }

    /**
     * Verify email
     */
    public function verifyEmail() {
        // Check if token is provided
        if (!isset($_GET['token']) || empty($_GET['token'])) {
            $this->session->setFlash('error', $this->localization->t('auth.invalid_token'));
            header('Location: /login');
            exit;
        }

        // Verify email
        $user = new User($this->db);
        if ($user->verifyEmail($_GET['token'])) {
            $this->session->setFlash('success', $this->localization->t('auth.email_verified'));
        } else {
            $this->session->setFlash('error', $this->localization->t('auth.invalid_token'));
        }

        header('Location: /login');
        exit;
    }

    /**
     * Show password reset request form
     */
    public function showForgotPassword() {
        // If user is already logged in, redirect to dashboard
        if ($this->auth->check()) {
            header('Location: /');
            exit;
        }

        // Get flash messages for showing errors/success
        $error = $this->session->getFlash('error');
        $success = $this->session->getFlash('success');

        // Render forgot password view
        include 'views/pages/auth/forgot-password.php';
    }

    /**
     * Process password reset request
     */
    public function forgotPassword() {
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /forgot-password');
            exit;
        }

        // Validate form data
        $validator = new Validator($_POST, $this->localization);
        if (!$validator->validate([
            'email' => 'required|email'
        ])) {
            $this->session->setFlash('error', $validator->getErrors()['email']);
            header('Location: /forgot-password');
            exit;
        }

        // Check if email exists
        $user = new User($this->db);
        if (!$user->getByEmail($_POST['email'])) {
            // We don't want to reveal if an email exists or not
            // So we'll show the same success message
            $this->session->setFlash('success', $this->localization->t('auth.reset_link_sent'));
            header('Location: /forgot-password');
            exit;
        }

        // Generate reset token
        $resetToken = $user->resetPassword($_POST['email']);

        if (!$resetToken) {
            $this->session->setFlash('error', $this->localization->t('auth.reset_failed'));
            header('Location: /forgot-password');
            exit;
        }

        // Send reset email
        // This would be implemented in a real-world application
        // For now, we'll just simulate it
        $resetUrl = "http://localhost/reset-password?token=$resetToken";

        // Set success message and redirect
        $this->session->setFlash('success', $this->localization->t('auth.reset_link_sent'));
        header('Location: /forgot-password');
        exit;
    }

    /**
     * Show reset password form
     */
    public function showResetPassword() {
        // If user is already logged in, redirect to dashboard
        if ($this->auth->check()) {
            header('Location: /');
            exit;
        }

        // Check if token is provided
        if (!isset($_GET['token']) || empty($_GET['token'])) {
            $this->session->setFlash('error', $this->localization->t('auth.invalid_token'));
            header('Location: /login');
            exit;
        }

        // Validate token
        $user = new User($this->db);
        if (!$user->validateResetToken($_GET['token'])) {
            $this->session->setFlash('error', $this->localization->t('auth.invalid_token'));
            header('Location: /login');
            exit;
        }

        // Get flash messages for showing errors/success
        $error = $this->session->getFlash('error');
        $token = $_GET['token'];

        // Render reset password view
        include 'views/pages/auth/reset-password.php';
    }

    /**
     * Process reset password form
     */
    public function resetPassword() {
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        // Validate form data
        $validator = new Validator($_POST, $this->localization);
        if (!$validator->validate([
            'token' => 'required',
            'password' => 'required|min:8',
            'password_confirm' => 'required|matches:password'
        ])) {
            $this->session->setFlash('error', $validator->getErrors()['password'] ?? $validator->getErrors()['password_confirm'] ?? $this->localization->t('auth.invalid_input'));
            header('Location: /reset-password?token=' . $_POST['token']);
            exit;
        }

        // Reset password
        $user = new User($this->db);
        if ($user->completeReset($_POST['token'], $_POST['password'])) {
            $this->session->setFlash('success', $this->localization->t('auth.password_reset_success'));
            header('Location: /login');
        } else {
            $this->session->setFlash('error', $this->localization->t('auth.reset_failed'));
            header('Location: /reset-password?token=' . $_POST['token']);
        }
        exit;
    }
}
