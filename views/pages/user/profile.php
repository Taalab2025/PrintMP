<?php
/**
 * User Profile View
 * File path: views/pages/user/profile.php
 */

$currentPage = 'profile';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="<?= $isRtl ? 'text-right' : 'text-left' ?> mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <?= $this->localization->t('user.profile_title') ?>
        </h1>
        <p class="mt-2 text-gray-600">
            <?= $this->localization->t('user.profile_subtitle') ?>
        </p>
    </div>

    <!-- Flash Messages -->
    <?php if ($this->session->hasFlash('success')): ?>
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md" role="alert">
        <div class="flex">
            <i class="fas fa-check-circle <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
            <span><?= $this->session->getFlash('success') ?></span>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($this->session->hasFlash('error')): ?>
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md" role="alert">
        <div class="flex">
            <i class="fas fa-exclamation-circle <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
            <span><?= $this->session->getFlash('error') ?></span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Profile Form -->
    <div class="bg-white shadow-sm rounded-lg">
        <form method="POST" action="/user/profile" class="divide-y divide-gray-200">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <!-- Basic Information -->
            <div class="px-6 py-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    <?= $this->localization->t('user.basic_information') ?>
                </h2>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Full Name -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                            <?= $this->localization->t('auth.full_name') ?> <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="<?= htmlspecialchars($user['name']) ?>"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= $isRtl ? 'text-right' : 'text-left' ?>"
                               required>
                    </div>

                    <!-- Email -->
                    <div class="sm:col-span-2">
                        <label for="email" class="block text-sm font-medium text-gray-700 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                            <?= $this->localization->t('auth.email') ?> <span class="text-red-500">*</span>
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="<?= htmlspecialchars($user['email']) ?>"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= $isRtl ? 'text-right' : 'text-left' ?>"
                               required>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                            <?= $this->localization->t('auth.phone') ?>
                        </label>
                        <input type="tel"
                               name="phone"
                               id="phone"
                               value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= $isRtl ? 'text-right' : 'text-left' ?>">
                    </div>

                    <!-- Preferred Language -->
                    <div>
                        <label for="preferred_language" class="block text-sm font-medium text-gray-700 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                            <?= $this->localization->t('user.preferred_language') ?> <span class="text-red-500">*</span>
                        </label>
                        <select name="preferred_language"
                                id="preferred_language"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= $isRtl ? 'text-right' : 'text-left' ?>"
                                required>
                            <option value="en" <?= ($user['preferred_language'] ?? 'en') === 'en' ? 'selected' : '' ?>>
                                English
                            </option>
                            <option value="ar" <?= ($user['preferred_language'] ?? 'en') === 'ar' ? 'selected' : '' ?>>
                                العربية
                            </option>
                        </select>
                    </div>

                    <!-- Address -->
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                            <?= $this->localization->t('user.address') ?>
                        </label>
                        <textarea name="address"
                                  id="address"
                                  rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= $isRtl ? 'text-right' : 'text-left' ?>"
                                  placeholder="<?= $this->localization->t('user.address_placeholder') ?>"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Password Change -->
            <div class="px-6 py-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    <?= $this->localization->t('user.change_password') ?>
                </h2>
                <p class="text-sm text-gray-600 mb-4">
                    <?= $this->localization->t('user.password_change_note') ?>
                </p>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Current Password -->
                    <div class="sm:col-span-2">
                        <label for="current_password" class="block text-sm font-medium text-gray-700 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                            <?= $this->localization->t('user.current_password') ?>
                        </label>
                        <input type="password"
                               name="current_password"
                               id="current_password"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= $isRtl ? 'text-right' : 'text-left' ?>">
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                            <?= $this->localization->t('user.new_password') ?>
                        </label>
                        <input type="password"
                               name="new_password"
                               id="new_password"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= $isRtl ? 'text-right' : 'text-left' ?>">
                        <p class="mt-1 text-xs text-gray-500">
                            <?= $this->localization->t('auth.password_requirements') ?>
                        </p>
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 <?= $isRtl ? 'text-right' : 'text-left' ?>">
                            <?= $this->localization->t('user.confirm_new_password') ?>
                        </label>
                        <input type="password"
                               name="confirm_password"
                               id="confirm_password"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= $isRtl ? 'text-right' : 'text-left' ?>">
                    </div>
                </div>
            </div>

            <!-- Account Statistics -->
            <div class="px-6 py-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    <?= $this->localization->t('user.account_statistics') ?>
                </h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="bg-gray-50 px-4 py-3 rounded-md">
                        <dt class="text-sm font-medium text-gray-500">
                            <?= $this->localization->t('user.member_since') ?>
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?= date('F Y', strtotime($user['created_at'])) ?>
                        </dd>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 rounded-md">
                        <dt class="text-sm font-medium text-gray-500">
                            <?= $this->localization->t('user.account_status') ?>
                        </dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $user['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= $this->localization->t("user.status_{$user['status']}") ?>
                            </span>
                        </dd>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 rounded-md">
                        <dt class="text-sm font-medium text-gray-500">
                            <?= $this->localization->t('user.email_verified') ?>
                        </dt>
                        <dd class="mt-1">
                            <?php if ($user['email_verified_at']): ?>
                                <span class="inline-flex items-center text-sm text-green-600">
                                    <i class="fas fa-check-circle <?= $isRtl ? 'ml-1' : 'mr-1' ?>"></i>
                                    <?= $this->localization->t('user.verified') ?>
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center text-sm text-red-600">
                                    <i class="fas fa-times-circle <?= $isRtl ? 'ml-1' : 'mr-1' ?>"></i>
                                    <?= $this->localization->t('user.not_verified') ?>
                                </span>
                            <?php endif; ?>
                        </dd>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 <?= $isRtl ? 'text-left' : 'text-right' ?> rounded-b-lg">
                <div class="flex <?= $isRtl ? 'flex-row-reverse' : 'flex-row' ?> space-x-3 <?= $isRtl ? 'space-x-reverse' : '' ?>">
                    <button type="button"
                            onclick="window.history.back()"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <?= $this->localization->t('general.cancel') ?>
                    </button>

                    <button type="submit"
                            class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                        <?= $this->localization->t('user.update_profile') ?>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Delete Account Section -->
    <div class="mt-8 bg-white shadow-sm rounded-lg border border-red-200">
        <div class="px-6 py-4 border-b border-red-200">
            <h2 class="text-lg font-medium text-red-900">
                <?= $this->localization->t('user.danger_zone') ?>
            </h2>
        </div>
        <div class="px-6 py-4">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-red-900">
                        <?= $this->localization->t('user.delete_account') ?>
                    </h3>
                    <p class="text-sm text-red-700 mt-1">
                        <?= $this->localization->t('user.delete_account_warning') ?>
                    </p>
                </div>
                <div class="<?= $isRtl ? 'mr-4' : 'ml-4' ?> flex-shrink-0">
                    <button type="button"
                            onclick="confirmDeleteAccount()"
                            class="bg-red-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <?= $this->localization->t('user.delete_account') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Confirmation Modal -->
<div id="delete-account-modal" class="fixed inset-0 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeDeleteModal()"></div>

        <div class="relative bg-white rounded-lg max-w-md w-full mx-auto shadow-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <?= $this->localization->t('user.confirm_delete_account') ?>
                </h3>
            </div>

            <div class="px-6 py-4">
                <p class="text-sm text-gray-600 mb-4">
                    <?= $this->localization->t('user.delete_account_confirmation') ?>
                </p>

                <div class="bg-red-50 border border-red-200 rounded-md p-3 mb-4">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-red-400 <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                        <div class="text-sm text-red-700">
                            <strong><?= $this->localization->t('user.warning') ?>:</strong>
                            <?= $this->localization->t('user.delete_permanent_warning') ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 <?= $isRtl ? 'text-left' : 'text-right' ?> rounded-b-lg">
                <div class="flex <?= $isRtl ? 'flex-row-reverse' : 'flex-row' ?> space-x-3 <?= $isRtl ? 'space-x-reverse' : '' ?>">
                    <button type="button"
                            onclick="closeDeleteModal()"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <?= $this->localization->t('general.cancel') ?>
                    </button>

                    <button type="button"
                            onclick="deleteAccount()"
                            class="bg-red-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700">
                        <?= $this->localization->t('user.yes_delete_account') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDeleteAccount() {
    document.getElementById('delete-account-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-account-modal').classList.add('hidden');
}

function deleteAccount() {
    // In a real implementation, this would make an AJAX call to delete the account
    alert('<?= $this->localization->t('user.delete_account_feature_coming_soon') ?>');
    closeDeleteModal();
}

// Password strength indicator
document.getElementById('new_password').addEventListener('input', function() {
    const password = this.value;
    const indicator = document.getElementById('password-strength');

    if (password.length === 0) {
        if (indicator) indicator.remove();
        return;
    }

    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    const colors = ['red', 'red', 'yellow', 'yellow', 'green'];
    const labels = ['<?= $this->localization->t('user.very_weak') ?>', '<?= $this->localization->t('user.weak') ?>', '<?= $this->localization->t('user.fair') ?>', '<?= $this->localization->t('user.good') ?>', '<?= $this->localization->t('user.strong') ?>'];

    let existingIndicator = document.getElementById('password-strength');
    if (!existingIndicator) {
        existingIndicator = document.createElement('div');
        existingIndicator.id = 'password-strength';
        existingIndicator.className = 'mt-1 text-xs';
        this.parentNode.appendChild(existingIndicator);
    }

    existingIndicator.innerHTML = `<span class="text-${colors[strength - 1]}-600"><?= $this->localization->t('user.password_strength') ?>: ${labels[strength - 1]}</span>`;
});

// Confirm password match validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('new_password').value;
    const confirmPassword = this.value;

    let existingIndicator = document.getElementById('password-match');
    if (!existingIndicator && confirmPassword.length > 0) {
        existingIndicator = document.createElement('div');
        existingIndicator.id = 'password-match';
        existingIndicator.className = 'mt-1 text-xs';
        this.parentNode.appendChild(existingIndicator);
    }

    if (confirmPassword.length === 0) {
        if (existingIndicator) existingIndicator.remove();
        return;
    }

    if (password === confirmPassword) {
        existingIndicator.innerHTML = '<span class="text-green-600"><i class="fas fa-check <?= $isRtl ? 'ml-1' : 'mr-1' ?>"></i><?= $this->localization->t('user.passwords_match') ?></span>';
    } else {
        existingIndicator.innerHTML = '<span class="text-red-600"><i class="fas fa-times <?= $isRtl ? 'ml-1' : 'mr-1' ?>"></i><?= $this->localization->t('user.passwords_dont_match') ?></span>';
    }
});
</script>
