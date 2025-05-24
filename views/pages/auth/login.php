<?php
/**
 * Login Page
 * File path: views/pages/auth/login.php
 *
 * @package Egypt Printing Services Marketplace
 * @author  Development Team
 */
?>

<?php include 'views/components/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg overflow-hidden shadow-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-center mb-6"><?= $this->localization->t('auth.login') ?></h2>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                    <p><?= $error ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                    <p><?= $success ?></p>
                </div>
            <?php endif; ?>

            <form action="/login" method="POST">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= $this->session->generateCsrfToken() ?>">

                <!-- Email Field -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                        <?= $this->localization->t('auth.email') ?>
                    </label>
                    <input type="email" id="email" name="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- Password Field -->
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                        <?= $this->localization->t('auth.password') ?>
                    </label>
                    <input type="password" id="password" name="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- Remember Me -->
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="form-checkbox h-4 w-4 text-blue-600">
                        <span class="ml-2 text-gray-700"><?= $this->localization->t('auth.remember_me') ?></span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    <?= $this->localization->t('auth.login') ?>
                </button>
            </form>

            <div class="mt-4 text-center">
                <a href="/forgot-password" class="text-blue-500 hover:underline">
                    <?= $this->localization->t('auth.forgot_password') ?>
                </a>
            </div>

            <div class="mt-6 text-center">
                <p><?= $this->localization->t('auth.no_account') ?>
                    <a href="/register" class="text-blue-500 hover:underline">
                        <?= $this->localization->t('auth.register') ?>
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'views/components/footer.php'; ?>
