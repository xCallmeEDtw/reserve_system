<?php
require_once(__DIR__ . '/../dbconfig.php');
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['memberId'])) {
    echo json_encode(['success' => false, 'message' => '未登入']);
    exit;
}

// 取 user_id
$username = $_SESSION['memberId'];
$userQuery = "SELECT user_id, email, name FROM user WHERE username = ?";
$userStmt = mysqli_prepare($conn, $userQuery);
mysqli_stmt_bind_param($userStmt, "s", $username);
mysqli_stmt_execute($userStmt);
$userResult = mysqli_stmt_get_result($userStmt);
$user = mysqli_fetch_assoc($userResult);

if (!$user) {
    echo json_encode(['success' => false, 'message' => '找不到使用者']);
    exit;
}

$userId = $user['user_id'];
$userEmail = $user['email'];
$userName = $user['name'];

mysqli_stmt_close($userStmt);

// 取得 POST 資料
$seatId = (int)$_POST['seat_id'];
$date = $_POST['date'];
$startTime = $_POST['start_time'];
$endTime = $_POST['end_time'];

// 檢查當天是否已有預約
$checkSql = "SELECT * FROM Reservation WHERE user_id = ? AND date = ?";
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, "is", $userId, $date);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) > 0) {
    echo json_encode(['success' => false, 'message' => '你當天已有預約！']);
    mysqli_stmt_close($checkStmt);
    exit;
}
mysqli_stmt_close($checkStmt);

// 插入預約紀錄
$insertSql = "INSERT INTO Reservation (user_id, seat_id, date, start_time, end_time) VALUES (?, ?, ?, ?, ?)";
$insertStmt = mysqli_prepare($conn, $insertSql);
mysqli_stmt_bind_param($insertStmt, "iisss", $userId, $seatId, $date, $startTime, $endTime);
$success = mysqli_stmt_execute($insertStmt);
mysqli_stmt_close($insertStmt);

if ($success) {
    // ✅ 成功預約後，呼叫寄信 API
    $postData = http_build_query([
        'to_email' => $userEmail,
        'to_name'  => $userName,
        'subject'  => 'Study room seat reservation successful notification',
        'body'     => "親愛的 {$userName}，<br>您已成功預約座位！<br>日期：{$date}<br>時間：{$startTime} - {$endTime}"
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/demo/api/send_email.php'); // 根據你的網站路徑改
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => '預約失敗']);
}

mysqli_close($conn);
?>
