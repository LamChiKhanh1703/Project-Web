<?php
    include_once "sidebar.php";
    require_once "connect.php"; // Đảm bảo connect.php đã được include

    $message = ''; // Biến để lưu thông báo
    $customer_data = null; // Biến để lưu dữ liệu khách hàng

    // Lấy id_khach từ URL
    if(isset($_GET["id_khach"])) {
        $id_khach = (int)$_GET["id_khach"];

        // Lấy thông tin khách hàng hiện tại
        $sql_select_customer = "SELECT hoten, sodienthoai, email, diachi, trangthai FROM khach_hang WHERE id_khach = ?";
        $stmt_select_customer = mysqli_prepare($conn, $sql_select_customer);

        if ($stmt_select_customer) {
            mysqli_stmt_bind_param($stmt_select_customer, 'i', $id_khach);
            mysqli_stmt_execute($stmt_select_customer);
            $result_select_customer = mysqli_stmt_get_result($stmt_select_customer);
            $customer_data = mysqli_fetch_assoc($result_select_customer);
            mysqli_stmt_close($stmt_select_customer);

            if (!$customer_data) {
                $message = '<div class="alert-error">Không tìm thấy khách hàng với ID này.</div>';
            }
        } else {
            $message = '<div class="alert-error">Lỗi chuẩn bị truy vấn khách hàng: ' . mysqli_error($conn) . '</div>';
        }
    } else {
        $message = '<div class="alert-error">Không có ID khách hàng được cung cấp.</div>';
    }

    // Xử lý khi form được submit
    if(isset($_POST["submit"]) && $customer_data) { // Chỉ xử lý nếu có dữ liệu khách hàng ban đầu
        $hoten = $_POST["hoten"];
        $sodienthoai = $_POST["sodienthoai"];
        $email = $_POST["email"];
        $diachi = $_POST["diachi"];
        $trangthai = $_POST["trangthai"];

        // Cập nhật thông tin khách hàng
        $sql_update_customer = "UPDATE khach_hang SET hoten = ?, sodienthoai = ?, email = ?, diachi = ?, trangthai = ? WHERE id_khach = ?";
        $stmt_update_customer = mysqli_prepare($conn, $sql_update_customer);

        if ($stmt_update_customer) {
            mysqli_stmt_bind_param($stmt_update_customer, 'ssssii', $hoten, $sodienthoai, $email, $diachi, $trangthai, $id_khach);
            $update_success = mysqli_stmt_execute($stmt_update_customer);

            if ($update_success) {
                $message = '<div class="alert-success">Cập nhật thông tin khách hàng thành công!</div>';
                // Cập nhật lại customer_data để hiển thị thông tin mới nhất trên form
                $customer_data['hoten'] = $hoten;
                $customer_data['sodienthoai'] = $sodienthoai;
                $customer_data['email'] = $email;
                $customer_data['diachi'] = $diachi;
                $customer_data['trangthai'] = $trangthai;
            } else {
                $message = '<div class="alert-error">Có lỗi xảy ra khi cập nhật khách hàng: ' . mysqli_error($conn) . '</div>';
            }
            mysqli_stmt_close($stmt_update_customer);
        } else {
            $message = '<div class="alert-error">Lỗi chuẩn bị truy vấn cập nhật: ' . mysqli_error($conn) . '</div>';
        }
    }
?>
<div class="main-content">
    <?php echo $message; ?>
    <div class="page-title">
        <div class="title">Sửa thông tin khách hàng</div>
    </div>

    <?php if ($customer_data): // Chỉ hiển thị form nếu có dữ liệu khách hàng ?>
        <form action="" class="form" method="POST">
            <div class="input-box">
                <label>ID Khách hàng</label>
                <input type="text" value="<?php echo htmlspecialchars($id_khach); ?>" readonly/>
            </div>
            <div class="input-box">
                <label>Họ tên</label>
                <input type="text" name="hoten" value="<?php echo htmlspecialchars($customer_data['hoten']); ?>" required/>
            </div>
            <div class="input-box">
                <label>Số điện thoại</label>
                <input type="text" name="sodienthoai" value="<?php echo htmlspecialchars($customer_data['sodienthoai']); ?>" required/>
            </div>
            <div class="input-box">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($customer_data['email']); ?>" required/>
            </div>
            <div class="input-box">
                <label>Địa chỉ</label>
                <textarea name="diachi" rows="3" required><?php echo htmlspecialchars($customer_data['diachi']); ?></textarea>
            </div>
            <div class="gender-box">
                <h3>Trạng thái</h3>
                <div class="gender-option">
                    <div class="gender">
                        <input style="width: 30px" type="radio" value="0" name="trangthai" <?php echo ($customer_data['trangthai'] == 0) ? 'checked' : ''; ?>/>Hoạt động
                    </div>
                    <div class="gender">
                        <input style="width: 30px" type="radio" value="1" name="trangthai" <?php echo ($customer_data['trangthai'] == 1) ? 'checked' : ''; ?>/>Ngưng hoạt động
                    </div>
                </div>
            </div>
            <button name="submit" type="submit">Cập nhật</button>
        </form>
    <?php endif; ?>
</div>

<?php include_once "footer.php"; ?>
