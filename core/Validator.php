<?php
/**
 * Form Validator
 * File path: core/Validator.php
 *
 * Provides form validation functionality with support for multilingual error messages
 *
 * @package Egypt Printing Services Marketplace
 * @author  Development Team
 */

class Validator {
    /**
     * @var array Error messages
     */
    private $errors = [];

    /**
     * @var array Field data to validate
     */
    private $data = [];

    /**
     * @var Localization Localization instance for error messages
     */
    private $localization;

    /**
     * Constructor
     *
     * @param array $data Input data to validate
     * @param Localization $localization Localization instance
     */
    public function __construct(array $data, Localization $localization) {
        $this->data = $data;
        $this->localization = $localization;
    }

    /**
     * Validate input data with given rules
     *
     * @param array $rules Rules for validation
     * @return bool Validation result
     */
    public function validate(array $rules): bool {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $fieldRules = explode('|', $fieldRules);

            // Check if field exists before processing
            $value = $this->getValue($field);

            // Skip other validations if field is not required and empty
            if (!in_array('required', $fieldRules) && ($value === '' || $value === null)) {
                continue;
            }

            // Process each rule for the field
            foreach ($fieldRules as $rule) {
                // Check for rules with parameters like max:255
                if (strpos($rule, ':') !== false) {
                    list($ruleName, $param) = explode(':', $rule);
                    $this->validateRule($field, $ruleName, $value, $param);
                } else {
                    $this->validateRule($field, $rule, $value);
                }

                // If field already has an error, move to next field
                if (isset($this->errors[$field])) {
                    break;
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Apply validation rule to a field
     *
     * @param string $field Field name
     * @param string $rule Rule name
     * @param mixed $value Field value
     * @param mixed $param Rule parameter
     * @return bool Rule validation result
     */
    private function validateRule(string $field, string $rule, $value, $param = null): bool {
        switch ($rule) {
            case 'required':
                if ($value === '' || $value === null) {
                    $this->addError($field, 'required');
                    return false;
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'email');
                    return false;
                }
                break;

            case 'min':
                if (is_string($value) && mb_strlen($value) < $param) {
                    $this->addError($field, 'min', ['min' => $param]);
                    return false;
                }
                break;

            case 'max':
                if (is_string($value) && mb_strlen($value) > $param) {
                    $this->addError($field, 'max', ['max' => $param]);
                    return false;
                }
                break;

            case 'matches':
                if ($value !== $this->getValue($param)) {
                    $this->addError($field, 'matches', ['field' => $this->localization->t('fields.' . $param)]);
                    return false;
                }
                break;

            case 'unique':
                // Check if value exists in the database
                // This would require a database connection
                // Implementation will be expanded when database is available
                break;

            case 'numeric':
                if (!is_numeric($value)) {
                    $this->addError($field, 'numeric');
                    return false;
                }
                break;

            case 'alpha':
                if (!ctype_alpha($value)) {
                    $this->addError($field, 'alpha');
                    return false;
                }
                break;

            case 'alphanumeric':
                if (!ctype_alnum($value)) {
                    $this->addError($field, 'alphanumeric');
                    return false;
                }
                break;

            case 'url':
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->addError($field, 'url');
                    return false;
                }
                break;
        }

        return true;
    }

    /**
     * Add an error message for a field
     *
     * @param string $field Field name
     * @param string $rule Rule that failed
     * @param array $params Parameters for translation
     */
    private function addError(string $field, string $rule, array $params = []): void {
        $fieldName = $this->localization->t('fields.' . $field);
        $params['field'] = $fieldName;

        $this->errors[$field] = $this->localization->t('validation.' . $rule, $params);
    }

    /**
     * Get a field value
     *
     * @param string $field Field name
     * @return mixed Field value
     */
    private function getValue(string $field) {
        return $this->data[$field] ?? null;
    }

    /**
     * Check if there are validation errors
     *
     * @return bool True if there are errors
     */
    public function hasErrors(): bool {
        return !empty($this->errors);
    }

    /**
     * Get all validation errors
     *
     * @return array Validation errors
     */
    public function getErrors(): array {
        return $this->errors;
    }

    /**
     * Get error for a specific field
     *
     * @param string $field Field name
     * @return string|null Error message
     */
    public function getError(string $field): ?string {
        return $this->errors[$field] ?? null;
    }

    /**
     * Get error messages as a flat array
     *
     * @return array Error messages
     */
    public function getErrorMessages(): array {
        return array_values($this->errors);
    }
}
