<?php
/**
 * Quote Request Form
 * File path: views/pages/quotes/request.php
 *
 * Allows users to request a quote for a specific service
 */

// Get necessary variables
$isRtl = $this->localization->isRtl();
$textAlign = $isRtl ? 'text-right' : 'text-left';
$marginStart = $isRtl ? 'mr' : 'ml';
$marginEnd = $isRtl ? 'ml' : 'mr';
$csrfToken = $this->session->generateCsrfToken();
$errors = $this->session->getFlash('errors') ?? [];
$oldInput = $this->session->getFlash('old_input') ?? [];

$title = $this->localization->t('quotes.request_title', ['service' => $service["title_{$language}"]]);
?>

<?php include 'views/layouts/main.php'; ?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <div class="flex items-center py-4 text-sm text-gray-600 <?= $textAlign ?>">
        <a href="/" class="hover:text-primary-600"><?= $this->localization->t('nav.home') ?></a>
        <svg class="h-5 w-5 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="/services" class="hover:text-primary-600"><?= $this->localization->t('nav.services') ?></a>
        <svg class="h-5 w-5 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="/services/<?= $service['id'] ?>" class="hover:text-primary-600"><?= $service["title_{$language}"] ?></a>
        <svg class="h-5 w-5 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-500"><?= $this->localization->t('quotes.request_quote') ?></span>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="md:flex">
            <!-- Service Info Section -->
            <div class="md:w-1/3 bg-gray-50 p-6 <?= $textAlign ?>">
                <h2 class="text-xl font-bold mb-4"><?= $service["title_{$language}"] ?></h2>

                <?php if ($service['image']): ?>
                <div class="mb-4">
                    <img src="<?= $service['image'] ?>" alt="<?= $service["title_{$language}"] ?>" class="w-full h-auto rounded-md">
                </div>
                <?php endif; ?>

                <div class="mb-4">
                    <h3 class="font-semibold text-lg mb-2"><?= $this->localization->t('services.vendor') ?>:</h3>
                    <div class="flex items-center">
                        <?php if ($vendor['logo']): ?>
                        <img src="<?= $vendor['logo'] ?>" alt="<?= $vendor["company_name_{$language}"] ?>" class="w-8 h-8 rounded-full <?= $marginEnd ?>-2">
                        <?php endif; ?>
                        <span><?= $vendor["company_name_{$language}"] ?></span>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="font-semibold text-lg mb-2"><?= $this->localization->t('services.description') ?>:</h3>
                    <p class="text-gray-700"><?= $service["description_{$language}"] ?></p>
                </div>

                <?php if (!empty($service['delivery_time'])): ?>
                <div class="mb-4">
                    <h3 class="font-semibold text-lg mb-2"><?= $this->localization->t('services.delivery_time') ?>:</h3>
                    <p class="text-gray-700"><?= $service['delivery_time'] ?> <?= $this->localization->t('services.days') ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($service['min_order'])): ?>
                <div class="mb-4">
                    <h3 class="font-semibold text-lg mb-2"><?= $this->localization->t('services.min_order') ?>:</h3>
                    <p class="text-gray-700"><?= $service['min_order'] ?></p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Quote Request Form Section -->
            <div class="md:w-2/3 p-6">
                <h1 class="text-2xl font-bold mb-6 <?= $textAlign ?>"><?= $this->localization->t('quotes.request_title', ['service' => $service["title_{$language}"]]) ?></h1>

                <form action="/quotes/submit" method="post" enctype="multipart/form-data" class="<?= $textAlign ?>">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <input type="hidden" name="service_id" value="<?= $service['id'] ?>">

                    <!-- Service Options Section -->
                    <?php if (!empty($service['options'])): ?>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4"><?= $this->localization->t('quotes.service_options') ?></h3>
                        <div class="space-y-4">
                            <?php foreach ($service['options'] as $option): ?>
                                <div class="form-group">
                                    <label for="option_<?= $option['id'] ?>" class="block font-medium mb-2">
                                        <?= $option["name_{$language}"] ?>
                                        <?php if ($option['is_required']): ?>
                                            <span class="text-red-500">*</span>
                                        <?php endif; ?>
                                    </label>

                                    <?php if ($option['type'] === 'select' && !empty($option['values'])): ?>
                                        <select
                                            id="option_<?= $option['id'] ?>"
                                            name="option_<?= $option['id'] ?>"
                                            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 <?= isset($errors["option_{$option['id']}"]) ? 'border-red-500' : 'border-gray-300' ?>"
                                        >
                                            <option value=""><?= $this->localization->t('general.select') ?></option>
                                            <?php foreach ($option['values'] as $value): ?>
                                                <option
                                                    value="<?= $value["value"] ?>"
                                                    <?= (isset($oldInput["option_{$option['id']}"]) && $oldInput["option_{$option['id']}"] === $value["value"]) ? 'selected' : '' ?>
                                                >
                                                    <?= $value["label_{$language}"] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php elseif ($option['type'] === 'radio' && !empty($option['values'])): ?>
                                        <div class="space-y-2">
                                            <?php foreach ($option['values'] as $value): ?>
                                                <div class="flex items-center">
                                                    <input
                                                        type="radio"
                                                        id="option_<?= $option['id'] ?>_<?= $value['value'] ?>"
                                                        name="option_<?= $option['id'] ?>"
                                                        value="<?= $value['value'] ?>"
                                                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                                                        <?= (isset($oldInput["option_{$option['id']}"]) && $oldInput["option_{$option['id']}"] === $value["value"]) ? 'checked' : '' ?>
                                                    >
                                                    <label for="option_<?= $option['id'] ?>_<?= $value['value'] ?>" class="<?= $marginStart ?>-2">
                                                        <?= $value["label_{$language}"] ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php elseif ($option['type'] === 'checkbox'): ?>
                                        <div class="flex items-center">
                                            <input
                                                type="checkbox"
                                                id="option_<?= $option['id'] ?>"
                                                name="option_<?= $option['id'] ?>"
                                                value="1"
                                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                                <?= (isset($oldInput["option_{$option['id']}"]) && $oldInput["option_{$option['id']}"]) ? 'checked' : '' ?>
                                            >
                                            <label for="option_<?= $option['id'] ?>" class="<?= $marginStart ?>-2">
                                                <?= $option["description_{$language}"] ?? '' ?>
                                            </label>
                                        </div>
                                    <?php elseif ($option['type'] === 'number'): ?>
                                        <input
                                            type="number"
                                            id="option_<?= $option['id'] ?>"
                                            name="option_<?= $option['id'] ?>"
                                            min="<?= $option['min'] ?? 1 ?>"
                                            max="<?= $option['max'] ?? '' ?>"
                                            value="<?= $oldInput["option_{$option['id']}"] ?? $option['default'] ?? '' ?>"
                                            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 <?= isset($errors["option_{$option['id']}"]) ? 'border-red-500' : 'border-gray-300' ?>"
                                        >
                                    <?php else: ?>
                                        <input
                                            type="text"
                                            id="option_<?= $option['id'] ?>"
                                            name="option_<?= $option['id'] ?>"
                                            value="<?= $oldInput["option_{$option['id']}"] ?? '' ?>"
                                            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 <?= isset($errors["option_{$option['id']}"]) ? 'border-red-500' : 'border-gray-300' ?>"
                                        >
                                    <?php endif; ?>

                                    <?php if (isset($errors["option_{$option['id']}"])): ?>
                                        <p class="text-red-500 text-sm mt-1"><?= $errors["option_{$option['id']}"] ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($option["description_{$language}"]) && $option['type'] !== 'checkbox'): ?>
                                        <p class="text-gray-500 text-sm mt-1"><?= $option["description_{$language}"] ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- File Upload Section -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4"><?= $this->localization->t('quotes.upload_files') ?></h3>
                        <div class="border-2 border-dashed border-gray-300 rounded-md p-6 text-center">
                            <label for="design_files" class="block cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span class="mt-2 block text-sm font-medium text-gray-700">
                                    <?= $this->localization->t('quotes.upload_instructions') ?>
                                </span>
                                <span class="mt-1 block text-xs text-gray-500">
                                    <?= $this->localization->t('quotes.upload_formats') ?>
                                </span>
                            </label>
                            <input
                                id="design_files"
                                name="design_files[]"
                                type="file"
                                multiple
                                class="hidden"
                                accept=".jpg,.jpeg,.png,.gif,.pdf,.zip,.rar"
                            >
                        </div>
                        <div id="file-list" class="mt-2"></div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4"><?= $this->localization->t('quotes.contact_info') ?></h3>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="contact_name" class="block font-medium mb-2">
                                    <?= $this->localization->t('quotes.contact_name') ?> <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="contact_name"
                                    name="contact_name"
                                    value="<?= $oldInput['contact_name'] ?? ($user ? $user['name'] : '') ?>"
                                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 <?= isset($errors['contact_name']) ? 'border-red-500' : 'border-gray-300' ?>"
                                    required
                                >
                                <?php if (isset($errors['contact_name'])): ?>
                                    <p class="text-red-500 text-sm mt-1"><?= $errors['contact_name'] ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="contact_email" class="block font-medium mb-2">
                                    <?= $this->localization->t('quotes.contact_email') ?> <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="contact_email"
                                    name="contact_email"
                                    value="<?= $oldInput['contact_email'] ?? ($user ? $user['email'] : '') ?>"
                                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 <?= isset($errors['contact_email']) ? 'border-red-500' : 'border-gray-300' ?>"
                                    required
                                >
                                <?php if (isset($errors['contact_email'])): ?>
                                    <p class="text-red-500 text-sm mt-1"><?= $errors['contact_email'] ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="contact_phone" class="block font-medium mb-2">
                                    <?= $this->localization->t('quotes.contact_phone') ?> <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="tel"
                                    id="contact_phone"
                                    name="contact_phone"
                                    value="<?= $oldInput['contact_phone'] ?? ($user && isset($user['phone']) ? $user['phone'] : '') ?>"
                                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 <?= isset($errors['contact_phone']) ? 'border-red-500' : 'border-gray-300' ?>"
                                    required
                                >
                                <?php if (isset($errors['contact_phone'])): ?>
                                    <p class="text-red-500 text-sm mt-1"><?= $errors['contact_phone'] ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="form-group md:col-span-2">
                                <label for="delivery_address" class="block font-medium mb-2">
                                    <?= $this->localization->t('quotes.delivery_address') ?>
                                </label>
                                <textarea
                                    id="delivery_address"
                                    name="delivery_address"
                                    rows="3"
                                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 border-gray-300"
                                ><?= $oldInput['delivery_address'] ?? '' ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Message Section -->
                    <div class="mb-6">
                        <label for="message" class="block font-medium mb-2">
                            <?= $this->localization->t('quotes.additional_info') ?>
                        </label>
                        <textarea
                            id="message"
                            name="message"
                            rows="4"
                            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 border-gray-300"
                        ><?= $oldInput['message'] ?? '' ?></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="px-6 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                        >
                            <?= $this->localization->t('quotes.submit_request') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// File upload preview
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('design_files');
    const fileList = document.getElementById('file-list');

    fileInput.addEventListener('change', function() {
        fileList.innerHTML = '';

        if (this.files.length > 0) {
            const fileListContainer = document.createElement('div');
            fileListContainer.className = 'mt-3 space-y-2';

            Array.from(this.files).forEach(file => {
                const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center p-2 bg-gray-50 rounded';

                // File icon based on type
                let iconSvg;
                if (file.type.startsWith('image/')) {
                    iconSvg = '<svg class="h-5 w-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>';
                } else if (file.type === 'application/pdf') {
                    iconSvg = '<svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>';
                } else {
                    iconSvg = '<svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
                }

                fileItem.innerHTML = `
                    <div class="<?= $marginEnd ?>-3">${iconSvg}</div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-sm font-medium truncate">${file.name}</p>
                        <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                    </div>
                `;

                fileListContainer.appendChild(fileItem);
            });

            fileList.appendChild(fileListContainer);
        }
    });

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>

<?php include 'views/layouts/footer.php'; ?>
