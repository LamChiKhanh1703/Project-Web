<?php
// File: Webdochoi/change-password.php

// 1. Bao gồm header.php (header.php nên gọi session_start() và require_once config/connect.php)
include_once "includes/header.php";

// 2. Kiểm tra xem người dùng đã đăng nhập chưa và lấy id_khach từ session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?message=Vui lòng đăng nhập để đổi mật khẩu.");
    exit();
}
$id_khach = $_SESSION['user_id'];

$message = ''; 

// 3. Xử lý việc gửi form đổi mật khẩu (phương thức POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $old_pass_input = $_POST['old-pass'];
    $new_pass_input = $_POST['new-pass'];
    $new_pass2_input = $_POST['new-pass2'];

    if (!$conn) {
        $message = '<div class="alert alert-danger">Lỗi kết nối cơ sở dữ liệu.</div>';
    } else {
        $sql_check_pass = "SELECT matkhau FROM khach_hang WHERE id_khach = ?";
        $stmt_check = mysqli_prepare($conn, $sql_check_pass);
        
        if ($stmt_check) {
            mysqli_stmt_bind_param($stmt_check, "i", $id_khach);
            mysqli_stmt_execute($stmt_check);
            $result_check = mysqli_stmt_get_result($stmt_check);
            $user_data = mysqli_fetch_assoc($result_check);
            mysqli_stmt_close($stmt_check);

            if ($user_data) {
                $current_hashed_password = $user_data['matkhau'];

                if (md5($old_pass_input) === $current_hashed_password) {
                    if ($new_pass_input === $new_pass2_input) {
                        // ***** BỎ KIỂM TRA ĐỘ DÀI MẬT KHẨU Ở ĐÂY *****
                        // Không còn kiểm tra: if (strlen($new_pass_input) >= 6)
                        
                        // Bước 5: Mã hóa mật khẩu mới
                        $new_hashed_password = md5($new_pass_input);

                        // Bước 6: Cập nhật mật khẩu mới vào cơ sở dữ liệu
                        $sql_update_pass = "UPDATE khach_hang SET matkhau = ? WHERE id_khach = ?";
                        $stmt_update = mysqli_prepare($conn, $sql_update_pass);
                        if ($stmt_update) {
                            mysqli_stmt_bind_param($stmt_update, "si", $new_hashed_password, $id_khach);
                            if (mysqli_stmt_execute($stmt_update)) {
                                $message = '<div class="alert alert-success">Đổi mật khẩu thành công!</div>';
                            } else {
                                $message = '<div class="alert alert-danger">Lỗi: Không thể cập nhật mật khẩu. Vui lòng thử lại.</div>';
                            }
                            mysqli_stmt_close($stmt_update);
                        } else {
                            $message = '<div class="alert alert-danger">Lỗi chuẩn bị câu lệnh cập nhật mật khẩu.</div>';
                        }
                        // ***** KẾT THÚC PHẦN BỎ KIỂM TRA *****
                    } else {
                        $message = '<div class="alert alert-danger">Mật khẩu mới nhập lại không khớp.</div>';
                    }
                } else {
                    $message = '<div class="alert alert-danger">Mật khẩu cũ không đúng.</div>';
                }
            } else {
                $message = '<div class="alert alert-danger">Không tìm thấy thông tin người dùng.</div>';
            }
        } else {
             $message = '<div class="alert alert-danger">Lỗi chuẩn bị câu lệnh kiểm tra mật khẩu.</div>';
        }
    }
}
?>

<div class="container auth-form-container" style="margin-top: 20px; margin-bottom: 20px;">
    <h2>Cập nhật mật khẩu</h2>

    <?php
    if (!empty($message)) {
        echo $message;
        // Xóa thông báo khỏi giao diện người dùng nếu muốn
        if (strpos($message, 'alert-success') !== false) {
             echo '<p class="mt-2">Mật khẩu của bạn đã được cập nhật.</p>';
        }
    } else {
        // Bạn có thể thêm một hướng dẫn nhỏ ở đây nếu chưa có thông báo nào
        echo '<p class="text-muted">Nhập mật khẩu cũ và mật khẩu mới của bạn.</p>';
    }
    ?>

    <form action="change-password.php" method="POST" class="form">
        <div class="input-box">
            <label>Mật khẩu cũ <span class="required">*</span></label>
            <input type="password" name="old-pass" required>
        </div>
        <br>
        <div class="input-box">
            <label>Mật khẩu mới <span class="required">*</span></label>
            <input type="password" name="new-pass" required>
        </div>
        <br>
        <div class="input-box">
            <label>Nhập lại mật khẩu mới <span class="required">*</span></label>
            <input type="password" name="new-pass2" required>
        </div>
        <br>
        <button type="submit" name="update" class="btn btn-primary">Cập nhật</button>
    </form>
</div>

<?php
include_once "includes/footer.php";
?>