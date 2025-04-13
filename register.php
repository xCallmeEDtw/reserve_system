<?php
require_once(__DIR__ . '/dbconfig.php');

session_start();

// 如果已經登入就跳轉
if (isset($_SESSION["loggedin"])) {
    header("location: ./welcome.php");
    exit;
}

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mb_convert_case($_POST["username"], MB_CASE_UPPER, "UTF-8");
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $email = $_POST["email"];
    $name = $_POST["name"];
    $role = 'user'; // 固定為 user

    // 簡單驗證
    if ($password !== $confirm_password) {
        echo '<script>alert("密碼與確認密碼不相符！");</script>';
    } else {
        // 檢查是否已有相同帳號
        $check_sql = "SELECT username FROM user WHERE username = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $username);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            echo '<script>alert("帳號已被使用！");</script>';
        } else {
            // 插入新使用者
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO user (username, password, name, email, role) VALUES (?, ?, ?, ?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "sssss", $username, $password_hash, $name, $email, $role);

            if (mysqli_stmt_execute($insert_stmt)) {
                // 成功後跳轉到 login.php
                echo '<script>alert("註冊成功，請登入！"); window.location.href = "login.php";</script>';
                exit;
            } else {
                echo '<script>alert("註冊失敗，請重試！");</script>';
            }

            mysqli_stmt_close($insert_stmt);
        }

        mysqli_stmt_close($check_stmt);
    }
}

// 關閉資料庫連線
mysqli_close($conn);
?>
<!doctype html>
<html lang="zh-hant">

<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="login.css" rel="stylesheet">
    <title>註冊新帳號</title>
</head>

<body class="text-center">
    <main class="form-signin w-100 m-auto">
        <form method="POST" action="">
            <h2 class="h2 mb-2 fw-bold">自習室座位預約系統</h2>
            <h1 class="h1 mb-4 fw-bold">註冊新帳號</h1>

            <div class="form-floating">
                <input type="text" class="form-control" id="inputUsername" placeholder="帳號" name="username" required>
                <label for="inputUsername">帳號</label>
            </div>
            <div class="form-floating">
                <input type="text" class="form-control" id="inputName" placeholder="姓名" name="name" required>
                <label for="inputName">姓名</label>
            </div>
            <div class="form-floating">
                <input type="email" class="form-control" id="inputEmail" placeholder="電子郵件" name="email" required>
                <label for="inputEmail">電子郵件</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="inputPassword" placeholder="密碼" name="password" required>
                <label for="inputPassword">密碼</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="inputConfirmPassword" placeholder="確認密碼" name="confirm_password" required>
                <label for="inputConfirmPassword">確認密碼</label>
            </div>

            <button class="w-100 btn btn-lg btn-primary fw-bold" type="submit">註冊</button>

            <div class="mt-2">
                <a href="login.php" class="btn btn-secondary w-100 fw-bold">返回登入</a>
            </div>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>
