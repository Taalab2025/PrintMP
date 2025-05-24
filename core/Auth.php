<?php
/**
 * Auth - Authentication System
 * Egypt Printing Services Marketplace
 */

class Auth
{
    private $db;
    private $session;
    private $user = null;

    /**
     * Constructor
     */
    public function __construct($db, $session)
    {
        $this->db = $db;
        $this->session = $session;

        // Load user if session exists
        if ($this->session->has('user_id')) {
            $this->loadUser($this->session->get('user_id'));
        }
    }

    /**
     * Load user by ID
     */
    private function loadUser($userId)
    {
        $sql = "SELECT u.*, v.id as vendor_id
                FROM users u
                LEFT JOIN vendors v ON u.id = v.user_id
                WHERE u.id = ? AND u.status = 'active'";

        $user = $this->db->fetchOne($sql, [$userId]);

        if ($user) {
            // Remove password from user array
            unset($user['password']);
            $this->user = $user;
        } else {
            // Invalid user ID in session, clear it
            $this->session->remove('user_id');
            $this->user = null;
        }
    }

    /**
     * Attempt to authenticate a user
     */
    public function attempt($email, $password)
    {
        $sql = "SELECT u.*, v.id as vendor_id
                FROM users u
                LEFT JOIN vendors v ON u.id = v.user_id
                WHERE u.email = ? AND u.status = 'active'";

        $user = $this->db->fetchOne($sql, [$email]);

        if ($user && password_verify($password, $user['password'])) {
            // Set the user session
            $this->session->set('user_id', $user['id']);
            $this->session->regenerate();

            // Load the user
            unset($user['password']);
            $this->user = $user;

            return true;
        }

        return false;
    }

    /**
     * Register a new user
     */
    public function register($data)
    {
        // Check if email already exists
        $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
        $count = $this->db->fetchColumn($sql, [$data['email']]);

        if ($count > 0) {
            return false;
        }

        // Hash the password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // Set default values
        $data['role'] = isset($data['role']) ? $data['role'] : 'customer';
        $data['status'] = 'active';
        $data['email_verified'] = 0;

        // Generate verification token
        $verificationToken = bin2hex(random_bytes(32));
        $data['remember_token'] = $verificationToken;

        // Insert the user
        $userId = $this->db->insert('users', $data);

        // If registering as a vendor, create vendor record
        if ($data['role'] === 'vendor' && isset($data['company_name_en'])) {
            $vendorData = [
                'user_id' => $userId,
                'company_name_en' => $data['company_name_en'],
                'company_name_ar' => $data['company_name_ar'] ?? $data['company_name_en'],
                'subscription_status' => 'free',
                'requests_quota' => FREE_QUOTE_LIMIT,
                'requests_used' => 0
            ];

            $this->db->insert('vendors', $vendorData);
        }

        return $userId;
    }

    /**
     * Get the current authenticated user
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * Check if a user is authenticated
     */
    public function check()
    {
        return $this->user !== null;
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        if (!$this->check()) {
            return false;
        }

        return $this->user['role'] === $role;
    }

    /**
     * Check if user is a vendor
     */
    public function isVendor()
    {
        return $this->hasRole('vendor');
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is a customer
     */
    public function isCustomer()
    {
        return $this->hasRole('customer');
    }

    /**
     * Logout the current user
     */
    public function logout()
    {
        $this->session->remove('user_id');
        $this->session->regenerate();
        $this->user = null;
    }

    /**
     * Generate a password reset token
     */
    public function generatePasswordResetToken($email)
    {
        $sql = "SELECT id FROM users WHERE email = ? AND status = 'active'";
        $userId = $this->db->fetchColumn($sql, [$email]);

        if (!$userId) {
            return false;
        }

        // Generate token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Update user
        $this->db->update('users',
            ['reset_token' => $token, 'reset_token_expiry' => $expiry],
            'id = ?',
            [$userId]
        );

        return $token;
    }

    /**
     * Validate a password reset token
     */
    public function validateResetToken($token)
    {
        $sql = "SELECT id FROM users
                WHERE reset_token = ?
                AND reset_token_expiry > NOW()
                AND status = 'active'";

        return $this->db->fetchColumn($sql, [$token]);
    }

    /**
     * Reset a user's password
     */
    public function resetPassword($token, $password)
    {
        $userId = $this->validateResetToken($token);

        if (!$userId) {
            return false;
        }

        // Hash the new password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Update the user
        $this->db->update('users',
            [
                'password' => $hashedPassword,
                'reset_token' => null,
                'reset_token_expiry' => null
            ],
            'id = ?',
            [$userId]
        );

        return true;
    }

    /**
     * Verify a user's email
     */
    public function verifyEmail($token)
    {
        $sql = "SELECT id FROM users
                WHERE remember_token = ?
                AND status = 'active'";

        $userId = $this->db->fetchColumn($sql, [$token]);

        if (!$userId) {
            return false;
        }

        // Update the user
        $this->db->update('users',
            [
                'email_verified' => 1,
                'remember_token' => null
            ],
            'id = ?',
            [$userId]
        );

        return true;
    }
}
