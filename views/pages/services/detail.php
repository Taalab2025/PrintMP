<?php
/**
 * Service Detail Page
 *
 * File path: views/pages/services/detail.php
 *
 * Displays detailed information about a single service with quote request option
 */

// Check if service is available
if (empty($viewData['service'])) {
    header('Location: /services');
    exit;
}

$service = $viewData['service'];
$vendor = $viewData['vendor'] ?? null;

// Check if it's an RTL language
$isRtl = $this->localization->isRtl();
$textAlign = $isRtl ? 'text-right' : 'text-left';
$floatDir = $isRtl ? 'float-right' : 'float-left';
$marginStart = $isRtl ? 'mr' : 'ml';
$marginEnd = $isRtl ? 'ml' : 'mr';
$paddingStart = $isRtl ? 'pr' : 'pl';
$paddingEnd = $isRtl ? 'pl' : 'pr';
?>

<div class="bg-gray-100 py-8">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <div class="flex items-center text-sm text-gray-600 mb-6 <?= $textAlign ?>">
            <a href="/" class="hover:underline"><?= $this->localization->t('general.home') ?></a>
            <i class="fas <?= $isRtl ? 'fa-chevron-left' : 'fa-chevron-right' ?> text-xs mx-2"></i>
            <a href="/services" class="hover:underline"><?= $this->localization->t('services.all_services') ?></a>

            <?php if (isset($service['category_name']) && isset($service['category_slug'])): ?>
                <i class="fas <?= $isRtl ? 'fa-chevron-left' : 'fa-chevron-right' ?> text-xs mx-2"></i>
                <a href="/services?category=<?= htmlspecialchars($service['category_slug']) ?>" class="hover:underline">
                    <?= htmlspecialchars($service['category_name']) ?>
                </a>
            <?php endif; ?>

            <i class="fas <?= $isRtl ? 'fa-chevron-left' : 'fa-chevron-right' ?> text-xs mx-2"></i>
            <span class="text-gray-900"><?= htmlspecialchars($service['title']) ?></span>
        </div>

        <!-- Service details layout -->
        <div class="flex flex-wrap -mx-4">
            <!-- Left column - Media and details -->
            <div class="w-full lg:w-8/12 px-4 mb-8 lg:mb-0">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <!-- Image gallery -->
                    <div class="relative">
                        <?php if (!empty($service['media']) && count($service['media']) > 0): ?>
                            <!-- Main image -->
                            <div id="mainImage" class="h-96 bg-gray-200">
                                <?php
                                $mainImage = null;
                                foreach ($service['media'] as $media) {
                                    if ($media['type'] === 'main') {
                                        $mainImage = $media;
                                        break;
                                    }
                                }

                                if (!$mainImage && !empty($service['media'])) {
                                    $mainImage = $service['media'][0];
                                }

                                $mainImagePath = $mainImage ? $mainImage['file_path'] : '/assets/images/placeholder-service.jpg';
                                ?>
                                <img
                                    src="<?= htmlspecialchars($mainImagePath) ?>"
                                    alt="<?= htmlspecialchars($service['title']) ?>"
                                    class="w-full h-full object-contain"
                                >
                            </div>

                            <!-- Thumbnails -->
                            <?php if (count($service['media']) > 1): ?>
                                <div class="mt-4 flex overflow-x-auto p-2 space-x-2 hide-scrollbar">
                                    <?php foreach ($service['media'] as $index => $media): ?>
                                        <div
                                            class="w-24 h-24 flex-shrink-0 rounded overflow-hidden cursor-pointer border-2 <?= ($media === $mainImage) ? 'border-blue-500' : 'border-transparent' ?>"
                                            onclick="changeMainImage('<?= htmlspecialchars($media['file_path']) ?>', this)"
                                        >
                                            <img
                                                src="<?= htmlspecialchars($media['file_path']) ?>"
                                                alt="<?= htmlspecialchars($service['title']) ?> - <?= $index + 1 ?>"
                                                class="w-full h-full object-cover"
                                            >
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Placeholder image if no media -->
                            <div class="h-96 bg-gray-200 flex items-center justify-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-image fa-4x mb-4"></i>
                                    <p><?= $this->localization->t('services.no_images_available') ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Featured badge if applicable -->
                        <?php if (isset($service['is_featured']) && $service['is_featured']): ?>
                            <div class="absolute top-4 <?= $isRtl ? 'right-4' : 'left-4' ?> bg-yellow-500 text-white px-3 py-1 text-sm font-semibold rounded">
                                <?= $this->localization->t('services.featured') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Service details -->
                    <div class="p-6">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2 <?= $textAlign ?>"><?= htmlspecialchars($service['title']) ?></h1>

                        <!-- Category and vendor -->
                        <div class="flex flex-wrap items-center text-sm text-gray-600 mb-4 <?= $textAlign ?>">
                            <?php if (isset($service['category_name'])): ?>
                                <a href="/services?category=<?= htmlspecialchars($service['category_slug']) ?>" class="mr-4 hover:underline flex items-center">
                                    <i class="fas fa-tag <?= $marginEnd ?>-1"></i>
                                    <?= htmlspecialchars($service['category_name']) ?>
                                </a>
                            <?php endif; ?>

                            <?php if (isset($service['vendor_name'])): ?>
                                <a href="/vendors/<?= htmlspecialchars($service['vendor_id']) ?>" class="hover:underline flex items-center">
                                    <i class="fas fa-store <?= $marginEnd ?>-1"></i>
                                    <?= htmlspecialchars($service['vendor_name']) ?>

                                    <?php if (isset($service['vendor_rating'])): ?>
                                        <div class="flex items-center <?= $marginStart ?>-2">
                                            <div class="text-yellow-400">
                                                <?php
                                                $rating = round($service['vendor_rating'] * 2) / 2; // Round to nearest 0.5
                                                for ($i = 1; $i <= 5; $i++) {
                                                    if ($rating >= $i) {
                                                        echo '<i class="fas fa-star"></i>';
                                                    } else if ($rating >= $i - 0.5) {
                                                        echo '<i class="fas fa-star-half-alt"></i>';
                                                    } else {
                                                        echo '<i class="far fa-star"></i>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <span class="<?= $marginStart ?>-1">(<?= number_format($service['vendor_rating'], 1) ?>)</span>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- Base price and service details -->
                        <div class="flex flex-wrap items-center mb-6">
                            <div class="mr-6 mb-2">
                                <span class="text-gray-600 text-sm"><?= $this->localization->t('services.base_price') ?>:</span>
                                <span class="text-2xl font-bold text-gray-900 <?= $marginStart ?>-1">
                                    <?= number_format($service['base_price'], 2) ?> <?= $this->localization->t('general.currency') ?>
                                </span>
                            </div>

                            <?php if (isset($service['min_order_qty']) && $service['min_order_qty'] > 1): ?>
                                <div class="mr-6 mb-2">
                                    <span class="text-gray-600 text-sm"><?= $this->localization->t('services.min_order') ?>:</span>
                                    <span class="font-medium text-gray-900 <?= $marginStart ?>-1">
                                        <?= $service['min_order_qty'] ?> <?= $this->localization->t('services.units') ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($service['production_time'])): ?>
                                <div class="mb-2">
                                    <span class="text-gray-600 text-sm"><?= $this->localization->t('services.production_time') ?>:</span>
                                    <span class="font-medium text-gray-900 <?= $marginStart ?>-1">
                                        <?= $service['production_time'] ?> <?= $this->localization->t('services.days') ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Description -->
                        <div class="prose prose-blue max-w-none mb-8 <?= $textAlign ?>">
                            <?= nl2br(htmlspecialchars($service['description'])) ?>
                        </div>

                        <!-- Service options -->
                        <?php if (!empty($service['options'])): ?>
                            <div class="mb-8">
                                <h3 class="text-xl font-semibold mb-4 <?= $textAlign ?>"><?= $this->localization->t('services.available_options') ?></h3>

                                <div class="space-y-4">
                                    <?php foreach ($service['options'] as $option): ?>
                                        <div class="border rounded-lg p-4">
                                            <h4 class="font-medium mb-2 <?= $textAlign ?>">
                                                <?= htmlspecialchars($option['name']) ?>
                                                <?php if (isset($option['required']) && $option['required']): ?>
                                                    <span class="text-red-500">*</span>
                                                <?php endif; ?>
                                            </h4>

                                            <?php if (isset($option['values']) && !empty($option['values'])): ?>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                    <?php foreach ($option['values'] as $value): ?>
                                                        <div class="flex items-center justify-between bg-gray-50 p-2 rounded <?= $textAlign ?>">
                                                            <span><?= htmlspecialchars($value['label']) ?></span>

                                                            <?php if (isset($value['price']) && $value['price'] > 0): ?>
                                                                <span class="text-blue-600 font-medium">
                                                                    +<?= number_format($value['price'], 2) ?> <?= $this->localization->t('general.currency') ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- CTA buttons -->
                        <div class="flex flex-wrap space-x-4">
                            <a
                                href="/quote-request/<?= htmlspecialchars($service['id']) ?>"
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors"
                            >
                                <?= $this->localization->t('services.request_quote') ?>
                            </a>

                            <a
                                href="/vendors/<?= htmlspecialchars($service['vendor_id']) ?>"
                                class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-medium hover:bg-gray-300 transition-colors"
                            >
                                <?= $this->localization->t('services.vendor_profile') ?>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Related services -->
                <?php if (isset($service['related_services']) && !empty($service['related_services'])): ?>
                    <div class="mt-8">
                        <h3 class="text-xl font-semibold mb-4 <?= $textAlign ?>"><?= $this->localization->t('services.related_services') ?></h3>

                        <div class="flex flex-wrap -mx-2">
                            <?php foreach ($service['related_services'] as $relatedService): ?>
                                <?php
                                    // Convert related service to format expected by service card
                                    $relatedService['slug'] = isset($relatedService['slug']) ? $relatedService['slug'] : $relatedService['id'];

                                    // Include the service card component with small size
                                    $size = 'small';
                                    include 'views/components/service-card.php';
                                ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right column - Vendor info and quote request button -->
            <div class="w-full lg:w-4/12 px-4">
                <!-- Vendor card -->
                <?php if ($vendor): ?>
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <div class="flex items-center mb-4">
                            <?php if (!empty($vendor['logo'])): ?>
                                <img
                                    src="<?= htmlspecialchars($vendor['logo']) ?>"
                                    alt="<?= htmlspecialchars($vendor['company_name']) ?>"
                                    class="w-16 h-16 rounded-full object-cover <?= $marginEnd ?>-3"
                                >
                            <?php else: ?>
                                <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center <?= $marginEnd ?>-3">
                                    <i class="fas fa-store text-gray-500 text-2xl"></i>
                                </div>
                            <?php endif; ?>

                            <div>
                                <h3 class="font-semibold text-lg"><?= htmlspecialchars($vendor['company_name']) ?></h3>

                                <?php if (isset($vendor['rating'])): ?>
                                    <div class="flex items-center">
                                        <div class="text-yellow-400">
                                            <?php
                                            $rating = round($vendor['rating'] * 2) / 2; // Round to nearest 0.5
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($rating >= $i) {
                                                    echo '<i class="fas fa-star"></i>';
                                                } else if ($rating >= $i - 0.5) {
                                                    echo '<i class="fas fa-star-half-alt"></i>';
                                                } else {
                                                    echo '<i class="far fa-star"></i>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <span class="text-gray-600 text-sm <?= $marginStart ?>-1">(<?= number_format($vendor['rating'], 1) ?>)</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($vendor['description'])): ?>
                            <div class="text-gray-600 mb-4 <?= $textAlign ?>">
                                <?= substr(htmlspecialchars($vendor['description']), 0, 200) ?>
                                <?php if (strlen($vendor['description']) > 200): ?>...<?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="space-y-2 mb-4">
                            <?php if (!empty($vendor['location'])): ?>
                                <div class="flex items-center text-gray-600 <?= $textAlign ?>">
                                    <i class="fas fa-map-marker-alt w-5 text-gray-400 <?= $marginEnd ?>-2"></i>
                                    <?= htmlspecialchars($vendor['location']) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($vendor['phone'])): ?>
                                <div class="flex items-center text-gray-600 <?= $textAlign ?>">
                                    <i class="fas fa-phone w-5 text-gray-400 <?= $marginEnd ?>-2"></i>
                                    <?= htmlspecialchars($vendor['phone']) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($vendor['email'])): ?>
                                <div class="flex items-center text-gray-600 <?= $textAlign ?>">
                                    <i class="fas fa-envelope w-5 text-gray-400 <?= $marginEnd ?>-2"></i>
                                    <?= htmlspecialchars($vendor['email']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <a
                            href="/vendors/<?= htmlspecialchars($vendor['id']) ?>"
                            class="block w-full text-center bg-gray-200 text-gray-800 py-2 rounded font-medium hover:bg-gray-300 transition-colors"
                        >
                            <?= $this->localization->t('services.view_all_vendor_services') ?>
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Quick quote request card -->
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                    <h3 class="text-xl font-semibold mb-4 <?= $textAlign ?>"><?= $this->localization->t('services.quick_quote') ?></h3>

                    <div class="text-gray-600 mb-4 <?= $textAlign ?>">
                        <?= $this->localization->t('services.quick_quote_description') ?>
                    </div>

                    <a
                        href="/quote-request/<?= htmlspecialchars($service['id']) ?>"
                        class="block w-full text-center bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors"
                    >
                        <?= $this->localization->t('services.request_quote') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to change the main image
    function changeMainImage(imagePath, thumbnail) {
        // Update main image
        document.getElementById('mainImage').innerHTML = `
            <img src="${imagePath}" alt="<?= htmlspecialchars($service['title']) ?>" class="w-full h-full object-contain">
        `;

        // Update active thumbnail border
        const thumbnails = document.querySelectorAll('.thumbnails div');
        thumbnails.forEach(thumb => {
            thumb.classList.remove('border-blue-500');
            thumb.classList.add('border-transparent');
        });

        thumbnail.classList.remove('border-transparent');
        thumbnail.classList.add('border-blue-500');
    }
</script>

<style>
    /* Hide scrollbar but allow scrolling */
    .hide-scrollbar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }

    .hide-scrollbar::-webkit-scrollbar {
        display: none;  /* Chrome, Safari, Opera */
    }

    /* Prose styles for description */
    .prose {
        line-height: 1.7;
    }

    .prose p {
        margin-bottom: 1rem;
    }

    .prose ul, .prose ol {
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }

    .prose ul {
        list-style-type: disc;
    }

    .prose ol {
        list-style-type: decimal;
    }
</style>
