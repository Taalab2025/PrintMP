<?php
/**
 * Service Card Component
 *
 * File path: views/components/service-card.php
 *
 * A reusable component to display service information in a card format
 */

/**
 * Expected variables:
 * $service - Service data array containing:
 *   - id: Service ID
 *   - title: Service title in current language
 *   - slug: Service slug for URL
 *   - base_price: Base price
 *   - main_image: Main image path
 *   - category_name: Category name in current language
 *   - vendor_name: Vendor name in current language
 * $size - Card size: 'small', 'medium' (default), or 'large'
 */

// Default size if not provided
$size = isset($size) ? $size : 'medium';

// Set CSS classes based on size
switch ($size) {
    case 'small':
        $cardClass = 'w-full sm:w-1/2 md:w-1/3 lg:w-1/4 p-2';
        $imageHeight = 'h-40';
        $titleClass = 'text-base';
        $detailsVisible = false;
        break;
    case 'large':
        $cardClass = 'w-full sm:w-full md:w-full lg:w-full p-3';
        $imageHeight = 'h-64';
        $titleClass = 'text-xl';
        $detailsVisible = true;
        break;
    case 'medium':
    default:
        $cardClass = 'w-full sm:w-1/2 md:w-1/3 p-3';
        $imageHeight = 'h-48';
        $titleClass = 'text-lg';
        $detailsVisible = true;
}

// Default image if not available
$imagePath = !empty($service['main_image']) ? $service['main_image'] : '/assets/images/placeholder-service.jpg';

// RTL support
$isRtl = isset($isRtl) ? $isRtl : false;
$textAlign = $isRtl ? 'text-right' : 'text-left';
$dirAttr = $isRtl ? 'dir="rtl"' : '';
?>

<div class="<?= $cardClass ?>">
    <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:shadow-lg hover:-translate-y-1 h-full flex flex-col">
        <!-- Service Image -->
        <a href="/services/<?= htmlspecialchars($service['slug']) ?>" class="block relative">
            <div class="<?= $imageHeight ?> bg-gray-200 overflow-hidden">
                <img
                    src="<?= htmlspecialchars($imagePath) ?>"
                    alt="<?= htmlspecialchars($service['title']) ?>"
                    class="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
                >
            </div>
            <?php if (isset($service['is_featured']) && $service['is_featured']): ?>
                <div class="absolute top-2 <?= $isRtl ? 'right-2' : 'left-2' ?> bg-yellow-500 text-white px-2 py-1 text-xs font-semibold rounded">
                    <?= $this->localization->t('services.featured') ?>
                </div>
            <?php endif; ?>
        </a>

        <!-- Card Body -->
        <div class="p-4 flex-grow flex flex-col" <?= $dirAttr ?>>
            <!-- Category -->
            <?php if (isset($service['category_name'])): ?>
                <div class="text-sm text-gray-500 mb-1 <?= $textAlign ?>">
                    <a href="/categories/<?= isset($service['category_slug']) ? htmlspecialchars($service['category_slug']) : '#' ?>" class="hover:underline">
                        <?= htmlspecialchars($service['category_name']) ?>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Title -->
            <h3 class="<?= $titleClass ?> font-semibold mb-2 <?= $textAlign ?>">
                <a href="/services/<?= htmlspecialchars($service['slug']) ?>" class="text-gray-800 hover:text-blue-600 transition-colors">
                    <?= htmlspecialchars($service['title']) ?>
                </a>
            </h3>

            <!-- Details -->
            <?php if ($detailsVisible && isset($service['vendor_name'])): ?>
                <div class="text-sm text-gray-600 mb-3 <?= $textAlign ?>">
                    <?= $this->localization->t('services.by') ?>
                    <a href="/vendors/<?= isset($service['vendor_id']) ? htmlspecialchars($service['vendor_id']) : '#' ?>" class="hover:underline">
                        <?= htmlspecialchars($service['vendor_name']) ?>
                    </a>
                </div>
            <?php endif; ?>

            <?php if ($size === 'large' && isset($service['description'])): ?>
                <div class="text-sm text-gray-600 mb-3 <?= $textAlign ?>">
                    <?= substr(htmlspecialchars(strip_tags($service['description'])), 0, 150) ?>...
                </div>
            <?php endif; ?>

            <!-- Price and Action -->
            <div class="mt-auto flex items-center justify-between pt-2 border-t border-gray-100">
                <div class="font-semibold <?= $isRtl ? 'text-right' : 'text-left' ?>">
                    <?= number_format($service['base_price'], 2) ?> <?= $this->localization->t('general.currency') ?>
                    <?php if (isset($service['min_order_qty']) && $service['min_order_qty'] > 1): ?>
                        <span class="text-xs text-gray-500">
                            / <?= $this->localization->t('services.min_qty', ['qty' => $service['min_order_qty']]) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <a href="/services/<?= htmlspecialchars($service['slug']) ?>" class="text-sm text-blue-600 hover:text-blue-800 transition-colors">
                    <?= $this->localization->t('services.view_details') ?>
                    <i class="fas <?= $isRtl ? 'fa-arrow-left' : 'fa-arrow-right' ?> ml-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
