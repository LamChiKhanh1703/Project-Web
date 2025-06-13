<?php
session_start();
require_once __DIR__ . '/config/connect.php'; // Đường dẫn tương đối

$message = '';

if (isset($_POST["submit"])) {
    $email = $_POST["email"];
    $pass = md5($_POST["password"]);

    $sql = "SELECT * FROM khach_hang WHERE email = '$email' AND matkhau = '$pass'";
    $query = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($query);

    if ($num == 0) {
        $message = '<div class="alert-error">Email hoặc mật khẩu không đúng.</div>';
    } else {
        $row = mysqli_fetch_assoc($query);
        if ($row["trangthai"] == 0) { // 0: active
            $_SESSION["user_id"] = $row["id_khach"];
            $_SESSION["user_name"] = $row["hoten"];
            $_SESSION["user_email"] = $row["email"];
            header("Location: index.php");
            exit();
        } else {
            $message = '<div class="alert-error">Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập khách hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="css/frontend.css">
    <style>
        /* Tùy chỉnh form.css nếu cần */
        .auth-form-container { margin-top: 50px; margin-bottom: 50px; }
        .auth-form-container h2 { font-size: 28px; }
    </style>
</head>
<body>
    <div class="container auth-form-container">
        <h2>Đăng nhập</h2>
        <?php echo $message; ?>
        <form action="" method="POST" class="form">
            <div class="input-box">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-box">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="submit">Đăng nhập</button>
            <p class="text-center my-3">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
        </form>
    </div>
</body>
</html>
<?php
if (isset($conn) && is_object($conn) && get_class($conn) === 'mysqli') {
    if (mysqli_thread_id($conn)) {
        mysqli_close($conn);
    }
}
?>