<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../dbconfig.php');
header('Content-Type: application/json');

// 取得參數
$roomId    = (int)($_GET['room_id'] ?? 0);
$date      = $_GET['date'] ?? '';
$startTime = $_GET['start_time'] ?? '';
$endTime   = $_GET['end_time'] ?? '';

// 檢查基本參數
if (!$roomId || !$date || !$startTime || !$endTime) {
    echo json_encode(['seats' => []]);
    exit;
}

// 取得自習室的行列數
$roomStmt = $conn->prepare("SELECT row_count, col_count FROM StudyRoom WHERE room_id = ?");
$roomStmt->bind_param('i', $roomId);
$roomStmt->execute();
$roomResult = $roomStmt->get_result();
$room = $roomResult->fetch_assoc();
$roomStmt->close();

if (!$room) {
    echo json_encode(['seats' => []]);
    exit;
}

$rowCount = (int)$room['row_count'];
$colCount = (int)$room['col_count'];

// ✅ 檢查是否該時段有被 blocked
$checkBlocked = $conn->prepare("
    SELECT 1 FROM BlockedPeriod
    WHERE room_id = ?
    AND start_time < ?
    AND end_time > ?
");
$startFull = "$date $startTime";
$endFull   = "$date $endTime";
$checkBlocked->bind_param('iss', $roomId, $endFull, $startFull);
$checkBlocked->execute();
$blockResult = $checkBlocked->get_result();
$isBlocked = $blockResult->num_rows > 0;
$checkBlocked->close();

// 撈取所有座位資料
$seatStmt = $conn->prepare("SELECT seat_id, status, has_power FROM Seat WHERE room_id = ? ORDER BY seat_id ASC");
$seatStmt->bind_param('i', $roomId);
$seatStmt->execute();
$seatResult = $seatStmt->get_result();

$seats = [];
$index = 0;

// 預先準備 reservation 查詢
$reservationStmt = $conn->prepare("
    SELECT 1 FROM Reservation
    WHERE seat_id = ?
    AND date = ?
    AND (
        (start_time < ? AND end_time > ?)
        OR (start_time >= ? AND start_time < ?)
    )
");

while ($seat = $seatResult->fetch_assoc()) {
    $row = floor($index / $colCount) + 1;
    $col = ($index % $colCount) + 1;
    $index++;

    if ($isBlocked) {
        $seatStatus = 'reserved';
    } else {
        // 檢查預約
        $reservationStmt->bind_param(
            'isssss',
            $seat['seat_id'],
            $date,
            $endTime,
            $startTime,
            $startTime,
            $endTime
        );
        $reservationStmt->execute();
        $reservationResult = $reservationStmt->get_result();

        $seatStatus = ($reservationResult && $reservationResult->num_rows > 0)
            ? 'reserved'
            : $seat['status'];
    }

    $seats[] = [
        'seat_id'   => $seat['seat_id'],
        'status'    => $seatStatus,
        'row'       => $row,
        'col'       => $col,
        'has_power' => $seat['has_power']
    ];
}

// 關閉連線
$reservationStmt->close();
$seatStmt->close();
$conn->close();

// 回傳結果
echo json_encode(['seats' => $seats]);
