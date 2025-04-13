<?php
require_once(__DIR__ . '/dbconfig.php');

session_start();
if (isset($_SESSION["loggedin"])) {
    header("location: ./welcome.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 取得 POST 過來的資料
    $username = mb_convert_case($_POST["username"], MB_CASE_UPPER, "UTF-8");
    $password = $_POST["password"];

    // 以帳號進資料庫查詢，順便把 role 撈出來
    $sql = "SELECT `username`, `password`, `name`, `email`, `role` FROM `user` WHERE `username`=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $result_username, $result_password, $result_name, $result_email, $result_role);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // 驗證密碼
    if (password_verify($password, $result_password)) {
        // 密碼通過驗證
        session_start();
        // 把資料存入 Session
        $_SESSION["loggedin"] = true;
        $_SESSION["memberId"] = $result_username;
        $_SESSION["memberName"] = $result_name;
        $_SESSION["memberEmail"] = $result_email;
        $_SESSION["memberRole"] = $result_role;

        // 根據角色跳轉頁面
        if ($result_role === 'admin') {
            header("location: ./admin.php");
        } else {
            header("location: ./welcome.php");
        }
        exit;
    } else {
        // 密碼驗證失敗
        $test = password_hash("123", PASSWORD_DEFAULT);
        echo '<script>alert("帳號或密碼錯誤.\nIncorrect ID or Password.");</script>';
        echo '<script>console.log("' . $test . '");</script>';
    }
}

// 關閉連線
mysqli_close($conn);
?>
<!doctype html>
<html lang="zh-hant">

<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous">
    <link href="login.css" rel="stylesheet">
    <title>自習室座位預約系統</title>
</head>

<body class="text-center">
    <main class="form-signin w-100 m-auto">
        <form method="POST" action="">
            <h2 class="h2 mb-2 fw-bold">自習室座位預約系統</h2>
            <h1 class="h1 mb-4 fw-bold">登入</h1>

            <div class="mt-2 mb-3">
                <a href="register.php" class="btn btn-secondary w-100 fw-bold">註冊新帳號</a>
            </div>

            <div class="form-floating">
                <input type="text" class="form-control" id="inputUsername" placeholder="帳號" name="username" required>
                <label for="inputUsername">帳號</label>
            </div>

            <div class="form-floating">
                <input type="password" class="form-control" id="inputPassword" placeholder="密碼" name="password" required>
                <label for="inputPassword">密碼</label>
            </div>

            <button class="w-100 btn btn-lg btn-primary fw-bold" type="submit">登入</button>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous">
    </script>
</body>

</html>
