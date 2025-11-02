<?php
include "../../connect.php";

header('Content-Type: application/json; charset=utf-8');

$table = "categories";

// Params
$limit  = isset($_GET['limit']) ? max(1, min( (int)$_GET['limit'], 100)) : 20;
$page   = isset($_GET['page'])  ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

try {
    // Total count
    $totalStmt = $con->prepare("SELECT COUNT(*) FROM $table");
    $totalStmt->execute();
    $total = (int)$totalStmt->fetchColumn();

    // Page data (ثبّت ترتيب ثابت على العمود الأساسي)
    $stmt = $con->prepare("SELECT * FROM $table ORDER BY categories_id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Meta
    $pages   = $limit > 0 ? (int)ceil($total / $limit) : 0;
    $hasNext = $page < $pages;
    $hasPrev = $page > 1;

    if ($data && count($data) > 0) {
        echo json_encode([
            "status" => "success",
            "data"   => $data,
            "meta"   => [
                "total"   => $total,
                "limit"   => $limit,
                "page"    => $page,
                "pages"   => $pages,
                "hasNext" => $hasNext,
                "hasPrev" => $hasPrev,
            ],
        ]);
    } else {
        echo json_encode([
            "status"  => "success",
            "data"    => [],
            "meta"    => [
                "total"   => $total,
                "limit"   => $limit,
                "page"    => $page,
                "pages"   => $pages,
                "hasNext" => $hasNext,
                "hasPrev" => $hasPrev,
            ],
            "message" => "No categories found for this page"
        ]);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "status"  => "failure",
        "message" => "Server error",
        "error"   => $e->getMessage(),
    ]);
}
