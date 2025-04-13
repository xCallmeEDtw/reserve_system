<?php
require_once(__DIR__ . '/dbconfig.php');

session_start();

// 確認管理員身份
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["memberRole"] !== 'admin') {
    header("location: login.php");
    exit;
}

// 取得所有自習室
$roomsResult = mysqli_query($conn, "SELECT * FROM StudyRoom");
$rooms = [];
while ($row = mysqli_fetch_assoc($roomsResult)) {
    $rooms[] = $row;
}

// 選擇的自習室 id
$selectedRoomId = $_GET['room_id'] ?? $rooms[0]['room_id'] ?? null;

// 更新自習室基本資料
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_room'])) {
    $roomId = (int)$_POST['room_id'];
    $roomName = $_POST['room_name'];
    $location = $_POST['location'];
    $rows = (int)$_POST['rows'];
    $cols = (int)$_POST['cols'];

    // 更新 StudyRoom
    $updateRoomSql = "UPDATE StudyRoom SET room_name = ?, location = ?, row_count = ?, col_count = ? WHERE room_id = ?";
    $stmt = mysqli_prepare($conn, $updateRoomSql);
    mysqli_stmt_bind_param($stmt, "ssiii", $roomName, $location, $rows, $cols, $roomId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // 刪除原有座位
    mysqli_query($conn, "DELETE FROM Seat WHERE room_id = $roomId");

    // 重新插入座位
    $insertSeatSql = "INSERT INTO Seat (room_id, has_power, status) VALUES (?, 0, 'available')";
    $seatStmt = mysqli_prepare($conn, $insertSeatSql);
    for ($i = 0; $i < $rows * $cols; $i++) {
        mysqli_stmt_bind_param($seatStmt, "i", $roomId);
        mysqli_stmt_execute($seatStmt);
    }
    mysqli_stmt_close($seatStmt);

    header("Location: admin_manage_room.php?room_id=$roomId");
    exit;
}

// 取得選擇自習室的資料
$currentRoom = null;
$seats = [];
if ($selectedRoomId) {
    $roomResult = mysqli_query($conn, "SELECT * FROM StudyRoom WHERE room_id = $selectedRoomId");
    $currentRoom = mysqli_fetch_assoc($roomResult);

    // 取得座位資料
    $seatResult = mysqli_query($conn, "SELECT * FROM Seat WHERE room_id = $selectedRoomId ORDER BY seat_id ASC");
    while ($row = mysqli_fetch_assoc($seatResult)) {
        $seats[] = $row;
    }
}

?>

<!doctype html>
<html lang="zh-hant">

<head>
    <meta charset="utf-8">
    <title>管理自習室 - 自習室座位預約系統</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous">
    <style>
        .seat-grid {
            display: grid;
            gap: 5px;
            justify-content: center;
        }
        .seat-box {
            width: 50px;
            height: 50px;
            background-color: black;
            color: white;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }
        .seat-box .icon {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 12px;
            height: 12px;
        }
        .seat-box.not-seat {
            background-color: gray;
        }
    </style>
</head>

<body class="text-center">
    <main class="form-signin w-100 m-auto">
        <h2 class="h2 mb-2 fw-bold">自習室座位預約系統 - 管理自習室</h2>

        <form method="GET" action="">
            <select name="room_id" class="form-select mb-3" onchange="this.form.submit()">
                <?php foreach ($rooms as $room): ?>
                    <option value="<?= $room['room_id'] ?>" <?= $room['room_id'] == $selectedRoomId ? 'selected' : '' ?>>
                        <?= htmlspecialchars($room['room_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if ($currentRoom): ?>
            <form method="POST" action="">
                <input type="hidden" name="room_id" value="<?= $currentRoom['room_id'] ?>">
                <input type="hidden" name="update_room" value="1">

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="room_name" value="<?= htmlspecialchars($currentRoom['room_name']) ?>" required>
                    <label>自習室名稱</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($currentRoom['location']) ?>" required>
                    <label>自習室位置</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="number" class="form-control" name="rows" value="<?= $currentRoom['row_count'] ?>" required>
                    <label>行數 (m)</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="number" class="form-control" name="cols" value="<?= $currentRoom['col_count'] ?>" required>
                    <label>列數 (n)</label>
                </div>

                <button class="w-100 btn btn-lg btn-primary fw-bold mb-3" type="submit">更新自習室</button>
            </form>

            <div id="seatGrid" class="seat-grid" style="grid-template-columns: repeat(<?= $currentRoom['col_count'] ?>, 1fr); grid-template-rows: repeat(<?= $currentRoom['row_count'] ?>, 1fr);">
                <?php foreach ($seats as $seat): ?>
                    <div class="seat-box <?= $seat['status'] === 'not_seat' ? 'not-seat' : '' ?>" data-seat-id="<?= $seat['seat_id'] ?>" data-has-power="<?= $seat['has_power'] ?>" data-status="<?= $seat['status'] ?>">
                        <?= $seat['status'] === 'not_seat' ? 'X' : '' ?>
                        <?php if ($seat['has_power']): ?>
                            <img src="power-icon.png" class="icon" alt="有插座">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="mt-3">
            <a href="admin.php" class="btn btn-secondary w-100 fw-bold">返回管理介面</a>
        </div>
    </main>

    <script>
        document.querySelectorAll('.seat-box').forEach(box => {
            box.addEventListener('click', () => {
                const seatId = box.getAttribute('data-seat-id');
                let hasPower = parseInt(box.getAttribute('data-has-power'));
                let status = box.getAttribute('data-status');

                if (status === 'available' && hasPower === 0) {
                    hasPower = 1;
                } else if (status === 'available' && hasPower === 1) {
                    status = 'not_seat';
                    hasPower = 0;
                } else if (status === 'not_seat') {
                    status = 'available';
                    hasPower = 0;
                }

                fetch('update_seat.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `seat_id=${seatId}&has_power=${hasPower}&status=${status}`
                }).then(() => location.reload());
            });
        });
    </script>
</body>

</html>