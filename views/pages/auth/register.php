<?php
/**
 * Registration Page
 * File path: views/pages/auth/register.php
 *
 * @package Egypt Printing Services Marketplace
 * @author  Development Team
 */
?>

<?php include 'views/components/header.php'; ?>

<?php
// Get old form data for repopulating form
$oldData = $this->session->getFlash('form_data') ?? [];
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg overflow-hidden shadow-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-center mb-6"><?= $this->localization->t('auth.register') ?></h2>

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

            <form action="/register" method="POST">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= $this->session->generateCsrfToken() ?>">

                <!-- Account Type Selection -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        <?= $this->localization->t('auth.account_type') ?>
                    </label>
                    <div class="flex flex-wrap">
                        <label class="w-full md:w-1/2 flex items-center mb-2 md:mb-0">
                            <input type="radio" name="role" value="customer" class="form-radio h-4 w-4 text-blue-600"
                                <?= (!isset($oldData['role']) || $oldData['role'] === 'customer') ? 'checked' : '' ?>>
                            <span class="ml-2 text-gray-700"><?= $this->localization->t('auth.customer') ?></span>
                        </label>
                        <label class="w-full md:w-1/2 flex items-center">
                            <input type="radio" name="role" value="vendor" class="form-radio h-4 w-4 text-blue-600"
                                <?= (isset($oldData['role']) && $oldData['role'] === 'vendor') ? 'checked' : '' ?>>
                            <span class="ml-2 text-gray-700"><?= $this->localization->t('auth.vendor') ?></span>
                        </label>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4"><?= $this->localization->t('auth.basic_info') ?></h3>

                    <!-- Name Field -->
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                            <?= $this->localization->t('auth.name') ?> <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="<?= htmlspecialchars($oldData['name'] ?? '') ?>"
                            required>
                    </div>

                    <!-- Email Field -->
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                            <?= $this->localization->t('auth.email') ?> <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="<?= htmlspecialchars($oldData['email'] ?? '') ?>"
                            required>
                    </div>

                    <!-- Password Field -->
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                            <?= $this->localization->t('auth.password') ?> <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password" name="password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                        <p class="text-gray-600 text-xs mt-1"><?= $this->localization->t('auth.password_hint') ?></p>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="mb-4">
                        <label for="password_confirm" class="block text-gray-700 text-sm font-bold mb-2">
                            <?= $this->localization->t('auth.confirm_password') ?> <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password_confirm" name="password_confirm"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>
                </div>

                <!-- Vendor Information (Hidden by default) -->
                <div id="vendor-fields" class="mb-6 vendor-fields <?= (isset($oldData['role']) && $oldData['role'] === 'vendor') ? '' : 'hidden' ?>">
                    <h3 class="text-lg font-semibold mb-4"><?= $this->localization->t('auth.vendor_info') ?></h3>

                    <!-- Company Name (English) -->
                    <div class="mb-4">
                        <label for="company_name" class="block text-gray-700 text-sm font-bold mb-2">
                            <?= $this->localization->t('auth.company_name_en') ?> <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="company_name" name="company_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="<?= htmlspecialchars($oldData['company_name'] ?? '') ?>">
                    </div>

                    <!-- Company Name (Arabic) -->
                    <div class="mb-4">
                        <label for="company_name_ar" class="block text-gray-700 text-sm font-bold mb-2">
                            <?= $this->localization->t('auth.company_name_ar') ?>
                        </label>
                        <input type="text" id="company_name_ar" name="company_name_ar"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="<?= htmlspecialchars($oldData['company_name_ar'] ?? '') ?>">
                    </div>

                    <!-- Phone -->
                    <div class="mb-4">
                        <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">
                            <?= $this->localization->t('auth.phone') ?> <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="phone" name="phone"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="<?= htmlspecialchars($oldData['phone'] ?? '') ?>">
                    </div>

                    <!-- Address -->
                    <div class="mb-4">
                        <label for="address" class="block text-gray-700 text-sm font-bold mb-2">
                            <?= $this->localization->t('auth.address') ?> <span class="text-red-500">*</span>
                        </label>
                        <textarea id="address" name="address" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($oldData['address'] ?? '') ?></textarea>
                    </div>

                    <!-- Terms for Vendors -->
                    <div class="mb-4">
                        <p class="text-gray-700 text-sm mb-2">
                            <?= $this->localization->t('auth.vendor_terms_intro') ?>
                        </p>
                        <ul class="list-disc pl-5 text-sm text-gray-700 mb-2">
                            <li><?= $this->localization->t('auth.vendor_terms_1') ?></li>
                            <li><?= $this->localization->t('auth.vendor_terms_2') ?></li>
                            <li><?= $this->localization->t('auth.vendor_terms_3') ?></li>
                        </ul>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="terms" class="form-checkbox h-4 w-4 text-blue-600" required>
                        <span class="ml-2 text-gray-700 text-sm">
                            <?= $this->localization->t('auth.agree_terms') ?>
                            <a href="/terms" class="text-blue-500 hover:underline"><?= $this->localization->t('auth.terms_link') ?></a>
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    <?= $this->localization->t('auth.register') ?>
                </button>
            </form>

            <div class="mt-6 text-center">
                <p><?= $this->localization->t('auth.have_account') ?>
                    <a href="/login" class="text-blue-500 hover:underline">
                        <?= $this->localization->t('auth.login') ?>
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle vendor fields based on role selection
    document.addEventListener('DOMContentLoaded', function() {
        const roleRadios = document.querySelectorAll('input[name="role"]');
        const vendorFields = document.getElementById('vendor-fields');

        roleRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'vendor') {
                    vendorFields.classList.remove('hidden');
                } else {
                    vendorFields.classList.add('hidden');
                }
            });
        });
    });
</script>

<?php include 'views/components/footer.php'; ?>
