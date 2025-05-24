<?php
/**
 * File Upload Handler
 * File path: core/FileUpload.php
 *
 * Handles file uploads with validation and security checks
 *
 * @package Egypt Printing Services Marketplace
 * @author  Development Team
 */

class FileUpload {
    /**
     * @var array Allowed file extensions
     */
    private $allowedExtensions = [];

    /**
     * @var int Maximum file size in bytes
     */
    private $maxSize = 5242880; // 5MB default

    /**
     * @var string Upload directory
     */
    private $uploadDir = '';

    /**
     * @var string Last error message
     */
    private $error = '';

    /**
     * @var array Uploaded files info
     */
    private $uploadedFiles = [];

    /**
     * Constructor
     *
     * @param string $uploadDir Upload directory
     * @param array $allowedExtensions Allowed file extensions
     * @param int $maxSize Maximum file size in bytes
     */
    public function __construct(string $uploadDir, array $allowedExtensions = [], int $maxSize = 0) {
        // Ensure upload directory ends with a slash
        $this->uploadDir = rtrim($uploadDir, '/') . '/';

        // Create directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        $this->allowedExtensions = $allowedExtensions;

        if ($maxSize > 0) {
            $this->maxSize = $maxSize;
        }
    }

    /**
     * Upload a single file
     *
     * @param array $file File data ($_FILES['field'])
     * @param string $newName New file name (optional)
     * @return bool|string False on failure, filename on success
     */
    public function upload(array $file, string $newName = ''): ?string {
        // Check if file was uploaded properly
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $this->error = 'No file uploaded';
            return null;
        }

        // Check file size
        if ($file['size'] > $this->maxSize) {
            $this->error = 'File size exceeds the limit of ' . $this->formatSize($this->maxSize);
            return null;
        }

        // Get file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Check file extension
        if (!empty($this->allowedExtensions) && !in_array($extension, $this->allowedExtensions)) {
            $this->error = 'File type not allowed. Allowed types: ' . implode(', ', $this->allowedExtensions);
            return null;
        }

        // Generate unique filename if not provided
        if (empty($newName)) {
            $newName = $this->generateUniqueFilename($extension);
        } else {
            // Ensure the name has the correct extension
            $newName = pathinfo($newName, PATHINFO_FILENAME) . '.' . $extension;
        }

        $destination = $this->uploadDir . $newName;

        // Move the uploaded file
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $this->error = 'Failed to move uploaded file';
            return null;
        }

        // Store uploaded file info
        $this->uploadedFiles[] = [
            'original_name' => $file['name'],
            'new_name' => $newName,
            'path' => $destination,
            'size' => $file['size'],
            'type' => $file['type'],
            'extension' => $extension
        ];

        return $newName;
    }

    /**
     * Upload multiple files
     *
     * @param array $files Files data ($_FILES['field'])
     * @return array Array of uploaded filenames
     */
    public function uploadMultiple(array $files): array {
        $uploadedFiles = [];

        // Check if files array is in correct format
        if (!isset($files['name']) || !is_array($files['name'])) {
            $this->error = 'Invalid files array format';
            return $uploadedFiles;
        }

        $fileCount = count($files['name']);

        for ($i = 0; $i < $fileCount; $i++) {
            // Skip empty entries
            if (empty($files['name'][$i])) {
                continue;
            }

            // Create a single file array
            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            ];

            $result = $this->upload($file);

            if ($result) {
                $uploadedFiles[] = $result;
            }
        }

        return $uploadedFiles;
    }

    /**
     * Generate a unique filename
     *
     * @param string $extension File extension
     * @return string Unique filename
     */
    private function generateUniqueFilename(string $extension): string {
        return md5(uniqid(mt_rand(), true)) . '.' . $extension;
    }

    /**
     * Format file size for display
     *
     * @param int $bytes File size in bytes
     * @return string Formatted file size
     */
    private function formatSize(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get last error message
     *
     * @return string Error message
     */
    public function getError(): string {
        return $this->error;
    }

    /**
     * Get uploaded files info
     *
     * @return array Uploaded files info
     */
    public function getUploadedFiles(): array {
        return $this->uploadedFiles;
    }

    /**
     * Set allowed file extensions
     *
     * @param array $extensions Allowed file extensions
     * @return FileUpload
     */
    public function setAllowedExtensions(array $extensions): self {
        $this->allowedExtensions = array_map('strtolower', $extensions);
        return $this;
    }

    /**
     * Set maximum file size
     *
     * @param int $size File size in bytes
     * @return FileUpload
     */
    public function setMaxSize(int $size): self {
        $this->maxSize = $size;
        return $this;
    }

    /**
     * Delete a file
     *
     * @param string $filename Filename to delete
     * @return bool True if file was deleted
     */
    public function deleteFile(string $filename): bool {
        $filepath = $this->uploadDir . $filename;

        if (file_exists($filepath)) {
            return unlink($filepath);
        }

        return false;
    }
}
