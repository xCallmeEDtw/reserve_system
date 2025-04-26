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

// 取得使用者資料
$username = $_SESSION['memberId'];
$userQuery = "SELECT user_id, name, email FROM user WHERE username = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("s", $username);
$userStmt->execute();
$userResult = $userStmt->get_result();
$user = $userResult->fetch_assoc();
$userId = $user['user_id'];
$userName = $user['name'];
$userEmail = $user['email'];
$userStmt->close();

// 確認該預約確實屬於此 user
$resvQuery = "SELECT * FROM Reservation WHERE reservation_id = ? AND user_id = ?";
$resvStmt = $conn->prepare($resvQuery);
$resvStmt->bind_param("ii", $reservationId, $userId);
$resvStmt->execute();
$resvResult = $resvStmt->get_result();
$reservation = $resvResult->fetch_assoc();
$resvStmt->close();

if (!$reservation) {
    echo json_encode(['success' => false, 'message' => '找不到此預約']);
    exit;
}

// 刪除預約
$deleteStmt = $conn->prepare("DELETE FROM Reservation WHERE reservation_id = ? AND user_id = ?");
$deleteStmt->bind_param("ii", $reservationId, $userId);
$success = $deleteStmt->execute();
$deleteStmt->close();

if ($success) {
    // 預約刪除成功後，寄取消通知
    $postData = http_build_query([
        'to_email' => $userEmail,
        'to_name'  => $userName,
        'subject'  => 'Study Room Seat Reservation Cancellation Notice',
        'body'     => "親愛的 {$userName}，<br>您已成功取消以下預約：<br>日期：{$reservation['date']}<br>時間：{$reservation['start_time']} - {$reservation['end_time']}"
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/demo/api/send_email.php'); // 根據你的網站路徑調整
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => '取消失敗']);
}
?>
