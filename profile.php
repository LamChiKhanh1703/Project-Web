<?php
include_once "includes/header.php";

$message = '';
$user_data = null;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=profile.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin khách hàng từ CSDL
$sql_select_user = "SELECT hoten, sodienthoai, email, diachi, ngay_dangky FROM khach_hang WHERE id_khach = ?";
$stmt_select_user = mysqli_prepare($conn, $sql_select_user);
if ($stmt_select_user) {
    mysqli_stmt_bind_param($stmt_select_user, 'i', $user_id);
    mysqli_stmt_execute($stmt_select_user);
    $result_select_user = mysqli_stmt_get_result($stmt_select_user);
    $user_data = mysqli_fetch_assoc($result_select_user);
    mysqli_stmt_close($stmt_select_user);
} else {
    $message = '<div class="alert-error">Lỗi khi tải thông tin tài khoản: ' . mysqli_error($conn) . '</div>';
}

// Xử lý cập nhật thông tin
if (isset($_POST['submit_update']) && $user_data) {
    $hoten = trim($_POST['hoten']);
    $sodienthoai = trim($_POST['sodienthoai']);
    $diachi = trim($_POST['diachi']);

    if (empty($hoten) || empty($sodienthoai) || empty($diachi)) {
        $message = '<div class="alert-error">Vui lòng điền đầy đủ Họ tên, Số điện thoại và Địa chỉ.</div>';
    } else {
        $sql_update_user = "UPDATE khach_hang SET hoten = ?, sodienthoai = ?, diachi = ? WHERE id_khach = ?";
        $stmt_update_user = mysqli_prepare($conn, $sql_update_user);
        if ($stmt_update_user) {
            mysqli_stmt_bind_param($stmt_update_user, 'sssi', $hoten, $sodienthoai, $diachi, $user_id);
            if (mysqli_stmt_execute($stmt_update_user)) {
                $message = '<div class="alert-success">Cập nhật thông tin tài khoản thành công!</div>';
                // Cập nhật lại session user_name nếu tên thay đổi
                $_SESSION['user_name'] = $hoten;
                // Cập nhật lại user_data để hiển thị thông tin mới nhất
                $user_data['hoten'] = $hoten;
                $user_data['sodienthoai'] = $sodienthoai;
                $user_data['diachi'] = $diachi;
            } else {
                $message = '<div class="alert-error">Có lỗi xảy ra khi cập nhật thông tin: ' . mysqli_error($conn) . '</div>';
            }
            mysqli_stmt_close($stmt_update_user);
        } else {
            $message = '<div class="alert-error">Lỗi chuẩn bị truy vấn cập nhật: ' . mysqli_error($conn) . '</div>';
        }
    }
}
?>

<div class="container auth-form-container">
    <h2 class="text-center">Thông tin tài khoản</h2>
    <?php echo $message; ?>

    <?php if ($user_data): ?>
        <form action="" method="POST" class="form">
            <div class="input-box">
                <label>ID Khách hàng</label>
                <input type="text" value="<?php echo htmlspecialchars($user_id); ?>" readonly/>
            </div>
            <div class="input-box">
                <label>Họ tên</label>
                <input type="text" name="hoten" value="<?php echo htmlspecialchars($user_data['hoten']); ?>" required/>
            </div>
            <div class="input-box">
                <label>Số điện thoại</label>
                <input type="text" name="sodienthoai" value="<?php echo htmlspecialchars($user_data['sodienthoai']); ?>" required/>
            </div>
            <div class="input-box">
                <label>Email (Không thể thay đổi)</label>
                <input type="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" readonly/>
            </div>
            <div class="input-box">
                <label>Địa chỉ</label>
                <textarea name="diachi" rows="3" required><?php echo htmlspecialchars($user_data['diachi']); ?></textarea>
            </div>
            <div class="input-box">
                <label>Ngày đăng ký</label>
                <input type="text" value="<?php echo htmlspecialchars($user_data['ngay_dangky']); ?>" readonly/>
            </div>
            <button name="update">Cập nhật</button>
                <a href="change-password.php">Đổi mật khẩu</a>
            <button type="submit" name="submit_update">Cập nhật thông tin</button>
        </form>
    <?php else: ?>
        <p class="text-center">Thông tin tài khoản không khả dụng.</p>
    <?php endif; ?>
</div>

<?php include_once "includes/footer.php"; ?>