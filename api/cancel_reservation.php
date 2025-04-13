<?php
require_once(__DIR__ . '/../dbconfig.php');
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => '未登入']);
    exit;
}

$reservationId = (int)($_POST['reservation_id'] ?? 0);

if (!$reservationId) {
    echo json_encode(['success' => false, 'message' => '缺少預約 ID']);
    exit;
}

// 安全檢查：確保使用者只能刪除自己的預約
$username = $_SESSION['memberId'];
$userQuery = "SELECT user_id FROM user WHERE username = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("s", $username);
$userStmt->execute();
$userResult = $userStmt->get_result();
$user = $userResult->fetch_assoc();
$userId = $user['user_id'];
$userStmt->close();

// 刪除預約
$deleteStmt = $conn->prepare("DELETE FROM Reservation WHERE reservation_id = ? AND user_id = ?");
$deleteStmt->bind_param("ii", $reservationId, $userId);
$success = $deleteStmt->execute();
$deleteStmt->close();

echo json_encode(['success' => $success]);
