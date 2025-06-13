<?php
session_start();
require_once __DIR__ . '/config/connect.php'; // Đường dẫn tương đối

$message = '';

if (isset($_POST["submit"])) {
    $hoten = $_POST["hoten"];
    $email = $_POST["email"];
    $sodienthoai = $_POST["sodienthoai"];
    $matkhau = md5($_POST["matkhau"]);
    $matkhau_confirm = md5($_POST["matkhau_confirm"]);

    if ($matkhau != $matkhau_confirm) {
        $message = '<div class="alert-error">Mật khẩu xác nhận không khớp.</div>';
    } else {
        // Kiểm tra email đã tồn tại chưa
        $sql_check_email = "SELECT id_khach FROM khach_hang WHERE email = '$email'";
        $query_check_email = mysqli_query($conn, $sql_check_email);
        if (mysqli_num_rows($query_check_email) > 0) {
            $message = '<div class="alert-error">Email này đã được đăng ký. Vui lòng sử dụng email khác.</div>';
        } else {
            $sql_register = "INSERT INTO khach_hang (hoten, sodienthoai, email, matkhau, ngay_dangky, trangthai)
                             VALUES ('$hoten', '$sodienthoai', '$email', '$matkhau', NOW(), 0)";
            $query_register = mysqli_query($conn, $sql_register);

            if ($query_register) {
                $message = '<div class="alert-success">Đăng ký tài khoản thành công! Vui lòng <a href="login.php">đăng nhập</a> để tiếp tục.</div>';
            } else {
                $message = '<div class="alert-error">Đăng ký thất bại. Vui lòng thử lại.</div>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản khách hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="css/frontend.css">
    <style>
        .auth-form-container { margin-top: 50px; margin-bottom: 50px; }
        .auth-form-container h2 { font-size: 28px; }
    </style>
</head>
<body>
    <div class="container auth-form-container">
        <h2>Đăng ký tài khoản</h2>
        <?php echo $message; ?>
        <form action="" method="POST" class="form">
            <div class="input-box">
                <label for="hoten">Họ tên:</label>
                <input type="text" id="hoten" name="hoten" required>
            </div>
            <div class="input-box">
                <label for="sodienthoai">Số điện thoại:</label>
                <input type="text" id="sodienthoai" name="sodienthoai" required>
            </div>
            <div class="input-box">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-box">
                <label for="matkhau">Mật khẩu:</label>
                <input type="password" id="matkhau" name="matkhau" required>
            </div>
            <div class="input-box">
                <label for="matkhau_confirm">Xác nhận mật khẩu:</label>
                <input type="password" id="matkhau_confirm" name="matkhau_confirm" required>
            </div>
            <button type="submit" name="submit">Đăng ký</button>
            <p class="text-center my-3">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
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