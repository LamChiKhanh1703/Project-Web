<?php
    include_once "sidebar.php";
    require_once "connect.php";

    $message = ''; // Biến để lưu thông báo

    // Kiểm tra user đã nhấn vào nút Lưu chưa
    if(isset($_POST["submit"])) {
        $name = $_POST["hoten"];
        $email = $_POST["email"];
        $matkhau = $_POST["matkhau"]; // Lấy mật khẩu từ form
        $matkhau_confirm = $_POST["matkhau_confirm"]; // Lấy mật khẩu xác nhận
        $role = $_POST["vaitro"];

        // Kiểm tra mật khẩu và xác nhận mật khẩu có khớp nhau không
        if ($matkhau !== $matkhau_confirm) {
            $message = '<div class="alert-error">Mật khẩu và xác nhận mật khẩu không khớp.</div>';
        } else {
            // Kiểm tra email đã tồn tại trong bảng quan_tri chưa
            $sql_check_email = "SELECT id_admin FROM quan_tri WHERE email = ?";
            $stmt_check_email = mysqli_prepare($conn, $sql_check_email);
            mysqli_stmt_bind_param($stmt_check_email, 's', $email);
            mysqli_stmt_execute($stmt_check_email);
            mysqli_stmt_store_result($stmt_check_email);

            if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
                $message = '<div class="alert-error">Email này đã tồn tại. Vui lòng sử dụng email khác.</div>';
            } else {
                $hashed_password = md5($matkhau); // Mã hóa mật khẩu MD5 trước khi lưu

                // Viết câu lệnh sql để insert dữ liệu vào bảng quản trị
                $sql = "INSERT INTO quan_tri(hoten, email, matkhau, vaitro, trangthai, ngay_tao) VALUES (?, ?, ?, ?, 0, NOW())"; // trạng thái 0 (active)
                $stmt = mysqli_prepare($conn, $sql);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'sssi', $name, $email, $hashed_password, $role);
                    $query_success = mysqli_stmt_execute($stmt);

                    // Kiểm tra xem đã insert thành công chưa
                    if($query_success) {
                        $message = '<div class="alert-success">Thêm mới tài khoản admin thành công!</div>';
                        // Có thể reset lại form sau khi thành công nếu muốn
                        // header("Location: admin-add.php?success=1"); exit();
                    } else {
                        $message = '<div class="alert-error">Có lỗi xảy ra khi thêm tài khoản: ' . mysqli_error($conn) . '</div>';
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $message = '<div class="alert-error">Lỗi chuẩn bị truy vấn SQL: ' . mysqli_error($conn) . '</div>';
                }
            }
            mysqli_stmt_close($stmt_check_email);
        }
    }
?>
<div class="main-content">
    <?php echo $message; ?>
    <div class="page-title">
        <div class="title">Thêm mới tài khoản Nhân viên</div>
    </div>

    <form action="" class="form" method="POST">
        <div class="input-box">
          <label>Họ tên</label>
          <input type="text" name="hoten" required value="<?php echo isset($_POST['hoten']) ? htmlspecialchars($_POST['hoten']) : ''; ?>"/>
        </div>
        <div class="input-box">
          <label>Email</label>
          <input type="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"/>
        </div>
        <div class="input-box">
          <label>Mật khẩu</label>
          <input type="password" name="matkhau" required/>
        </div>
        <div class="input-box">
          <label>Xác nhận mật khẩu</label>
          <input type="password" name="matkhau_confirm" required/>
        </div>
        <div class="input-box">
          <label>Vai trò</label>
          <div class="select-box">
            <select name="vaitro" required>
                <option hidden>Vai trò</option>
                <?php $selected_role = isset($_POST['vaitro']) ? $_POST['vaitro'] : ''; ?>
                <option value="0" <?php echo ($selected_role === '0') ? 'selected' : ''; ?>>Admin (Admin chính)</option>
                <option value="5" <?php echo ($selected_role === '5') ? 'selected' : ''; ?>>Quản lý</option>
                <option value="1" <?php echo ($selected_role === '1') ? 'selected' : ''; ?>>Nhân viên Sale</option>
                <option value="2" <?php echo ($selected_role === '2') ? 'selected' : ''; ?>>Chăm sóc khách hàng</option>
                <option value="3" <?php echo ($selected_role === '3') ? 'selected' : ''; ?>>Nhân viên kho</option>
                <option value="4" <?php echo ($selected_role === '4') ? 'selected' : ''; ?>>Kế toán</option>
            </select>
        </div>
        <button name="submit" type="submit">Lưu</button>
    </form>
</div>

<?php include_once "footer.php"; ?>