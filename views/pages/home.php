<?php
/**
 * Homepage View
 * File path: views/pages/home.php
 *
 * @package Egypt Printing Services Marketplace
 * @author  Development Team
 */

// Set page title
$pageTitle = $this->localization->t('general.home_page_title');
?>

<?php include VIEWS_PATH . '/components/header.php'; ?>
<!-- Hero Section -->
<section class="relative bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="w-full md:w-1/2 mb-8 md:mb-0">
                <h1 class="text-4xl lg:text-5xl font-bold mb-4">
                    <?= $this->localization->t('home.hero_title') ?>
                </h1>
                <p class="text-xl mb-6">
                    <?= $this->localization->t('home.hero_subtitle') ?>
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="/services" class="px-6 py-3 bg-white text-blue-600 rounded-md font-semibold hover:bg-blue-50 transition">
                        <?= $this->localization->t('home.browse_services') ?>
                    </a>
                    <a href="/register?role=vendor" class="px-6 py-3 bg-transparent border-2 border-white rounded-md font-semibold hover:bg-white hover:text-blue-600 transition">
                        <?= $this->localization->t('home.join_as_vendor') ?>
                    </a>
                </div>
            </div>
            <div class="w-full md:w-1/2">
                <img src="/assets/images/hero-image.svg" alt="Print Services" class="w-full max-w-md mx-auto">
            </div>
        </div>
    </div>
    <!-- Wave Shape Divider -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" fill="#ffffff">
            <path d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,42.7C1120,32,1280,32,1360,32L1440,32L1440,100L1360,100C1280,100,1120,100,960,100C800,100,640,100,480,100C320,100,160,100,80,100L0,100Z"></path>
        </svg>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12"><?= $this->localization->t('home.how_it_works') ?></h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Step 1 -->
            <div class="flex flex-col items-center text-center">
                <div class="bg-blue-100 text-blue-600 rounded-full w-16 h-16 flex items-center justify-center mb-4">
                    <span class="text-2xl font-bold">1</span>
                </div>
                <h3 class="text-xl font-semibold mb-2"><?= $this->localization->t('home.step_1_title') ?></h3>
                <p class="text-gray-600"><?= $this->localization->t('home.step_1_desc') ?></p>
            </div>

            <!-- Step 2 -->
            <div class="flex flex-col items-center text-center">
                <div class="bg-blue-100 text-blue-600 rounded-full w-16 h-16 flex items-center justify-center mb-4">
                    <span class="text-2xl font-bold">2</span>
                </div>
                <h3 class="text-xl font-semibold mb-2"><?= $this->localization->t('home.step_2_title') ?></h3>
                <p class="text-gray-600"><?= $this->localization->t('home.step_2_desc') ?></p>
            </div>

            <!-- Step 3 -->
            <div class="flex flex-col items-center text-center">
                <div class="bg-blue-100 text-blue-600 rounded-full w-16 h-16 flex items-center justify-center mb-4">
                    <span class="text-2xl font-bold">3</span>
                </div>
                <h3 class="text-xl font-semibold mb-2"><?= $this->localization->t('home.step_3_title') ?></h3>
                <p class="text-gray-600"><?= $this->localization->t('home.step_3_desc') ?></p>
            </div>
        </div>

        <div class="text-center mt-12">
            <a href="/how-it-works" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-800">
                <?= $this->localization->t('home.learn_more') ?>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Featured Categories Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12"><?= $this->localization->t('home.featured_categories') ?></h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            // This would normally be populated from the database
            $categories = [
                [
                    'name' => $this->localization->t('categories.business_cards'),
                    'icon' => 'business-card.svg',
                    'slug' => 'business-cards'
                ],
                [
                    'name' => $this->localization->t('categories.banners'),
                    'icon' => 'banner.svg',
                    'slug' => 'banners'
                ],
                [
                    'name' => $this->localization->t('categories.brochures'),
                    'icon' => 'brochure.svg',
                    'slug' => 'brochures'
                ],
                [
                    'name' => $this->localization->t('categories.promotional'),
                    'icon' => 'promotional.svg',
                    'slug' => 'promotional-items'
                ],
                [
                    'name' => $this->localization->t('categories.packaging'),
                    'icon' => 'packaging.svg',
                    'slug' => 'packaging'
                ],
                [
                    'name' => $this->localization->t('categories.stickers'),
                    'icon' => 'sticker.svg',
                    'slug' => 'stickers'
                ],
                [
                    'name' => $this->localization->t('categories.apparel'),
                    'icon' => 'tshirt.svg',
                    'slug' => 'apparel'
                ],
                [
                    'name' => $this->localization->t('categories.posters'),
                    'icon' => 'poster.svg',
                    'slug' => 'posters'
                ]
            ];

            foreach ($categories as $category):
            ?>
            <a href="/services/category/<?= $category['slug'] ?>" class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center hover:shadow-lg transition">
                <img src="/assets/images/categories/<?= $category['icon'] ?>" alt="<?= $category['name'] ?>" class="w-16 h-16 mb-3">
                <h3 class="text-lg font-semibold text-center"><?= $category['name'] ?></h3>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-8">
            <a href="/services" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition">
                <?= $this->localization->t('home.view_all_categories') ?>
            </a>
        </div>
    </div>
</section>

<!-- Featured Vendors Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12"><?= $this->localization->t('home.featured_vendors') ?></h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            // This would normally be populated from the database
            $vendors = [
                [
                    'name' => 'PrintPro Egypt',
                    'logo' => 'vendor1.png',
                    'rating' => 4.8,
                    'services' => 12,
                    'slug' => 'printpro-egypt'
                ],
                [
                    'name' => 'Cairo Graphics',
                    'logo' => 'vendor2.png',
                    'rating' => 4.6,
                    'services' => 8,
                    'slug' => 'cairo-graphics'
                ],
                [
                    'name' => 'Alexandria Press',
                    'logo' => 'vendor3.png',
                    'rating' => 4.9,
                    'services' => 15,
                    'slug' => 'alexandria-press'
                ]
            ];

            foreach ($vendors as $vendor):
            ?>
            <a href="/vendors/<?= $vendor['slug'] ?>" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                <div class="flex items-center mb-4">
                    <img src="/assets/images/vendors/<?= $vendor['logo'] ?>" alt="<?= $vendor['name'] ?>" class="w-16 h-16 object-contain mr-4">
                    <div>
                        <h3 class="text-lg font-semibold"><?= $vendor['name'] ?></h3>
                        <div class="flex items-center text-yellow-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="ml-1 text-gray-700"><?= $vendor['rating'] ?></span>
                        </div>
                    </div>
                </div>
                <p class="text-gray-600"><?= sprintf($this->localization->t('home.vendor_services_count'), $vendor['services']) ?></p>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-8">
            <a href="/vendors" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition">
                <?= $this->localization->t('home.view_all_vendors') ?>
            </a>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12"><?= $this->localization->t('home.testimonials') ?></h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            // This would normally be populated from the database
            $testimonials = [
                [
                    'name' => 'Ahmed Mohamed',
                    'company' => 'StartUp Hub',
                    'text' => $this->localization->t('home.testimonial_1'),
                    'image' => 'testimonial1.jpg'
                ],
                [
                    'name' => 'Sara Ali',
                    'company' => 'Design Agency',
                    'text' => $this->localization->t('home.testimonial_2'),
                    'image' => 'testimonial2.jpg'
                ],
                [
                    'name' => 'Omar Khaled',
                    'company' => 'Marketing Experts',
                    'text' => $this->localization->t('home.testimonial_3'),
                    'image' => 'testimonial3.jpg'
                ]
            ];

            foreach ($testimonials as $testimonial):
            ?>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center mb-4">
                    <img src="/assets/images/testimonials/<?= $testimonial['image'] ?>" alt="<?= $testimonial['name'] ?>" class="w-12 h-12 rounded-full object-cover mr-4">
                    <div>
                        <h3 class="font-semibold"><?= $testimonial['name'] ?></h3>
                        <p class="text-gray-600 text-sm"><?= $testimonial['company'] ?></p>
                    </div>
                </div>
                <p class="text-gray-700">"<?= $testimonial['text'] ?>"</p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="py-16 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-6"><?= $this->localization->t('home.cta_title') ?></h2>
        <p class="text-xl max-w-3xl mx-auto mb-8"><?= $this->localization->t('home.cta_subtitle') ?></p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="/register" class="px-6 py-3 bg-white text-blue-600 rounded-md font-semibold hover:bg-blue-50 transition">
                <?= $this->localization->t('home.get_started') ?>
            </a>
            <a href="/contact" class="px-6 py-3 bg-transparent border-2 border-white rounded-md font-semibold hover:bg-white hover:text-blue-600 transition">
                <?= $this->localization->t('home.contact_us') ?>
            </a>
        </div>
    </div>
</section>

<?php include VIEWS_PATH . '/components/footer.php'; ?>