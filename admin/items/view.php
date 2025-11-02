<?php
include "../../connect.php";

$table = "itemsview";

$limit  = isset($_GET['limit'])  ? (int)$_GET['limit']  : 20;
$page   = isset($_GET['page'])   ? (int)$_GET['page']   : 1;
$offset = ($page - 1) * $limit;

$totalStmt = $con->prepare("SELECT COUNT(*) as total FROM $table");
$totalStmt->execute();
$total = (int)$totalStmt->fetchColumn();

$stmt = $con->prepare("SELECT * FROM $table LIMIT :limit OFFSET :offset");
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPages = ceil($total / $limit);
$hasNext = $page < $totalPages;
$hasPrev = $page > 1;

if ($data) {
    echo json_encode([
        "status" => "success",
        "data" => $data,
        "meta" => [
            "total" => $total,
            "limit" => $limit,
            "page" => $page,
            "pages" => $totalPages,
            "hasNext" => $hasNext,
            "hasPrev" => $hasPrev
        ]
    ]);
} else {
    echo json_encode(["status" => "failure", "message" => "No items found"]);
}
