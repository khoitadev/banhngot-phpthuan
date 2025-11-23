<?php
/**
 * Helper functions for order status management
 */

/**
 * Get order status information
 * @param int $status Status code
 * @return array Status information
 */
function getOrderStatus($status) {
    $statuses = [
        0 => [
            'code' => 0,
            'name' => 'Chờ xử lý',
            'class' => 'warning',
            'icon' => 'clock',
            'description' => 'Đơn hàng đang chờ được xử lý'
        ],
        1 => [
            'code' => 1,
            'name' => 'Đang chuẩn bị hàng',
            'class' => 'info',
            'icon' => 'package',
            'description' => 'Đơn hàng đang được chuẩn bị'
        ],
        2 => [
            'code' => 2,
            'name' => 'Đang giao hàng',
            'class' => 'primary',
            'icon' => 'truck',
            'description' => 'Đơn hàng đang được vận chuyển'
        ],
        3 => [
            'code' => 3,
            'name' => 'Đã nhận hàng',
            'class' => 'success',
            'icon' => 'check-circle',
            'description' => 'Bạn đã nhận được hàng'
        ],
        4 => [
            'code' => 4,
            'name' => 'Chờ đánh giá',
            'class' => 'secondary',
            'icon' => 'star',
            'description' => 'Vui lòng đánh giá sản phẩm'
        ],
        5 => [
            'code' => 5,
            'name' => 'Hoàn thành',
            'class' => 'success',
            'icon' => 'check',
            'description' => 'Đơn hàng đã hoàn thành'
        ],
        -1 => [
            'code' => -1,
            'name' => 'Đã hủy',
            'class' => 'danger',
            'icon' => 'x-circle',
            'description' => 'Đơn hàng đã bị hủy'
        ]
    ];
    
    return isset($statuses[$status]) ? $statuses[$status] : $statuses[0];
}

/**
 * Get all order statuses for select dropdown
 * @return array
 */
function getAllOrderStatuses() {
    return [
        0 => 'Chờ xử lý',
        1 => 'Đang chuẩn bị hàng',
        2 => 'Đang giao hàng',
        3 => 'Đã nhận hàng',
        4 => 'Chờ đánh giá',
        5 => 'Hoàn thành',
        -1 => 'Đã hủy'
    ];
}

/**
 * Get status badge HTML
 * @param int $status Status code
 * @return string HTML badge
 */
function getOrderStatusBadge($status) {
    $statusInfo = getOrderStatus($status);
    return '<span class="badge badge-' . $statusInfo['class'] . '">' . $statusInfo['name'] . '</span>';
}

/**
 * Get status timeline steps
 * @param int $currentStatus Current status
 * @return array Timeline steps
 */
function getOrderStatusTimeline($currentStatus) {
    $steps = [
        ['code' => 0, 'name' => 'Chờ xử lý', 'icon' => 'clock'],
        ['code' => 1, 'name' => 'Đang chuẩn bị hàng', 'icon' => 'package'],
        ['code' => 2, 'name' => 'Đang giao hàng', 'icon' => 'truck'],
        ['code' => 3, 'name' => 'Đã nhận hàng', 'icon' => 'check-circle'],
        ['code' => 4, 'name' => 'Chờ đánh giá', 'icon' => 'star'],
        ['code' => 5, 'name' => 'Hoàn thành', 'icon' => 'check']
    ];
    
    foreach ($steps as &$step) {
        $step['active'] = $step['code'] <= $currentStatus;
        $step['current'] = $step['code'] == $currentStatus;
    }
    
    return $steps;
}

