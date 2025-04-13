<?php
require_once(__DIR__ . '/../dbconfig.php');
session_start();

header('Content-Type: application/json');

$username = $_SESSION['memberId'];
$date = $_GET['date'];

// 查詢是否已有預約
$sql = "SELECT * FROM Reservation WHERE username = ? AND date = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $username, $date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$hasReservation = mysqli_num_rows($result) > 0;

echo json_encode(['hasReservation' => $hasReservation]);

mysqli_stmt_close($stmt);
mysqli_close($conn);
