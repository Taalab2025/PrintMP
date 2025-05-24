<?php
/**
 * Order Detail View
 * File path: views/pages/orders/detail.php
 * Session: 7 - Quote Comparison & Order Placement
 */
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="<?= $isRtl ? 'text-right' : 'text-left' ?> mb-8">
        <div class="flex <?= $isRtl ? 'flex-row-reverse' : 'flex-row' ?> items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <?= $this->localization->t('orders.order') ?> #<?= $order['id'] ?>
                </h1>
                <p class="mt-2 text-gray-600">
                    <?= $this->localization->t('orders.placed_on') ?> <?= date('F j, Y \a\t g:i A', strtotime($order['created_at'])) ?>
                </p>
            </div>

            <!-- Order Status Badge -->
            <div>
                <?php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'confirmed' => 'bg-blue-100 text-blue-800',
                    'processing' => 'bg-purple-100 text-purple-800',
                    'shipped' => 'bg-indigo-100 text-indigo-800',
                    'delivered' => 'bg-green-100 text-green-800',
                    'completed' => 'bg-green-100 text-green-800',
                    'cancelled' => 'bg-red-100 text-red-800'
                ];
                $statusClass = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800';
                ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $statusClass ?>">
                    <?= $this->localization->t("orders.status_{$order['status']}") ?>
                </span>
            </div>
        </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Status Timeline -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    <?= $this->localization->t('orders.order_status') ?>
                </h2>

                <?php
                $statusSteps = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];
                $currentStepIndex = array_search($order['status'], $statusSteps);
                if ($currentStepIndex === false) $currentStepIndex = -1;
                ?>

                <div class="flow-root">
                    <ul class="<?= $isRtl ? '-mr-6' : '-ml-6' ?>">
                        <?php foreach ($statusSteps as $index => $status): ?>
                        <?php
                        $isCompleted = $currentStepIndex >= $index;
                        $isCurrent = $currentStepIndex === $index;
                        $isLast = $index === count($statusSteps) - 1;
                        ?>
                        <li class="relative pb-8 <?= $isLast ? '' : '' ?>">
                            <?php if (!$isLast): ?>
                            <div class="absolute top-4 <?= $isRtl ? 'right-4' : 'left-4' ?> mt-0.5 h-full w-0.5 <?= $isCompleted ? 'bg-blue-600' : 'bg-gray-300' ?>"></div>
                            <?php endif; ?>

                            <div class="<?= $isRtl ? 'pr-9' : 'pl-9' ?> flex items-center group">
                                <span class="h-9 flex items-center">
                                    <span class="relative z-10 w-8 h-8 flex items-center justify-center <?= $isCompleted ? 'bg-blue-600 border-blue-600' : 'bg-white border-gray-300' ?> border-2 rounded-full group-hover:border-gray-400">
                                        <?php if ($isCompleted): ?>
                                            <i class="fas fa-check text-white text-sm"></i>
                                        <?php else: ?>
                                            <span class="h-2.5 w-2.5 bg-transparent rounded-full group-hover:bg-gray-300"></span>
                                        <?php endif; ?>
                                    </span>
                                </span>
                                <span class="<?= $isRtl ? 'mr-4' : 'ml-4' ?> min-w-0 flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">
                                        <?= $this->localization->t("orders.status_{$status}") ?>
                                    </span>
                                    <?php if ($isCurrent): ?>
                                    <span class="text-sm text-gray-500">
                                        <?= $this->localization->t('orders.current_status') ?>
                                    </span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Service Details -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    <?= $this->localization->t('orders.service_details') ?>
                </h2>

                <div class="flex items-start space-x-4 <?= $isRtl ? 'space-x-reverse' : '' ?>">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-print text-gray-600 text-xl"></i>
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-medium text-gray-900">
                            <?= htmlspecialchars($order["service_title_$currentLanguage"]) ?>
                        </h3>

                        <div class="mt-2 space-y-1">
                            <p class="text-sm text-gray-600">
                                <span class="font-medium"><?= $this->localization->t('orders.vendor') ?>:</span>
                                <?= htmlspecialchars($order["vendor_name_$currentLanguage"]) ?>
                            </p>

                            <p class="text-sm text-gray-600">
                                <span class="font-medium"><?= $this->localization->t('orders.price') ?>:</span>
                                <?= number_format($order['total_amount'], 2) ?> <?= $this->localization->t('general.currency') ?>
                            </p>

                            <?php if ($order['estimated_delivery_date']): ?>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium"><?= $this->localization->t('orders.estimated_delivery') ?>:</span>
                                <?= date('F j, Y', strtotime($order['estimated_delivery_date'])) ?>
                            </p>
                            <?php endif; ?>
                        </div>

                        <?php if ($order['quote_message']): ?>
                        <div class="mt-3 p-3 bg-gray-50 rounded-md">
                            <p class="text-xs text-gray-600 mb-1"><?= $this->localization->t('orders.vendor_notes') ?>:</p>
                            <p class="text-sm text-gray-700"><?= htmlspecialchars($order['quote_message']) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    <?= $this->localization->t('orders.contact_information') ?>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-900"><?= $this->localization->t('orders.full_name') ?></p>
                        <p class="text-sm text-gray-600"><?= htmlspecialchars($order['contact_name']) ?></p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-900"><?= $this->localization->t('orders.email') ?></p>
                        <p class="text-sm text-gray-600"><?= htmlspecialchars($order['contact_email']) ?></p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-900"><?= $this->localization->t('orders.phone') ?></p>
                        <p class="text-sm text-gray-600"><?= htmlspecialchars($order['contact_phone']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    <?= $this->localization->t('orders.delivery_information') ?>
                </h2>

                <div>
                    <p class="text-sm font-medium text-gray-900 mb-2"><?= $this->localization->t('orders.delivery_address') ?></p>
                    <p class="text-sm text-gray-600 whitespace-pre-line"><?= htmlspecialchars($order['delivery_address']) ?></p>
                </div>

                <?php if ($order['tracking_number']): ?>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm font-medium text-gray-900"><?= $this->localization->t('orders.tracking_number') ?></p>
                    <p class="text-sm text-gray-600 font-mono"><?= htmlspecialchars($order['tracking_number']) ?></p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Design Files -->
            <?php if (!empty($files)): ?>
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    <?= $this->localization->t('orders.design_files') ?>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($files as $file): ?>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file text-gray-400 text-lg"></i>
                            </div>
                            <div class="<?= $isRtl ? 'mr-3' : 'ml-3' ?> flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    <?= htmlspecialchars($file['original_name']) ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    <?= strtoupper($file['file_type']) ?>
                                </p>
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="/files/download/<?= $file['id'] ?>"
                               class="text-sm text-blue-600 hover:text-blue-500">
                                <i class="fas fa-download <?= $isRtl ? 'ml-1' : 'mr-1' ?>"></i>
                                <?= $this->localization->t('general.download') ?>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Additional Notes -->
            <?php if ($order['notes']): ?>
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    <?= $this->localization->t('orders.additional_notes') ?>
                </h2>
                <p class="text-sm text-gray-600 whitespace-pre-line"><?= htmlspecialchars($order['notes']) ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Order Actions & Summary -->
        <div class="lg:col-span-1">
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">
                        <?= $this->localization->t('orders.quick_actions') ?>
                    </h2>

                    <div class="space-y-3">
                        <a href="/orders/track/<?= $order['id'] ?>"
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-map-marker-alt <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                            <?= $this->localization->t('orders.track_order') ?>
                        </a>

                        <?php if ($order['status'] === 'completed' && $this->auth->isLoggedIn()): ?>
                        <a href="/reviews/create/<?= $order['id'] ?>"
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-star <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                            <?= $this->localization->t('orders.write_review') ?>
                        </a>
                        <?php endif; ?>

                        <button onclick="window.print()"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-print <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                            <?= $this->localization->t('orders.print_order') ?>
                        </button>

                        <?php if ($order['status'] === 'pending' && $this->auth->isLoggedIn()): ?>
                        <button onclick="cancelOrder()"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                            <i class="fas fa-times <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                            <?= $this->localization->t('orders.cancel_order') ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">
                        <?= $this->localization->t('orders.payment_information') ?>
                    </h2>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600"><?= $this->localization->t('orders.subtotal') ?>:</span>
                            <span class="text-sm font-medium text-gray-900">
                                <?= number_format($order['total_amount'], 2) ?> <?= $this->localization->t('general.currency') ?>
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600"><?= $this->localization->t('orders.payment_status') ?>:</span>
                            <span class="text-sm">
                                <?php
                                $paymentColors = [
                                    'pending' => 'text-yellow-600',
                                    'paid' => 'text-green-600',
                                    'failed' => 'text-red-600'
                                ];
                                $paymentClass = $paymentColors[$order['payment_status']] ?? 'text-gray-600';
                                ?>
                                <span class="<?= $paymentClass ?>">
                                    <?= $this->localization->t("orders.payment_{$order['payment_status']}") ?>
                                </span>
                            </span>
                        </div>

                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex justify-between">
                                <span class="text-base font-medium text-gray-900"><?= $this->localization->t('orders.total') ?>:</span>
                                <span class="text-base font-medium text-gray-900">
                                    <?= number_format($order['total_amount'], 2) ?> <?= $this->localization->t('general.currency') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Need Help? -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">
                        <?= $this->localization->t('orders.need_help') ?>
                    </h2>

                    <p class="text-sm text-gray-600 mb-4">
                        <?= $this->localization->t('orders.need_help_text') ?>
                    </p>

                    <div class="space-y-2">
                        <a href="mailto:support@printhub-egypt.com"
                           class="text-sm text-blue-600 hover:text-blue-500 flex items-center">
                            <i class="fas fa-envelope <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                            <?= $this->localization->t('orders.contact_support') ?>
                        </a>

                        <a href="tel:+201234567890"
                           class="text-sm text-blue-600 hover:text-blue-500 flex items-center">
                            <i class="fas fa-phone <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                            <?= $this->localization->t('orders.call_support') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div id="cancelModal" class="fixed inset-0 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeCancelModal()"></div>

        <div class="relative bg-white rounded-lg max-w-md w-full mx-auto shadow-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <?= $this->localization->t('orders.cancel_order') ?>
                </h3>
            </div>

            <div class="px-6 py-4">
                <p class="text-sm text-gray-600 mb-4">
                    <?= $this->localization->t('orders.cancel_confirmation') ?>
                </p>

                <div class="mb-4">
                    <label for="cancelReason" class="block text-sm font-medium text-gray-700 mb-2">
                        <?= $this->localization->t('orders.cancellation_reason') ?>
                    </label>
                    <textarea id="cancelReason"
                              rows="3"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                              placeholder="<?= $this->localization->t('orders.cancellation_reason_placeholder') ?>"></textarea>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 <?= $isRtl ? 'text-left' : 'text-right' ?> rounded-b-lg">
                <div class="flex <?= $isRtl ? 'flex-row-reverse' : 'flex-row' ?> space-x-3 <?= $isRtl ? 'space-x-reverse' : '' ?>">
                    <button type="button"
                            onclick="closeCancelModal()"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <?= $this->localization->t('general.cancel') ?>
                    </button>

                    <button type="button"
                            onclick="confirmCancel()"
                            class="bg-red-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700">
                        <?= $this->localization->t('orders.cancel_order') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cancelOrder() {
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}

function confirmCancel() {
    const reason = document.getElementById('cancelReason').value;

    fetch('/orders/cancel', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            order_id: '<?= $order['id'] ?>',
            reason: reason,
            csrf_token: '<?= $csrfToken ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('<?= $this->localization->t("orders.cancellation_failed") ?>');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('<?= $this->localization->t("general.error_occurred") ?>');
    });

    closeCancelModal();
}
</script>
