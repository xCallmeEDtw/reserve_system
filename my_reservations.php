<?php
session_start();
require_once(__DIR__ . '/dbconfig.php');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// 取得目前登入者的 user_id
$username = $_SESSION['memberId'];
$userQuery = "SELECT user_id FROM user WHERE username = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("s", $username);
$userStmt->execute();
$userResult = $userStmt->get_result();
$user = $userResult->fetch_assoc();
$userId = $user['user_id'];
$userStmt->close();

// 撈取我的預約紀錄
$reservationsQuery = "
    SELECT 
        r.reservation_id,
        r.date,
        r.start_time,
        r.end_time,
        s.seat_id,
        sr.room_name,
        sr.location
    FROM Reservation r
    JOIN Seat s ON r.seat_id = s.seat_id
    JOIN StudyRoom sr ON s.room_id = sr.room_id
    WHERE r.user_id = ?
    ORDER BY r.date DESC, r.start_time ASC
";
$reservationsStmt = $conn->prepare($reservationsQuery);
$reservationsStmt->bind_param("i", $userId);
$reservationsStmt->execute();
$reservationsResult = $reservationsStmt->get_result();
$reservations = [];
while ($row = $reservationsResult->fetch_assoc()) {
    $reservations[] = $row;
}
$reservationsStmt->close();
$conn->close();
?>

<!doctype html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>我的預約紀錄 - 自習室座位預約系統</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="text-center">
    <main class="container mt-5">
        <h2 class="h2 mb-4 fw-bold">自習室座位預約系統 - 我的預約紀錄</h2>

        <?php if (empty($reservations)): ?>
            <p>目前沒有任何預約紀錄。</p>
        <?php else: ?>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>自習室名稱</th>
                        <th>位置</th>
                        <th>座位編號</th>
                        <th>日期</th>
                        <th>時間</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $res): ?>
                        <tr id="reservation-<?= $res['reservation_id'] ?>">
                            <td><?= htmlspecialchars($res['room_name']) ?></td>
                            <td><?= htmlspecialchars($res['location']) ?></td>
                            <td><?= htmlspecialchars($res['seat_id']) ?></td>
                            <td><?= htmlspecialchars($res['date']) ?></td>
                            <td><?= htmlspecialchars($res['start_time']) ?> - <?= htmlspecialchars($res['end_time']) ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm" onclick="cancelReservation(<?= $res['reservation_id'] ?>)">取消預約</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="mt-3">
            <a href="welcome.php" class="btn btn-secondary fw-bold">返回首頁</a>
        </div>
    </main>

    <script>
        function cancelReservation(reservationId) {
            if (!confirm("確定要取消這筆預約嗎？")) return;

            fetch('api/cancel_reservation.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `reservation_id=${reservationId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('已成功取消預約！');
                    document.getElementById(`reservation-${reservationId}`).remove();
                } else {
                    alert(data.message || '取消失敗');
                }
            })
            .catch(err => {
                console.error(err);
                alert('發生錯誤，請稍後再試。');
            });
        }
    </script>
</body>

</html>
