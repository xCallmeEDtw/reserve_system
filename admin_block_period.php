<?php
require_once(__DIR__ . '/dbconfig.php');
session_start();

// 確認是管理員
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

// 預設選擇第一個自習室
$selectedRoomId = $_GET['room_id'] ?? $rooms[0]['room_id'] ?? null;

// 刪除不開放時段
if (isset($_GET['delete_block'])) {
    $blockId = (int)$_GET['delete_block'];
    mysqli_query($conn, "DELETE FROM BlockedPeriod WHERE block_id = $blockId");
    header("Location: admin_block_period.php?room_id=$selectedRoomId");
    exit;
}

// 新增不開放時段
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_block'])) {
    $roomId = (int)$_POST['room_id'];
    $startDate = $_POST['start_date'] . ' 00:00:00';
    $endDate   = $_POST['end_date']   . ' 23:59:59';
    $reason = $_POST['reason'];

    $sql = "INSERT INTO BlockedPeriod (room_id, start_time, end_time, reason) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isss", $roomId, $startDate, $endDate, $reason);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: admin_block_period.php?room_id=$roomId");
    exit;
}

// 取得現有不開放時段
$blockedPeriods = [];
if ($selectedRoomId) {
    $query = "
        SELECT b.block_id, b.start_time, b.end_time, b.reason, s.room_name
        FROM BlockedPeriod b
        JOIN StudyRoom s ON b.room_id = s.room_id
        WHERE b.room_id = $selectedRoomId
        ORDER BY b.start_time ASC
    ";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $blockedPeriods[] = $row;
    }
}
?>
<!doctype html>
<html lang="zh-hant">

<head>
    <meta charset="utf-8">
    <title>設定不開放時段 - 自習室座位預約系統</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body class="text-center">
<main class="form-signin w-100 m-auto">
    <h2 class="h2 mb-2 fw-bold">自習室座位預約系統 - 設定不開放時段</h2>

    <!-- 選擇自習室 -->
    <form method="GET" action="">
        <select name="room_id" class="form-select mb-3" onchange="this.form.submit()">
            <?php foreach ($rooms as $room): ?>
                <option value="<?= $room['room_id'] ?>" <?= $room['room_id'] == $selectedRoomId ? 'selected' : '' ?>>
                    <?= htmlspecialchars($room['room_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <!-- 新增不開放時段 -->
    <form method="POST" action="">
        <input type="hidden" name="room_id" value="<?= $selectedRoomId ?>">
        <input type="hidden" name="add_block" value="1">

        <div class="form-floating mb-3">
            <input type="text" class="form-control" name="start_date" id="start_date" placeholder="開始日期" required>
            <label for="start_date">開始日期</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" name="end_date" id="end_date" placeholder="結束日期" required>
            <label for="end_date">結束日期</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" name="reason" placeholder="原因 (選填)">
            <label>原因 (選填)</label>
        </div>

        <button class="w-100 btn btn-lg btn-primary fw-bold mb-3" type="submit">新增不開放時段</button>
    </form>

    <!-- 已設定的不開放時段 -->
    <div class="mb-3">
        <h5 class="fw-bold">已設定的不開放時段</h5>
        <?php if (count($blockedPeriods) > 0): ?>
            <ul class="list-group">
                <?php foreach ($blockedPeriods as $period): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="me-auto text-start">
                            <div><strong><?= htmlspecialchars($period['room_name']) ?></strong></div>
                            <div><?= htmlspecialchars($period['start_time']) ?> - <?= htmlspecialchars($period['end_time']) ?></div>
                            <div class="text-muted"><?= htmlspecialchars($period['reason']) ?></div>
                        </div>
                        <a class="btn btn-sm btn-danger fw-bold ms-2"
                           href="?room_id=<?= $selectedRoomId ?>&delete_block=<?= $period['block_id'] ?>"
                           onclick="return confirm('確定要刪除這個不開放時段？')">
                            刪除
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>目前尚未設定不開放時段。</p>
        <?php endif; ?>
    </div>

    <div class="mt-3">
        <a href="admin.php" class="btn btn-secondary w-100 fw-bold">返回管理介面</a>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("#start_date", {
        dateFormat: "Y-m-d",
        locale: "zh"
    });
    flatpickr("#end_date", {
        dateFormat: "Y-m-d",
        locale: "zh"
    });
</script>
</body>
</html>
