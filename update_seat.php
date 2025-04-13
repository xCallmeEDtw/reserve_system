<?php
require_once(__DIR__ . '/dbconfig.php');

session_start();

// 確認是否為管理員
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["memberRole"] !== 'admin') {
    http_response_code(403);
    echo 'Access denied';
    exit;
}

// 確認必要參數
if (!isset($_POST['seat_id'], $_POST['has_power'], $_POST['status'])) {
    http_response_code(400);
    echo 'Invalid request';
    exit;
}

$seatId = (int)$_POST['seat_id'];
$hasPower = (int)$_POST['has_power'];
$status = $_POST['status'];

// 安全檢查 status 是否在允許的值裡面
$allowedStatus = ['available', 'reserved', 'not_seat'];
if (!in_array($status, $allowedStatus)) {
    http_response_code(400);
    echo 'Invalid status';
    exit;
}

// 更新資料庫
$sql = "UPDATE Seat SET has_power = ?, status = ? WHERE seat_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "isi", $hasPower, $status, $seatId);
if (mysqli_stmt_execute($stmt)) {
    http_response_code(200);
    echo 'Success';
} else {
    http_response_code(500);
    echo 'Database error';
}
mysqli_stmt_close($stmt);
mysqli_close($conn);
