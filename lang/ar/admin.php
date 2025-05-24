<?php
/**
 * Admin Arabic Translations
 * File path: lang/ar/admin.php
 */

return [
    // Navigation & General
    'dashboard' => 'لوحة التحكم',
    'dashboard_subtitle' => 'نظرة عامة على نشاط منصتك',
    'users' => 'المستخدمون',
    'vendors' => 'البائعون',
    'services' => 'الخدمات',
    'orders' => 'الطلبات',
    'categories' => 'الفئات',
    'reports' => 'التقارير',
    'settings' => 'الإعدادات',
    'administrator' => 'مدير النظام',

    // Dashboard Stats
    'total_users' => 'إجمالي المستخدمين',
    'total_vendors' => 'إجمالي البائعين',
    'total_services' => 'إجمالي الخدمات',
    'total_orders' => 'إجمالي الطلبات',
    'pending_approval' => 'في انتظار الموافقة',
    'monthly_activity' => 'النشاط الشهري',
    'quote_requests' => 'طلبات الأسعار',
    'total_requests' => 'إجمالي الطلبات',
    'quote_progress_text' => '75% من الطلبات تحصل على عرض سعر واحد على الأقل',

    // Recent Activity
    'recent_orders' => 'الطلبات الأخيرة',
    'recent_vendors' => 'البائعون الجدد',
    'view_all' => 'عرض الكل',
    'no_recent_orders' => 'لا توجد طلبات حديثة',
    'no_recent_vendors' => 'لا يوجد بائعون جدد',

    // Charts
    'new_users' => 'مستخدمون جدد',
    'new_orders' => 'طلبات جديدة',

    // User Management
    'manage_users_subtitle' => 'إدارة جميع المستخدمين المسجلين في المنصة',
    'search' => 'البحث',
    'search_users' => 'البحث عن المستخدمين...',
    'role' => 'الدور',
    'all_roles' => 'جميع الأدوار',
    'customer' => 'عميل',
    'vendor' => 'بائع',
    'status' => 'الحالة',
    'all_statuses' => 'جميع الحالات',
    'active' => 'نشط',
    'inactive' => 'غير نشط',
    'filter' => 'تصفية',
    'clear' => 'مسح',

    // Table Headers
    'user' => 'المستخدم',
    'joined' => 'تاريخ الانضمام',
    'actions' => 'الإجراءات',
    'order_id' => 'رقم الطلب',
    'customer' => 'العميل',
    'amount' => 'المبلغ',
    'company' => 'الشركة',

    // Status Labels
    'status_active' => 'نشط',
    'status_inactive' => 'غير نشط',
    'status_pending' => 'في الانتظار',
    'status_suspended' => 'موقوف',

    // Actions
    'activate' => 'تفعيل',
    'deactivate' => 'إلغاء التفعيل',
    'delete' => 'حذف',
    'approve' => 'موافقة',
    'suspend' => 'إيقاف',
    'edit' => 'تعديل',
    'view' => 'عرض',

    // Confirmations
    'confirm_activate' => 'هل أنت متأكد من تفعيل هذا المستخدم؟',
    'confirm_deactivate' => 'هل أنت متأكد من إلغاء تفعيل هذا المستخدم؟',
    'confirm_delete' => 'هل أنت متأكد من حذف هذا المستخدم؟ لا يمكن التراجع عن هذا الإجراء.',
    'confirm_approve' => 'هل أنت متأكد من الموافقة على هذا البائع؟',
    'confirm_suspend' => 'هل أنت متأكد من إيقاف هذا البائع؟',

    // Messages
    'user_activated' => 'تم تفعيل المستخدم بنجاح',
    'user_deactivated' => 'تم إلغاء تفعيل المستخدم بنجاح',
    'user_deleted' => 'تم حذف المستخدم بنجاح',
    'vendor_approved' => 'تم الموافقة على البائع بنجاح',
    'vendor_suspended' => 'تم إيقاف البائع بنجاح',
    'vendor_activated' => 'تم تفعيل البائع بنجاح',
    'vendor_deleted' => 'تم حذف البائع بنجاح',
    'service_approved' => 'تم الموافقة على الخدمة بنجاح',
    'service_suspended' => 'تم إيقاف الخدمة بنجاح',
    'service_deleted' => 'تم حذف الخدمة بنجاح',
    'order_updated' => 'تم تحديث حالة الطلب بنجاح',
    'payment_updated' => 'تم تحديث حالة الدفع بنجاح',
    'order_cancelled' => 'تم إلغاء الطلب بنجاح',
    'settings_updated' => 'تم تحديث الإعدادات بنجاح',
    'settings_failed' => 'فشل في تحديث الإعدادات',
    'action_failed' => 'فشل الإجراء. يرجى المحاولة مرة أخرى.',
    'invalid_action' => 'إجراء غير صحيح',

    // Pagination
    'previous' => 'السابق',
    'next' => 'التالي',
    'showing' => 'عرض',
    'to' => 'إلى',
    'of' => 'من',
    'results' => 'نتيجة',

    // Empty States
    'no_users_found' => 'لم يتم العثور على مستخدمين',
    'no_users_description' => 'لا يوجد مستخدمون يطابقون معايير التصفية الحالية.',
    'no_vendors_found' => 'لم يتم العثور على بائعين',
    'no_vendors_description' => 'لا يوجد بائعون يطابقون معايير التصفية الحالية.',
    'no_services_found' => 'لم يتم العثور على خدمات',
    'no_services_description' => 'لا توجد خدمات تطابق معايير التصفية الحالية.',
    'no_orders_found' => 'لم يتم العثور على طلبات',
    'no_orders_description' => 'لا توجد طلبات تطابق معايير التصفية الحالية.',

    // Vendor Management
    'manage_vendors_subtitle' => 'إدارة جميع حسابات البائعين والموافقات',
    'search_vendors' => 'البحث عن البائعين...',
    'subscription_status' => 'الاشتراك',
    'all_subscriptions' => 'جميع الاشتراكات',
    'free' => 'مجاني',
    'premium' => 'مميز',
    'services_count' => 'الخدمات',
    'orders_count' => 'الطلبات',

    // Service Management
    'manage_services_subtitle' => 'مراجعة ومراقبة قوائم الخدمات',
    'search_services' => 'البحث عن الخدمات...',
    'category' => 'الفئة',
    'all_categories' => 'جميع الفئات',
    'service_title' => 'الخدمة',
    'vendor_name' => 'البائع',

    // Order Management
    'manage_orders_subtitle' => 'مراقبة وإدارة جميع الطلبات',
    'search_orders' => 'البحث عن الطلبات...',
    'payment_status' => 'حالة الدفع',
    'all_payment_statuses' => 'جميع حالات الدفع',
    'date_range' => 'نطاق التاريخ',
    'date_from' => 'من',
    'date_to' => 'إلى',
    'contact_name' => 'جهة الاتصال',
    'total_amount' => 'المجموع',

    // Reports
    'reports_subtitle' => 'تحليلات وتقارير شاملة',
    'overview_report' => 'نظرة عامة',
    'vendor_report' => 'أفضل البائعين',
    'service_report' => 'الخدمات الشائعة',
    'order_report' => 'تحليل الطلبات',
    'quote_report' => 'تحليل العروض',
    'generate_report' => 'إنشاء تقرير',
    'export_report' => 'تصدير',
    'revenue' => 'الإيرادات',
    'conversion_rate' => 'معدل التحويل',
    'avg_rating' => 'متوسط التقييم',
    'quote_requests_count' => 'طلبات الأسعار',
    'total_revenue' => 'إجمالي الإيرادات',
    'avg_order_value' => 'متوسط قيمة الطلب',
    'total_responses' => 'إجمالي الردود',
    'accepted_quotes' => 'العروض المقبولة',

    // Settings
    'settings_subtitle' => 'تكوين إعدادات المنصة والتفضيلات',
    'general_settings' => 'الإعدادات العامة',
    'site_settings' => 'إعدادات الموقع',
    'business_settings' => 'إعدادات الأعمال',
    'site_name' => 'اسم الموقع',
    'site_description' => 'وصف الموقع',
    'contact_email' => 'بريد الاتصال',
    'contact_phone' => 'هاتف الاتصال',
    'default_language' => 'اللغة الافتراضية',
    'vendor_free_quotes' => 'العروض المجانية لكل بائع',
    'subscription_price' => 'سعر الاشتراك الشهري',
    'maintenance_mode' => 'وضع الصيانة',
    'save_settings' => 'حفظ الإعدادات',

    // Misc
    'loading' => 'جاري التحميل...',
    'refresh' => 'تحديث',
    'export' => 'تصدير',
    'import' => 'استيراد',
    'backup' => 'نسخ احتياطي',
    'restore' => 'استعادة',
];
