<?php

include __DIR__ . "/../../connect.php";

/**
 * Execute a scalar query and cast the result to int.
 */
function fetchInt(string $sql, array $params = []): int
{
    global $con;
    $stmt = $con->prepare($sql);
    $stmt->execute($params);
    $value = $stmt->fetchColumn();
    return $value !== false ? (int) $value : 0;
}

/**
 * Execute a scalar query and cast the result to float.
 */
function fetchFloat(string $sql, array $params = []): float
{
    global $con;
    $stmt = $con->prepare($sql);
    $stmt->execute($params);
    $value = $stmt->fetchColumn();
    return $value !== false ? (float) $value : 0.0;
}

/**
 * Check if a table contains a specific column.
 */
function tableHasColumn(string $table, string $column): bool
{
    static $cache = [];
    $cacheKey = $table . '.' . $column;
    if (array_key_exists($cacheKey, $cache)) {
        return $cache[$cacheKey];
    }

    global $con;
    $stmt = $con->prepare(
        'SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
    );
    $stmt->execute([$table, $column]);
    $cache[$cacheKey] = $stmt->fetchColumn() > 0;

    return $cache[$cacheKey];
}

/**
 * Build the statistics payload that feeds the admin dashboard.
 */
function buildDashboardStats(): array
{
    $now = date('Y-m-d H:i:s');

    $users = [
        'total'            => fetchInt('SELECT COUNT(*) FROM users'),
        'active'           => fetchInt('SELECT COUNT(*) FROM users WHERE users_active = 1'),
        'inactive'         => fetchInt('SELECT COUNT(*) FROM users WHERE users_active = 0'),
        'pending_approval' => fetchInt('SELECT COUNT(*) FROM users WHERE users_approve = 0'),
    ];

    $delivery = [
        'total'            => fetchInt('SELECT COUNT(*) FROM delivery'),
        'approved'         => fetchInt('SELECT COUNT(*) FROM delivery WHERE delivery_approve = 1'),
        'pending_approval' => fetchInt('SELECT COUNT(*) FROM delivery WHERE delivery_approve = 0'),
    ];

    $categories = [
        'total' => fetchInt('SELECT COUNT(*) FROM categories'),
    ];

    $items = [
        'total'        => fetchInt('SELECT COUNT(*) FROM items'),
        'active'       => fetchInt('SELECT COUNT(*) FROM items WHERE items_active = 1'),
        'inactive'     => fetchInt('SELECT COUNT(*) FROM items WHERE items_active = 0'),
        'out_of_stock' => fetchInt('SELECT COUNT(*) FROM items WHERE items_count <= 0'),
        'low_stock'    => fetchInt('SELECT COUNT(*) FROM items WHERE items_count > 0 AND items_count <= 5'),
    ];

    $orders = [
        'total'            => fetchInt('SELECT COUNT(*) FROM orders'),
        'pending'          => fetchInt('SELECT COUNT(*) FROM orders WHERE orders_status = 0'),
        'approved'         => fetchInt('SELECT COUNT(*) FROM orders WHERE orders_status = 1'),
        'prepared'         => fetchInt('SELECT COUNT(*) FROM orders WHERE orders_status = 2'),
        'out_for_delivery' => fetchInt('SELECT COUNT(*) FROM orders WHERE orders_status = 3'),
        'completed'        => fetchInt('SELECT COUNT(*) FROM orders WHERE orders_status = 4'),
        'delivery_orders'  => fetchInt('SELECT COUNT(*) FROM orders WHERE orders_type = 0'),
        'pickup_orders'    => fetchInt('SELECT COUNT(*) FROM orders WHERE orders_type = 1'),
        'cash_payments'    => fetchInt('SELECT COUNT(*) FROM orders WHERE orders_paymentmethod = 0'),
        'card_payments'    => fetchInt('SELECT COUNT(*) FROM orders WHERE orders_paymentmethod = 1'),
        'total_revenue'    => round(fetchFloat('SELECT COALESCE(SUM(orders_totalprice), 0) FROM orders WHERE orders_status = 4'), 2),
        'average_rating'   => round(fetchFloat('SELECT COALESCE(AVG(NULLIF(orders_rating, 0)), 0) FROM orders WHERE orders_rating IS NOT NULL'), 2),
        'rated_orders'     => fetchInt('SELECT COUNT(*) FROM orders WHERE orders_rating IS NOT NULL AND orders_rating > 0'),
        'feedback_with_note' => fetchInt('SELECT COUNT(*) FROM orders WHERE orders_noterating IS NOT NULL AND orders_noterating != ""'),
    ];

    $coupons = [
        'total'                 => fetchInt('SELECT COUNT(*) FROM coupon'),
        'active'                => fetchInt('SELECT COUNT(*) FROM coupon WHERE coupon_active = 1'),
        'inactive'              => fetchInt('SELECT COUNT(*) FROM coupon WHERE coupon_active = 0'),
        'expired'               => fetchInt('SELECT COUNT(*) FROM coupon WHERE coupon_expdate IS NOT NULL AND coupon_expdate < ?', [$now]),
        'remaining_redemptions' => fetchInt('SELECT COALESCE(SUM(coupon_count), 0) FROM coupon WHERE coupon_active = 1'),
    ];

    $favorites = [
        'total_relations' => fetchInt('SELECT COUNT(*) FROM favorite'),
        'unique_users'    => fetchInt('SELECT COUNT(DISTINCT favorite_usersid) FROM favorite'),
    ];

    $carts = [
        'open_carts'            => fetchInt('SELECT COUNT(*) FROM cart WHERE cart_orders = 0'),
        'open_cart_distinct_items' => fetchInt('SELECT COUNT(DISTINCT cart_itemid) FROM cart WHERE cart_orders = 0'),
    ];

    $addresses = [
        'total' => fetchInt('SELECT COUNT(*) FROM address'),
    ];

    $notifications = [
        'total' => fetchInt('SELECT COUNT(*) FROM notification'),
    ];

    if (tableHasColumn('notification', 'notification_read')) {
        $notifications['unread'] = fetchInt('SELECT COUNT(*) FROM notification WHERE notification_read = 0');
    }

    if (tableHasColumn('notification', 'notification_action')) {
        $notifications['with_action'] = fetchInt('SELECT COUNT(*) FROM notification WHERE notification_action IS NOT NULL AND notification_action != ""');
    }

    return [
        'generated_at'  => date('c'),
        'users'         => $users,
        'delivery'      => $delivery,
        'categories'    => $categories,
        'items'         => $items,
        'orders'        => $orders,
        'coupons'       => $coupons,
        'favorites'     => $favorites,
        'carts'         => $carts,
        'addresses'     => $addresses,
        'notifications' => $notifications,
    ];
}

/**
 * Flatten a multi-dimensional associative array so it can be exported as CSV.
 */
function flattenStats(array $stats, string $prefix = ''): array
{
    $rows = [];
    foreach ($stats as $key => $value) {
        $label = $prefix === '' ? $key : $prefix . '.' . $key;
        if (is_array($value)) {
            $rows = array_merge($rows, flattenStats($value, $label));
        } else {
            $rows[] = [$label, $value];
        }
    }

    return $rows;
}

$format = isset($_GET['format']) ? strtolower($_GET['format']) : 'json';
$section = isset($_GET['section']) ? strtolower($_GET['section']) : null;

$stats = buildDashboardStats();
$sectionsOnly = $stats;
unset($sectionsOnly['generated_at']);

if ($section !== null && $section !== '') {
    if (!array_key_exists($section, $sectionsOnly)) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status'  => 'failure',
            'message' => 'Unknown section requested',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stats = [
        'generated_at' => $stats['generated_at'],
        $section       => $sectionsOnly[$section],
    ];
}

if ($format === 'excel' || $format === 'csv') {
    $filename = 'dashboard-stats-' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    $output = fopen('php://output', 'w');
    fputcsv($output, ['metric', 'value']);
    foreach (flattenStats($stats) as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'status' => 'success',
    'data'   => $stats,
], JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION);