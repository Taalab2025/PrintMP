<?php
/**
 * User Model
 * File path: models/User.php
 *
 * Handles user data and operations
 *
 * @package Egypt Printing Services Marketplace
 * @author  Development Team
 */

class User {
    /**
     * @var Database Database instance
     */
    private $db;

    /**
     * @var array User data
     */
    private $data = [];

    /**
     * User roles
     */
    const ROLE_CUSTOMER = 'customer';
    const ROLE_VENDOR = 'vendor';
    const ROLE_ADMIN = 'admin';

    /**
     * Constructor
     *
     * @param Database $db Database instance
     */
    public function __construct(Database $db) {
        $this->db = $db;
    }

    /**
     * Get user by ID
     *
     * @param int $id User ID
     * @return User This instance
     */
    public function getById(int $id): ?User {
        $user = $this->db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);

        if ($user) {
            $this->data = $user;
            return $this;
        }

        return null;
    }

    /**
     * Get user by email
     *
     * @param string $email User email
     * @return User|null This instance or null if not found
     */
    public function getByEmail(string $email): ?User {
        $user = $this->db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);

        if ($user) {
            $this->data = $user;
            return $this;
        }

        return null;
    }

    /**
     * Create a new user
     *
     * @param array $data User data
     * @return int|bool New user ID or false on failure
     */
    public function create(array $data) {
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // Set default role if not provided
        if (!isset($data['role'])) {
            $data['role'] = self::ROLE_CUSTOMER;
        }

        // Set creation date
        $data['created_at'] = date('Y-m-d H:i:s');

        // Set email verification token if email verification is enabled
        if (isset($data['require_email_verification']) && $data['require_email_verification']) {
            $data['email_verified'] = 0;
            $data['verification_token'] = $this->generateToken();
            unset($data['require_email_verification']);
        } else {
            $data['email_verified'] = 1;
            $data['verification_token'] = null;
        }

        // Insert user
        $userId = $this->db->insert('users', $data);

        if ($userId) {
            $this->data = $this->getById($userId)->data;
            return $userId;
        }

        return false;
    }

    /**
     * Update user
     *
     * @param int $id User ID
     * @param array $data User data
     * @return bool True on success
     */
    public function update(int $id, array $data): bool {
        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            // Remove password field if empty
            unset($data['password']);
        }

        // Set update date
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Update user
        $result = $this->db->update('users', $data, ['id' => $id]);

        if ($result && $this->data['id'] == $id) {
            // Refresh user data
            $this->data = $this->getById($id)->data;
        }

        return $result;
    }

    /**
     * Verify user password
     *
     * @param string $password Plain password
     * @return bool True if password is correct
     */
    public function verifyPassword(string $password): bool {
        if (empty($this->data)) {
            return false;
        }

        return password_verify($password, $this->data['password']);
    }

    /**
     * Generate a secure token
     *
     * @return string Secure token
     */
    private function generateToken(): string {
        return bin2hex(random_bytes(32));
    }

    /**
     * Verify email
     *
     * @param string $token Verification token
     * @return bool True on success
     */
    public function verifyEmail(string $token): bool {
        $user = $this->db->fetchOne("SELECT * FROM users WHERE verification_token = ?", [$token]);

        if ($user) {
            $result = $this->db->update('users', [
                'email_verified' => 1,
                'verification_token' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $user['id']]);

            if ($result && $this->data['id'] == $user['id']) {
                $this->data['email_verified'] = 1;
                $this->data['verification_token'] = null;
            }

            return $result;
        }

        return false;
    }

    /**
     * Reset password
     *
     * @param string $email User email
     * @return string|bool Reset token or false on failure
     */
    public function resetPassword(string $email) {
        $user = $this->getByEmail($email);

        if ($user) {
            $resetToken = $this->generateToken();
            $resetExpires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $result = $this->db->update('users', [
                'reset_token' => $resetToken,
                'reset_expires' => $resetExpires,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $user->data['id']]);

            if ($result) {
                return $resetToken;
            }
        }

        return false;
    }

    /**
     * Validate reset token
     *
     * @param string $token Reset token
     * @return bool True if token is valid
     */
    public function validateResetToken(string $token): bool {
        $user = $this->db->fetchOne("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()", [$token]);

        if ($user) {
            $this->data = $user;
            return true;
        }

        return false;
    }

    /**
     * Complete password reset
     *
     * @param string $token Reset token
     * @param string $newPassword New password
     * @return bool True on success
     */
    public function completeReset(string $token, string $newPassword): bool {
        if ($this->validateResetToken($token)) {
            $result = $this->db->update('users', [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT),
                'reset_token' => null,
                'reset_expires' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $this->data['id']]);

            if ($result) {
                $this->data['reset_token'] = null;
                $this->data['reset_expires'] = null;
                return true;
            }
        }

        return false;
    }

    /**
     * Get user data
     *
     * @return array User data
     */
    public function getData(): array {
        return $this->data;
    }

    /**
     * Check if user is vendor
     *
     * @return bool True if user is vendor
     */
    public function isVendor(): bool {
        return isset($this->data['role']) && $this->data['role'] == self::ROLE_VENDOR;
    }

    /**
     * Check if user is admin
     *
     * @return bool True if user is admin
     */
    public function isAdmin(): bool {
        return isset($this->data['role']) && $this->data['role'] == self::ROLE_ADMIN;
    }

    /**
     * Check if user is customer
     *
     * @return bool True if user is customer
     */
    public function isCustomer(): bool {
        return isset($this->data['role']) && $this->data['role'] == self::ROLE_CUSTOMER;
    }

    /**
     * Get all users
     *
     * @param array $filters Filter options
     * @param int $limit Limit results
     * @param int $offset Offset results
     * @return array Users data
     */
    public function getAll(array $filters = [], int $limit = 0, int $offset = 0): array {
        $sql = "SELECT * FROM users";
        $params = [];

        // Apply filters
        if (!empty($filters)) {
            $sqlFilters = [];

            if (isset($filters['role'])) {
                $sqlFilters[] = "role = ?";
                $params[] = $filters['role'];
            }

            if (isset($filters['email_verified'])) {
                $sqlFilters[] = "email_verified = ?";
                $params[] = $filters['email_verified'];
            }

            if (isset($filters['search'])) {
                $sqlFilters[] = "(name LIKE ? OR email LIKE ?)";
                $params[] = "%{$filters['search']}%";
                $params[] = "%{$filters['search']}%";
            }

            if (!empty($sqlFilters)) {
                $sql .= " WHERE " . implode(" AND ", $sqlFilters);
            }
        }

        // Apply sorting
        $sql .= " ORDER BY created_at DESC";

        // Apply limit and offset
        if ($limit > 0) {
            $sql .= " LIMIT ?";
            $params[] = $limit;

            if ($offset > 0) {
                $sql .= " OFFSET ?";
                $params[] = $offset;
            }
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Count users
     *
     * @param array $filters Filter options
     * @return int User count
     */
    public function count(array $filters = []): int {
        $sql = "SELECT COUNT(*) as count FROM users";
        $params = [];

        // Apply filters
        if (!empty($filters)) {
            $sqlFilters = [];

            if (isset($filters['role'])) {
                $sqlFilters[] = "role = ?";
                $params[] = $filters['role'];
            }

            if (isset($filters['email_verified'])) {
                $sqlFilters[] = "email_verified = ?";
                $params[] = $filters['email_verified'];
            }

            if (isset($filters['search'])) {
                $sqlFilters[] = "(name LIKE ? OR email LIKE ?)";
                $params[] = "%{$filters['search']}%";
                $params[] = "%{$filters['search']}%";
            }

            if (!empty($sqlFilters)) {
                $sql .= " WHERE " . implode(" AND ", $sqlFilters);
            }
        }

        $result = $this->db->fetchOne($sql, $params);
        return $result ? (int)$result['count'] : 0;
    }
}
