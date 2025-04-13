<?php
session_start();

// 確認管理員身份
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["memberRole"] !== 'admin') {
    header("location: login.php");
    exit;
}

$memberName = $_SESSION["memberName"];
?>
<!doctype html>
<html lang="zh-hant">

<head>
    <meta charset="utf-8">
    <title>自習室座位預約系統 - 管理介面</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous">
    <link href="login.css" rel="stylesheet">
</head>

<body class="text-center">
    <main class="form-signin w-100 m-auto">
        <h2 class="h2 mb-2 fw-bold">自習室座位預約系統</h2>
        <h1 class="h1 mb-4 fw-bold" style="white-space: nowrap;">歡迎回來,<?php echo htmlspecialchars($memberName); ?>
        <h1>

        <div class="d-grid gap-2 mb-3">
            <a href="admin_seat.php" class="btn btn-primary btn-lg fw-bold">查詢 / 維護自習室座位資訊</a>
            <a href="admin_reservations.php" class="btn btn-success btn-lg fw-bold">查詢所有預約紀錄</a>
            <a href="admin_block_period.php" class="btn btn-warning btn-lg fw-bold">設定不開放借用日期</a>
            <a href="admin_add_room.php" class="btn btn-info btn-lg fw-bold">新增自習室</a>
            <a href="admin_manage_room.php" class="btn btn-secondary btn-lg fw-bold">管理自習室</a>
            <a href="logout.php" class="btn btn-dark btn-lg fw-bold">登出</a>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous">
    </script>
</body>

</html>
