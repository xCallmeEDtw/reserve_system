<?php
require_once(__DIR__ . '/dbconfig.php');

session_start();

// 確認是管理員
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["memberRole"] !== 'admin') {
    header("location: login.php");
    exit;
}

// 新增自習室提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roomName = $_POST["room_name"];
    $location = $_POST["location"];
    $rows = (int)$_POST["rows"];
    $cols = (int)$_POST["cols"];

    // 插入 StudyRoom
    $insertRoomSql = "INSERT INTO StudyRoom (room_name, location, row_count, col_count) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insertRoomSql);
    mysqli_stmt_bind_param($stmt, "ssii", $roomName, $location, $rows, $cols);
    mysqli_stmt_execute($stmt);

    // 取得新插入的 room_id
    $roomId = mysqli_insert_id($conn);

    // 插入 Seat
    $insertSeatSql = "INSERT INTO Seat (room_id, has_power, status) VALUES (?, 0, 'available')";
    $seatStmt = mysqli_prepare($conn, $insertSeatSql);
    for ($i = 0; $i < $rows * $cols; $i++) {
        mysqli_stmt_bind_param($seatStmt, "i", $roomId);
        mysqli_stmt_execute($seatStmt);
    }

    mysqli_stmt_close($seatStmt);
    mysqli_stmt_close($stmt);

    echo '<script>alert("自習室新增成功！"); window.location.href = "admin.php";</script>';
    exit;
}
?>

<!doctype html>
<html lang="zh-hant">

<head>
    <meta charset="utf-8">
    <title>新增自習室 - 自習室座位預約系統</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous">
    <link href="login.css" rel="stylesheet">
    <style>
        .seat-preview {
            display: grid;
            gap: 5px;
            justify-content: center;
        }

        .seat-box {
            width: 40px;
            height: 40px;
            background-color: black;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body class="text-center">
    <main class="form-signin w-100 m-auto">
        <h2 class="h2 mb-2 fw-bold">自習室座位預約系統 - 管理介面</h2>
        <h1 class="h1 mb-4 fw-bold">新增自習室</h1>

        <form method="POST" action="">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="roomName" name="room_name" placeholder="自習室名稱" required>
                <label for="roomName">自習室名稱</label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="location" name="location" placeholder="自習室位置" required>
                <label for="location">自習室位置</label>
            </div>

            <div class="form-floating mb-3">
                <input type="number" class="form-control" id="rows" name="rows" placeholder="行數 (m)" value="3" required>
                <label for="rows">行數 (m)</label>
            </div>

            <div class="form-floating mb-3">
                <input type="number" class="form-control" id="cols" name="cols" placeholder="列數 (n)" value="3" required>
                <label for="cols">列數 (n)</label>
            </div>

            <div class="mb-3">
                <h5 class="fw-bold">座位預覽</h5>
                <div id="seatPreview" class="seat-preview"></div>
            </div>

            <button class="w-100 btn btn-lg btn-primary fw-bold" type="submit">新增自習室</button>
        </form>

        <div class="mt-2">
            <a href="admin.php" class="btn btn-secondary w-100 fw-bold">返回管理介面</a>
        </div>
    </main>

    <script>
        const rowsInput = document.getElementById('rows');
        const colsInput = document.getElementById('cols');
        const seatPreview = document.getElementById('seatPreview');

        function updateSeatPreview() {
            const rows = parseInt(rowsInput.value) || 0;
            const cols = parseInt(colsInput.value) || 0;

            seatPreview.style.gridTemplateRows = `repeat(${rows}, 1fr)`;
            seatPreview.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;

            seatPreview.innerHTML = '';
            for (let i = 0; i < rows * cols; i++) {
                const seatBox = document.createElement('div');
                seatBox.className = 'seat-box';
                seatBox.textContent = '';
                seatPreview.appendChild(seatBox);
            }
        }

        rowsInput.addEventListener('input', updateSeatPreview);
        colsInput.addEventListener('input', updateSeatPreview);

        // 初始化預覽
        updateSeatPreview();
    </script>
</body>

</html>