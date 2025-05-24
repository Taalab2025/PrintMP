<?php
/**
 * Vendor Profile Page
 *
 * File path: views/pages/vendors/profile.php
 *
 * Displays detailed information about a single vendor with their services
 */

// Check if vendor is available
if (empty($viewData['vendor'])) {
    header('Location: /vendors');
    exit;
}

$vendor = $viewData['vendor'];
$services = $viewData['services'] ?? [];
$reviews = $viewData['reviews'] ?? [];

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
            <a href="/vendors" class="hover:underline"><?= $this->localization->t('vendors.all_vendors') ?></a>
            <i class="fas <?= $isRtl ? 'fa-chevron-left' : 'fa-chevron-right' ?> text-xs mx-2"></i>
            <span class="text-gray-900"><?= htmlspecialchars($vendor['company_name']) ?></span>
        </div>

        <!-- Vendor profile header -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
            <div class="relative bg-blue-600 h-32 md:h-48">
                <!-- Cover image or background pattern could be added here -->
                <?php if (!empty($vendor['cover_image'])): ?>
                    <img
                        src="<?= htmlspecialchars($vendor['cover_image']) ?>"
                        alt="<?= htmlspecialchars($vendor['company_name']) ?> cover"
                        class="w-full h-full object-cover"
                    >
                <?php endif; ?>
            </div>

            <div class="px-6 py-6 md:px-8 md:py-8 relative">
                <!-- Logo/Profile Image -->
                <div class="absolute -top-16 <?= $isRtl ? 'right-8' : 'left-8' ?> bg-white p-1 rounded-full shadow-lg">
                    <?php if (!empty($vendor['logo'])): ?>
                        <img
                            src="<?= htmlspecialchars($vendor['logo']) ?>"
                            alt="<?= htmlspecialchars($vendor['company_name']) ?>"
                            class="w-24 h-24 md:w-32 md:h-32 rounded-full object-cover border-4 border-white"
                        >
                    <?php else: ?>
                        <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-gray-200 flex items-center justify-center border-4 border-white">
                            <i class="fas fa-store text-gray-400 text-4xl"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Vendor info -->
                <div class="<?= $isRtl ? 'pr-36 md:pr-44' : 'pl-36 md:pl-44' ?>">
                    <div class="flex flex-wrap items-center justify-between mb-4">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 <?= $textAlign ?>"><?= htmlspecialchars($vendor['company_name']) ?></h1>

                            <?php if (isset($vendor['rating'])): ?>
                                <div class="flex items-center mt-2">
                                    <div class="flex text-yellow-400">
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
                                    <span class="text-gray-600 text-sm <?= $marginStart ?>-2">
                                        <?= number_format($vendor['rating'], 1) ?>
                                        (<?= isset($vendor['review_count']) ? $vendor['review_count'] : 0 ?> <?= $this->localization->t('vendors.reviews') ?>)
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- CTA buttons -->
                        <div class="mt-4 md:mt-0">
                            <a
                                href="/services?vendor=<?= htmlspecialchars($vendor['id']) ?>"
                                class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm"
                            >
                                <?= $this->localization->t('vendors.view_all_services') ?>
                            </a>

                            <?php if (isset($vendor['email'])): ?>
                                <a
                                    href="mailto:<?= htmlspecialchars($vendor['email']) ?>"
                                    class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors text-sm <?= $marginStart ?>-2"
                                >
                                    <i class="fas fa-envelope <?= $marginEnd ?>-1"></i>
                                    <?= $this->localization->t('vendors.contact') ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Vendor info blocks -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                        <!-- Location -->
                        <?php if (!empty($vendor['location'])): ?>
                            <div class="flex items-start <?= $textAlign ?>">
                                <div class="<?= $paddingEnd ?>-4 text-blue-500">
                                    <i class="fas fa-map-marker-alt text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900"><?= $this->localization->t('vendors.location') ?></h3>
                                    <p class="text-gray-600"><?= htmlspecialchars($vendor['location']) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Phone -->
                        <?php if (!empty($vendor['phone'])): ?>
                            <div class="flex items-start <?= $textAlign ?>">
                                <div class="<?= $paddingEnd ?>-4 text-blue-500">
                                    <i class="fas fa-phone text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900"><?= $this->localization->t('vendors.phone') ?></h3>
                                    <p class="text-gray-600"><?= htmlspecialchars($vendor['phone']) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Service count -->
                        <?php if (isset($vendor['service_count'])): ?>
                            <div class="flex items-start <?= $textAlign ?>">
                                <div class="<?= $paddingEnd ?>-4 text-blue-500">
                                    <i class="fas fa-print text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900"><?= $this->localization->t('vendors.services_offered') ?></h3>
                                    <p class="text-gray-600">
                                        <?= $vendor['service_count'] ?> <?= $this->localization->t('vendors.services') ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($vendor['description'])): ?>
                <div class="px-8 py-6 border-t border-gray-100">
                    <h2 class="text-xl font-semibold mb-4 <?= $textAlign ?>"><?= $this->localization->t('vendors.about') ?></h2>
                    <div class="prose prose-blue max-w-none <?= $textAlign ?>">
                        <?= nl2br(htmlspecialchars($vendor['description'])) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Services section -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900 <?= $textAlign ?>"><?= $this->localization->t('vendors.services') ?></h2>

                <?php if (!empty($services) && count($services) > 6): ?>
                    <a
                        href="/services?vendor=<?= htmlspecialchars($vendor['id']) ?>"
                        class="text-blue-600 hover:underline flex items-center"
                    >
                        <?= $this->localization->t('vendors.view_all') ?>
                        <i class="fas <?= $isRtl ? 'fa-arrow-left' : 'fa-arrow-right' ?> ml-1 text-xs"></i>
                    </a>
                <?php endif; ?>
            </div>

            <?php if (!empty($services)): ?>
                <div class="flex flex-wrap -mx-3">
                    <?php foreach ($services as $service): ?>
                        <?php
                            // Include the service card component with medium size
                            $size = 'medium';
                            include 'views/components/service-card.php';
                        ?>
                    <?php endforeach; ?>
                </div>

                <?php if (count($services) > 6): ?>
                    <div class="text-center mt-6">
                        <a
                            href="/services?vendor=<?= htmlspecialchars($vendor['id']) ?>"
                            class="inline-block bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300 transition-colors"
                        >
                            <?= $this->localization->t('vendors.view_all_services') ?>
                        </a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-clipboard-list fa-3x"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">
                        <?= $this->localization->t('vendors.no_services') ?>
                    </h3>
                    <p class="text-gray-600">
                        <?= $this->localization->t('vendors.no_services_message') ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Reviews section -->
        <div>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900 <?= $textAlign ?>">
                    <?= $this->localization->t('vendors.reviews') ?>
                    <?php if (isset($vendor['review_count']) && $vendor['review_count'] > 0): ?>
                        <span class="text-gray-500 text-lg font-normal">(<?= $vendor['review_count'] ?>)</span>
                    <?php endif; ?>
                </h2>
            </div>

            <?php if (!empty($reviews)): ?>
                <div class="space-y-4">
                    <?php foreach ($reviews as $review): ?>
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center <?= $marginEnd ?>-3">
                                        <i class="fas fa-user text-gray-500"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900"><?= htmlspecialchars($review['user_name']) ?></h4>
                                        <div class="text-sm text-gray-500"><?= date('F j, Y', strtotime($review['created_at'])) ?></div>
                                    </div>
                                </div>

                                <div class="flex text-yellow-400">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($review['rating'] >= $i) {
                                            echo '<i class="fas fa-star"></i>';
                                        } else {
                                            echo '<i class="far fa-star"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>

                            <?php
                            // Get service title in current language
                            $serviceTitle = $this->localization->getCurrentLanguage() === 'ar' && isset($review['service_title_ar'])
                                ? $review['service_title_ar']
                                : $review['service_title_en'];
                            ?>

                            <?php if (!empty($serviceTitle)): ?>
                                <div class="bg-gray-50 px-3 py-1 rounded text-sm text-gray-600 mb-3 inline-block <?= $textAlign ?>">
                                    <i class="fas fa-print <?= $marginEnd ?>-1"></i>
                                    <?= htmlspecialchars($serviceTitle) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($review['comment'])): ?>
                                <div class="text-gray-700 <?= $textAlign ?>">
                                    <?= nl2br(htmlspecialchars($review['comment'])) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($review['vendor_response'])): ?>
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <div class="bg-blue-50 rounded-lg p-4 <?= $textAlign ?>">
                                        <div class="text-sm font-medium text-blue-800 mb-2">
                                            <i class="fas fa-reply <?= $marginEnd ?>-1"></i>
                                            <?= $this->localization->t('vendors.vendor_response') ?>
                                        </div>
                                        <div class="text-blue-800">
                                            <?= nl2br(htmlspecialchars($review['vendor_response'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (isset($vendor['review_count']) && $vendor['review_count'] > count($reviews)): ?>
                    <div class="text-center mt-6">
                        <a
                            href="#"
                            class="inline-block bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300 transition-colors"
                        >
                            <?= $this->localization->t('vendors.view_all_reviews') ?>
                        </a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-star fa-3x"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">
                        <?= $this->localization->t('vendors.no_reviews') ?>
                    </h3>
                    <p class="text-gray-600">
                        <?= $this->localization->t('vendors.no_reviews_message') ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
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
