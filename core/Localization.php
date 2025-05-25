<?php
/**
 * Localization Class
 * file path: core/Localization.php
 */

class Localization {
    private $language;
    private $translations = [];
    private $availableLanguages = ['en', 'ar']; // Supported languages
    private $defaultLanguage = 'en';
    private $rtlLanguages = ['ar']; // Languages that use RTL

    /**
     * Constructor
     *
     * @param string $language The language code to use
     */
    public function __construct($language = null) {
        // Use provided language, cookie, or default
        $this->language = $this->validateLanguage($language);
        

        // Load core translations
        $this->loadTranslations('general');
    }

    /**
     * Validate language code
     *
     * @param string $language Language code to validate
     * @return string Valid language code
     */
    private function validateLanguage($language) {
        // If language is provided and valid, use it
        if ($language && in_array($language, $this->availableLanguages)) {
            return $language;
        }

        // Check if language is set in cookie
        if (isset($_COOKIE['language']) && in_array($_COOKIE['language'], $this->availableLanguages)) {
            return $_COOKIE['language'];
        }

        // Check browser language
        $browserLang = $this->getBrowserLanguage();
        if ($browserLang && in_array($browserLang, $this->availableLanguages)) {
            return $browserLang;
        }

        // Fallback to default
        return $this->defaultLanguage;
    }

    /**
     * Get browser language
     *
     * @return string|null Browser language code or null
     */
    private function getBrowserLanguage() {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLangs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($browserLangs as $lang) {
                $lang = substr($lang, 0, 2); // Get first 2 chars
                if (in_array($lang, $this->availableLanguages)) {
                    return $lang;
                }
            }
        }
        return null;
    }

    /**
     * Set the current language
     *
     * @param string $language Language code
     * @param bool $saveCookie Whether to save in cookie
     * @return bool Success status
     */
    public function setLanguage($language, $saveCookie = true) {
        if (!in_array($language, $this->availableLanguages)) {
            return false;
        }

        $this->language = $language;

        // Save in cookie for 30 days
        if ($saveCookie) {
            setcookie('language', $language, time() + (86400 * 30), '/');
        }

        // Reload translations for current language
        $this->reloadTranslations();

        return true;
    }

    /**
     * Get current language
     *
     * @return string Current language code
     */
    public function getCurrentLanguage() {
        return $this->language;
    }

    /**
     * Check if current language is RTL
     *
     * @return bool True if language is RTL
     */
    public function isRtl() {
        return in_array($this->language, $this->rtlLanguages);
    }

    /**
     * Get available languages
     *
     * @return array Available language codes
     */
    public function getAvailableLanguages() {
        return $this->availableLanguages;
    }

    /**
     * Load translations for a module
     *
     * @param string $module Module name (e.g., 'general', 'auth')
     * @return bool Success status
     */
    public function loadTranslations($module) {
        $path = 'lang/' . $this->language . '/' . $module . '.php';

        if (file_exists($path)) {
            $translations = include $path;

            if (is_array($translations)) {
                // Save with module prefix to avoid conflicts
                foreach ($translations as $key => $value) {
                    $this->translations[$module . '.' . $key] = $value;
                }
                return true;
            }
        }

        // Try to load default language as fallback
        if ($this->language !== $this->defaultLanguage) {
            $defaultPath = 'lang/' . $this->defaultLanguage . '/' . $module . '.php';

            if (file_exists($defaultPath)) {
                $translations = include $defaultPath;

                if (is_array($translations)) {
                    foreach ($translations as $key => $value) {
                        $this->translations[$module . '.' . $key] = $value;
                    }
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Reload all loaded translations
     */
    private function reloadTranslations() {
        // Get list of loaded modules
        $loadedModules = [];
        foreach (array_keys($this->translations) as $key) {
            $module = explode('.', $key)[0];
            $loadedModules[$module] = true;
        }

        // Clear translations
        $this->translations = [];

        // Reload all modules
        foreach (array_keys($loadedModules) as $module) {
            $this->loadTranslations($module);
        }
    }

    /**
     * Get translation by key
     *
     * @param string $key Translation key (with or without module prefix)
     * @param array $params Parameters for placeholder replacement
     * @return string Translated text or key if not found
     */
    public function t($key, $params = []) {
        // If key doesn't contain dot, assume it's from 'general' module
        if (strpos($key, '.') === false) {
            $key = 'general.' . $key;
        }

        // Check if translation exists
        if (isset($this->translations[$key])) {
            $translation = $this->translations[$key];

            // Replace parameters if any
            if (!empty($params)) {
                foreach ($params as $param => $value) {
                    $translation = str_replace(':' . $param, $value, $translation);
                }
            }

            return $translation;
        }

        // Try to load module if not loaded yet
        $parts = explode('.', $key);
        if (count($parts) >= 2) {
            $module = $parts[0];
            $moduleKey = implode('.', array_slice($parts, 1));

            // Check if we already tried to load this module
            $moduleLoaded = false;
            foreach (array_keys($this->translations) as $loadedKey) {
                if (strpos($loadedKey, $module . '.') === 0) {
                    $moduleLoaded = true;
                    break;
                }
            }

            // If module not loaded yet, try to load it
            if (!$moduleLoaded && $this->loadTranslations($module)) {
                // Try again with the full key
                return $this->t($key, $params);
            }
        }

        // Fallback to key
        return $key;
    }

    /**
     * Load multiple translation modules at once
     *
     * @param array $modules Array of module names
     */
    public function loadModules($modules) {
        foreach ($modules as $module) {
            $this->loadTranslations($module);
        }
    }

    /**
     * Get language name from code
     *
     * @param string $code Language code
     * @return string Language name
     */
    public function getLanguageName($code) {
        $names = [
            'en' => 'English',
            'ar' => 'العربية'
        ];

        return $names[$code] ?? $code;
    }

    /**
     * Get language direction
     *
     * @param string $code Language code (optional, uses current if not specified)
     * @return string 'rtl' or 'ltr'
     */
    public function getDirection($code = null) {
        $lang = $code ?? $this->language;
        return in_array($lang, $this->rtlLanguages) ? 'rtl' : 'ltr';
    }
}
