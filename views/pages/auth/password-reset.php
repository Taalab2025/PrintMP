<?php
/**
 * Reset Password Page
 * File path: views/pages/auth/reset-password.php
 *
 * @package Egypt Printing Services Marketplace
 * @author  Development Team
 */
?>

<?php include 'views/components/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg overflow-hidden shadow-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-center mb-6"><?= $this->localization->t('auth.reset_password') ?></h2>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                    <p><?= $error ?></p>
                </div>
            <?php endif; ?>

            <form action="/reset-password" method="POST">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= $this->session->generateCsrfToken() ?>">

                <!-- Token Field -->
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <!-- Password Field -->
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                        <?= $this->localization->t('auth.new_password') ?>
                    </label>
                    <input type="password" id="password" name="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <p class="text-gray-600 text-xs mt-1"><?= $this->localization->t('auth.password_hint') ?></p>
                </div>

                <!-- Confirm Password Field -->
                <div class="mb-4">
                    <label for="password_confirm" class="block text-gray-700 text-sm font-bold mb-2">
                        <?= $this->localization->t('auth.confirm_password') ?>
                    </label>
                    <input type="password" id="password_confirm" name="password_confirm"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    <?= $this->localization->t('auth.reset_password') ?>
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="/login" class="text-blue-500 hover:underline">
                    <?= $this->localization->t('auth.back_to_login') ?>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'views/components/footer.php'; ?>
