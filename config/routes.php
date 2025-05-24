<?php
/**
 * Routes Configuration
 * Egypt Printing Services Marketplace
 */

$router = App::getInstance()->getRouter();

// Public routes
$router->get('/', 'Home', 'index');
$router->get('/about', 'Home', 'about');
$router->get('/contact', 'Home', 'contact');
$router->post('/contact', 'Home', 'submitContact');

// Language routes
$router->get('/language/{lang}', 'Home', 'setLanguage');

// Auth routes
$router->get('/login', 'Auth', 'loginForm');
$router->post('/login', 'Auth', 'login');
$router->get('/logout', 'Auth', 'logout');
$router->get('/register', 'Auth', 'registerForm');
$router->post('/register', 'Auth', 'register');
$router->get('/forgot-password', 'Auth', 'forgotPasswordForm');
$router->post('/forgot-password', 'Auth', 'forgotPassword');
$router->get('/reset-password/{token}', 'Auth', 'resetPasswordForm');
$router->post('/reset-password', 'Auth', 'resetPassword');
$router->get('/verify-email/{token}', 'Auth', 'verifyEmail');

// Service routes
$router->get('/services', 'Service', 'index');
$router->get('/services/category/{id}', 'Service', 'category');
$router->get('/services/{id}', 'Service', 'show');
$router->get('/search', 'Service', 'search');

// Vendor public routes
$router->get('/vendors', 'Vendor', 'index');
$router->get('/vendors/{id}', 'Vendor', 'show');

// Quote routes
$router->get('/quote-request/{serviceId}', 'Quote', 'requestForm', ['auth']);
$router->post('/quote-request', 'Quote', 'submitRequest');
$router->get('/quotes', 'Quote', 'index', ['auth']);
$router->get('/quotes/{id}', 'Quote', 'show', ['auth']);
$router->get('/quote-compare/{requestId}', 'Quote', 'compare', ['auth']);
$router->post('/quote-accept/{quoteId}', 'Quote', 'accept', ['auth']);

// Order routes
$router->get('/orders', 'Order', 'index', ['auth']);
$router->get('/orders/{id}', 'Order', 'show', ['auth']);
$router->post('/place-order', 'Order', 'place', ['auth']);
$router->post('/order-review/{orderId}', 'Order', 'submitReview', ['auth']);

// User dashboard routes
$router->get('/dashboard', 'User', 'dashboard', ['auth']);
$router->get('/profile', 'User', 'profile', ['auth']);
$router->post('/profile', 'User', 'updateProfile', ['auth']);
$router->get('/password', 'User', 'passwordForm', ['auth']);
$router->post('/password', 'User', 'updatePassword', ['auth']);

// Vendor dashboard routes
$router->get('/vendor/dashboard', 'Vendor', 'dashboard', ['vendor']);
$router->get('/vendor/services', 'Vendor', 'services', ['vendor']);
$router->get('/vendor/service/create', 'Vendor', 'createServiceForm', ['vendor']);
$router->post('/vendor/service/create', 'Vendor', 'createService', ['vendor']);
$router->get('/vendor/service/edit/{id}', 'Vendor', 'editServiceForm', ['vendor']);
$router->post('/vendor/service/edit/{id}', 'Vendor', 'updateService', ['vendor']);
$router->post('/vendor/service/delete/{id}', 'Vendor', 'deleteService', ['vendor']);
$router->get('/vendor/quotes', 'Vendor', 'quotes', ['vendor']);
$router->get('/vendor/quote/{id}', 'Vendor', 'showQuote', ['vendor']);
$router->post('/vendor/quote/respond/{id}', 'Vendor', 'respondToQuote', ['vendor']);
$router->get('/vendor/orders', 'Vendor', 'orders', ['vendor']);
$router->get('/vendor/order/{id}', 'Vendor', 'showOrder', ['vendor']);
$router->post('/vendor/order/update-status/{id}', 'Vendor', 'updateOrderStatus', ['vendor']);
$router->get('/vendor/analytics', 'Vendor', 'analytics', ['vendor']);
$router->get('/vendor/profile', 'Vendor', 'vendorProfile', ['vendor']);
$router->post('/vendor/profile', 'Vendor', 'updateVendorProfile', ['vendor']);
$router->get('/vendor/subscription', 'Vendor', 'subscription', ['vendor']);
$router->post('/vendor/subscription/upgrade', 'Vendor', 'upgradeSubscription', ['vendor']);

// Admin routes
$router->get('/admin', 'Admin', 'dashboard', ['admin']);
$router->get('/admin/users', 'Admin', 'users', ['admin']);
$router->get('/admin/user/{id}', 'Admin', 'showUser', ['admin']);
$router->post('/admin/user/update/{id}', 'Admin', 'updateUser', ['admin']);
$router->get('/admin/vendors', 'Admin', 'vendors', ['admin']);
$router->get('/admin/vendor/{id}', 'Admin', 'showVendor', ['admin']);
$router->post('/admin/vendor/update/{id}', 'Admin', 'updateVendor', ['admin']);
$router->get('/admin/categories', 'Admin', 'categories', ['admin']);
$router->get('/admin/category/create', 'Admin', 'createCategoryForm', ['admin']);
$router->post('/admin/category/create', 'Admin', 'createCategory', ['admin']);
$router->get('/admin/category/edit/{id}', 'Admin', 'editCategoryForm', ['admin']);
$router->post('/admin/category/edit/{id}', 'Admin', 'updateCategory', ['admin']);
$router->post('/admin/category/delete/{id}', 'Admin', 'deleteCategory', ['admin']);
$router->get('/admin/services', 'Admin', 'services', ['admin']);
$router->get('/admin/service/{id}', 'Admin', 'showService', ['admin']);
$router->post('/admin/service/update/{id}', 'Admin', 'updateService', ['admin']);
$router->get('/admin/orders', 'Admin', 'orders', ['admin']);
$router->get('/admin/order/{id}', 'Admin', 'showOrder', ['admin']);
$router->post('/admin/order/update/{id}', 'Admin', 'updateOrder', ['admin']);
$router->get('/admin/quotes', 'Admin', 'quotes', ['admin']);
$router->get('/admin/quote/{id}', 'Admin', 'showQuote', ['admin']);
$router->get('/admin/subscriptions', 'Admin', 'subscriptions', ['admin']);
$router->get('/admin/settings', 'Admin', 'settings', ['admin']);
$router->post('/admin/settings', 'Admin', 'updateSettings', ['admin']);
$router->get('/admin/reports', 'Admin', 'reports', ['admin']);

// Error routes
$router->get('/404', 'Error', 'notFound');
$router->get('/403', 'Error', 'forbidden');
$router->get('/500', 'Error', 'serverError');
