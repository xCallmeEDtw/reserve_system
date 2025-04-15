<?php
session_start();
require_once(__DIR__ . '/dbconfig.php');

// 管理員身份檢查
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["memberRole"] !== 'admin') {
    header("location: login.php");
    exit;
}

// 讀取所有自習室
$roomsResult = $conn->query("SELECT room_id, room_name FROM StudyRoom");
$rooms = [];
while ($row = $roomsResult->fetch_assoc()) {
    $rooms[] = $row;
}

// 讀取所有日期（distinct）
$datesResult = $conn->query("SELECT DISTINCT date FROM Reservation ORDER BY date DESC");
$dates = [];
while ($row = $datesResult->fetch_assoc()) {
    $dates[] = $row['date'];
}

// 撈出所有預約資料（初始全部顯示）
$reservationsResult = $conn->query("
    SELECT r.reservation_id, r.date, r.start_time, r.end_time,
           u.username, u.name, s.seat_id, sr.room_name
    FROM Reservation r
    JOIN user u ON r.user_id = u.user_id
    JOIN Seat s ON r.seat_id = s.seat_id
    JOIN StudyRoom sr ON s.room_id = sr.room_id
    ORDER BY r.date DESC, r.start_time ASC
");

$reservations = [];
while ($row = $reservationsResult->fetch_assoc()) {
    $reservations[] = $row;
}
?>

<!doctype html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>全部預約紀錄 - 管理員</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="text-center">
    <main class="container mt-5">
        <h2 class="h2 mb-4 fw-bold">自習室座位預約系統 - 所有預約紀錄</h2>

        <!-- 篩選條件 -->
        <div class="row mb-4 justify-content-center">
            <div class="col-md-4">
                <select id="roomFilter" class="form-select">
                    <option value="">全部自習室</option>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?= $room['room_name'] ?>"><?= htmlspecialchars($room['room_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <select id="dateFilter" class="form-select">
                    <option value="">全部日期</option>
                    <?php foreach ($dates as $date): ?>
                        <option value="<?= $date ?>"><?= $date ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <table class="table table-striped table-bordered" id="reservationTable">
            <thead class="table-dark">
                <tr>
                    <th>自習室</th>
                    <th>座位編號</th>
                    <th>使用者</th>
                    <th>姓名</th>
                    <th>日期</th>
                    <th>時間</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $res): ?>
                    <tr data-room="<?= htmlspecialchars($res['room_name']) ?>" data-date="<?= $res['date'] ?>">
                        <td><?= htmlspecialchars($res['room_name']) ?></td>
                        <td><?= htmlspecialchars($res['seat_id']) ?></td>
                        <td><?= htmlspecialchars($res['username']) ?></td>
                        <td><?= htmlspecialchars($res['name']) ?></td>
                        <td><?= htmlspecialchars($res['date']) ?></td>
                        <td><?= htmlspecialchars($res['start_time']) ?> - <?= htmlspecialchars($res['end_time']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="mt-3">
            <a href="admin.php" class="btn btn-secondary fw-bold">返回首頁</a>
        </div>
    </main>

    <script>
        const roomFilter = document.getElementById('roomFilter');
        const dateFilter = document.getElementById('dateFilter');
        const tableRows = document.querySelectorAll('#reservationTable tbody tr');

        function applyFilters() {
            const selectedRoom = roomFilter.value;
            const selectedDate = dateFilter.value;

            tableRows.forEach(row => {
                const rowRoom = row.dataset.room;
                const rowDate = row.dataset.date;
                const matchRoom = !selectedRoom || rowRoom === selectedRoom;
                const matchDate = !selectedDate || rowDate === selectedDate;
                row.style.display = matchRoom && matchDate ? '' : 'none';
            });
        }

        roomFilter.addEventListener('change', applyFilters);
        dateFilter.addEventListener('change', applyFilters);
    </script>
</body>
</html>
