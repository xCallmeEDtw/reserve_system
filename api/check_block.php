<?php
require_once(__DIR__ . '/../dbconfig.php');
session_start();

header('Content-Type: application/json');

$roomId = (int)$_GET['room_id'];
$date = $_GET['date'];

// 查詢 BlockedPeriod
$sql = "SELECT * FROM BlockedPeriod WHERE room_id = ? AND start_time <= ? AND end_time >= ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iss", $roomId, $date, $date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$isBlocked = mysqli_num_rows($result) > 0;

echo json_encode(['blocked' => $isBlocked]);

mysqli_stmt_close($stmt);
mysqli_close($conn);
