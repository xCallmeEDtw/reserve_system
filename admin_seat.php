<?php
require_once(__DIR__ . '/dbconfig.php');
session_start();

// 管理員身份驗證
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['memberRole'] !== 'admin') {
    header("location: login.php");
    exit;
}

header('Content-Type: text/html; charset=utf-8');

// 取自習室資料
$roomsResult = mysqli_query($conn, "SELECT * FROM StudyRoom");
$rooms = [];
while ($row = mysqli_fetch_assoc($roomsResult)) {
    $rooms[] = $row;
}
?>

<!doctype html>
<html lang="zh-Hant">

<head>
    <meta charset="utf-8">
    <title>管理查詢座位資訊 - 自習室座位預約系統</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <style>
        .seat-grid {
            display: grid;
            gap: 5px;
            justify-content: center;
            margin-top: 20px;
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
            font-weight: bold;
            border: 2px solid transparent;
        }
        .seat-box.reserved {
            background-color: red;
        }
        .seat-box.not-seat {
            background-color: gray;
        }
        .power-icon {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 15px;
            height: 15px;
        }
    </style>
</head>

<body class="text-center">
    <main class="form-signin w-100 m-auto">
        <h2 class="h2 mb-3 fw-bold">自習室座位預約系統 - 管理查詢座位</h2>

        <!-- 選擇教室和日期時間 -->
        <div class="d-flex justify-content-between mb-3">
            <select id="roomSelect" class="form-select w-50 me-2">
                <?php foreach ($rooms as $room): ?>
                    <option value="<?= $room['room_id'] ?>"><?= htmlspecialchars($room['room_name']) ?></option>
                <?php endforeach; ?>
            </select>

            <input type="text" id="datePicker" class="form-control w-50" placeholder="選擇日期">
        </div>

        <div class="d-flex justify-content-between mb-3">
            <select id="startTimeSelect" class="form-select w-50 me-2">
                <option value="">開始時間</option>
            </select>
            <select id="endTimeSelect" class="form-select w-50">
                <option value="">結束時間</option>
            </select>
        </div>

        <div id="statusMessage" class="text-danger mb-2"></div>

        <!-- 預覽座位 -->
        <div id="seatGrid" class="seat-grid"></div>

        <div class="mt-3">
            <a href="admin.php" class="btn btn-secondary w-100 fw-bold">返回管理首頁</a>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        const roomSelect = document.getElementById('roomSelect');
        const datePicker = document.getElementById('datePicker');
        const startTimeSelect = document.getElementById('startTimeSelect');
        const endTimeSelect = document.getElementById('endTimeSelect');
        const seatGrid = document.getElementById('seatGrid');
        const statusMessage = document.getElementById('statusMessage');

        flatpickr(datePicker, {
            dateFormat: "Y-m-d",
            defaultDate: new Date(),
            onChange: loadAvailability
        });

        function populateTimeOptions() {
            startTimeSelect.innerHTML = '<option value="">開始時間</option>';
            endTimeSelect.innerHTML = '<option value="">結束時間</option>';
            for (let i = 8; i <= 20; i++) {
                const timeStr = `${i.toString().padStart(2, '0')}:00`;
                startTimeSelect.innerHTML += `<option value="${timeStr}">${timeStr}</option>`;
                endTimeSelect.innerHTML += `<option value="${timeStr}">${timeStr}</option>`;
            }
        }

        populateTimeOptions();

        roomSelect.addEventListener('change', loadAvailability);
        startTimeSelect.addEventListener('change', validateTime);
        endTimeSelect.addEventListener('change', validateTime);

        function validateTime() {
            const start = startTimeSelect.value;
            const end = endTimeSelect.value;
            if (start && end && start >= end) {
                statusMessage.textContent = "結束時間必須大於開始時間";
                seatGrid.innerHTML = '';
            } else {
                statusMessage.textContent = "";
                loadAvailability();
            }
        }

        function loadAvailability() {
            seatGrid.innerHTML = '';

            const roomId = roomSelect.value;
            const date = datePicker.value;
            const startTime = startTimeSelect.value;
            const endTime = endTimeSelect.value;

            if (!roomId || !date || !startTime || !endTime) return;

            fetch(`api/load_seats.php?room_id=${roomId}&date=${date}&start_time=${startTime}&end_time=${endTime}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.seats || data.seats.length === 0) {
                        seatGrid.innerHTML = '<p>無座位資料</p>';
                        return;
                    }

                    const cols = data.seats[data.seats.length - 1].col;
                    seatGrid.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;
                    seatGrid.innerHTML = '';

                    data.seats.forEach(seat => {
                        const seatDiv = document.createElement('div');
                        seatDiv.className = 'seat-box';
                        if (seat.status === 'reserved') seatDiv.classList.add('reserved');
                        if (seat.status === 'not_seat') seatDiv.classList.add('not-seat');
                        seatDiv.textContent = seat.status === 'not_seat' ? 'X' : '';

                        // 插座小圖示
                        if (seat.has_power == 1 && seat.status !== 'not_seat') {
                            const img = document.createElement('img');
                            img.src = 'power-icon.png';
                            img.className = 'power-icon';
                            seatDiv.appendChild(img);
                        }

                        seatGrid.appendChild(seatDiv);
                    });
                });
        }
    </script>
</body>

</html>
