<?php
require_once(__DIR__ . '/dbconfig.php');
session_start();

// 確認是否登入
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// 取得所有自習室
$roomsResult = mysqli_query($conn, "SELECT * FROM StudyRoom");
$rooms = [];
while ($row = mysqli_fetch_assoc($roomsResult)) {
    $rooms[] = $row;
}
?>

<!doctype html>
<html lang="zh-hant">

<head>
    <meta charset="utf-8">
    <title>預約座位 - 自習室座位預約系統</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
            cursor: pointer;
            border: 2px solid transparent;
        }
        .seat-box.selected {
            border: 2px solid limegreen;
        }
        .seat-box.reserved {
            background-color: red;
            cursor: not-allowed;
        }
        .seat-box.not-seat {
            background-color: gray;
            cursor: not-allowed;
        }
    </style>
</head>

<body class="text-center">
    <main class="form-signin w-100 m-auto">
        <h2 class="h2 mb-2 fw-bold">自習室座位預約系統 - 預約座位</h2>

        <!-- 自習室與日期選擇 -->
        <div class="d-flex justify-content-between mb-3">
            <select id="roomSelect" class="form-select w-50 me-2">
                <?php foreach ($rooms as $room): ?>
                    <option value="<?= $room['room_id'] ?>"><?= htmlspecialchars($room['room_name']) ?></option>
                <?php endforeach; ?>
            </select>

            <input type="text" id="datePicker" class="form-control w-50" placeholder="選擇日期">
        </div>

        <!-- 時間選擇 -->
        <div class="d-flex justify-content-between mb-3">
            <select id="startTimeSelect" class="form-select w-50 me-2">
                <option value="">開始時間</option>
            </select>
            <select id="endTimeSelect" class="form-select w-50">
                <option value="">結束時間</option>
            </select>
        </div>

        <div id="statusMessage" class="text-danger mb-2"></div>

        <!-- 座位預覽 -->
        <div id="seatGrid" class="seat-grid"></div>

        <!-- 確認預約 -->
        <button id="confirmBtn" class="w-100 btn btn-lg btn-primary fw-bold mt-3" disabled>確認預約</button>

        <div class="mt-3">
            <a href="welcome.php" class="btn btn-secondary w-100 fw-bold">返回首頁</a>
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
        const confirmBtn = document.getElementById('confirmBtn');

        let selectedSeatId = null;

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
                confirmBtn.disabled = true;
                seatGrid.innerHTML = '';
            } else {
                statusMessage.textContent = "";
                loadAvailability();
            }
        }

        function loadAvailability() {
            selectedSeatId = null;
            confirmBtn.disabled = true;
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
                        seatDiv.dataset.seatId = seat.seat_id;

                        if (seat.has_power && seat.status !== 'not_seat') {
                            const powerIcon = document.createElement('img');
                            powerIcon.src = 'power-icon.png';
                            powerIcon.style.position = 'absolute';
                            powerIcon.style.right = '2px';
                            powerIcon.style.bottom = '2px';
                            powerIcon.style.width = '16px';
                            powerIcon.style.height = '16px';
                            seatDiv.appendChild(powerIcon);
                        }

                        if (!seatDiv.classList.contains('reserved') && !seatDiv.classList.contains('not-seat')) {
                            seatDiv.addEventListener('click', () => {
                                document.querySelectorAll('.seat-box.selected').forEach(box => box.classList.remove('selected'));
                                seatDiv.classList.add('selected');
                                selectedSeatId = seat.seat_id;
                                confirmBtn.disabled = false;
                            });
                        }

                        seatGrid.appendChild(seatDiv);
                    });
                });
        }

        confirmBtn.addEventListener('click', () => {
            if (!selectedSeatId) return;
            const roomId = roomSelect.value;
            const date = datePicker.value;
            const startTime = startTimeSelect.value;
            const endTime = endTimeSelect.value;

            fetch('api/book_seat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `seat_id=${selectedSeatId}&date=${date}&start_time=${startTime}&end_time=${endTime}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('預約成功！');
                    location.reload();
                } else {
                    alert(data.message || '預約失敗');
                }
            });
        });
    </script>
</body>

</html>