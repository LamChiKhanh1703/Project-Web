<?php
// admin/payment-add.php

// Bật hiển thị lỗi để debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include các file cần thiết
include_once "sidebar.php"; // Sidebar của bạn (đã bao gồm session_start() và kiểm tra login admin)
require_once "connect.php"; // File kết nối CSDL, khởi tạo biến $conn

// Khởi tạo các biến cho form
$ten_hinh_thuc = '';
$mo_ta = '';
$thong_tin_them = '';
$trang_thai = 0; // Mặc định là "Hoạt động"

$errorMessage = '';
$successMessage = '';

// Xử lý khi form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_add_payment'])) {
    // Lấy dữ liệu từ form và làm sạch cơ bản
    $ten_hinh_thuc = trim($_POST['ten_hinh_thuc']);
    $mo_ta = trim($_POST['mo_ta']);
    $thong_tin_them = trim($_POST['thong_tin_them']);
    $trang_thai = (int)$_POST['trang_thai'];

    // Validate dữ liệu
    if (empty($ten_hinh_thuc)) {
        $errorMessage = "Tên hình thức thanh toán không được để trống.";
    }

    if (empty($errorMessage)) {
        // Kiểm tra kết nối CSDL
        if (!$conn || $conn->connect_error) {
            $errorMessage = "Lỗi kết nối CSDL: " . ($conn ? $conn->connect_error : 'Không có đối tượng kết nối');
        } else {
            // Kiểm tra xem tên hình thức thanh toán đã tồn tại chưa
            $sql_check = "SELECT `id_thanhtoan` FROM `thanh_toan` WHERE `ten_hinh_thuc` = ?";
            $stmt_check = mysqli_prepare($conn, $sql_check);
            
            if ($stmt_check) {
                mysqli_stmt_bind_param($stmt_check, "s", $ten_hinh_thuc);
                mysqli_stmt_execute($stmt_check);
                mysqli_stmt_store_result($stmt_check);

                if (mysqli_stmt_num_rows($stmt_check) > 0) {
                    $errorMessage = "Tên hình thức thanh toán này đã tồn tại trong hệ thống.";
                } else {
                    // Nếu chưa tồn tại, tiến hành thêm mới
                    // Câu lệnh SQL sẽ bao gồm cột `thong_tin_them`
                    $sql_insert = "INSERT INTO `thanh_toan` (`ten_hinh_thuc`, `mo_ta`, `thong_tin_them`, `trang_thai`) 
                                   VALUES (?, ?, ?, ?)";
                    
                    $stmt_insert = mysqli_prepare($conn, $sql_insert);

                    if ($stmt_insert === false) {
                        // Lỗi khi chuẩn bị câu lệnh
                        $mysql_error_details = mysqli_error($conn);
                        $error_to_display = "LỖI CHI TIẾT TỪ MYSQLI_PREPARE: <strong style='color:red;'>" . htmlspecialchars($mysql_error_details) . "</strong>" .
                                            "<br><br>SQL ĐÃ THỬ: <pre>" . htmlspecialchars($sql_insert) . "</pre>";
                        // Dừng kịch bản và chỉ hiển thị lỗi này để debug
                        die("<div style='padding: 20px; border: 2px solid red; background-color: #ffe0e0; font-family: Arial, sans-serif;'>" . $error_to_display . "</div>");
                    } else {
                        // Bind parameters (s: string, i: integer)
                        // ten_hinh_thuc (s), mo_ta (s), thong_tin_them (s), trang_thai (i)
                        mysqli_stmt_bind_param($stmt_insert, "sssi", 
                                               $ten_hinh_thuc, 
                                               $mo_ta, 
                                               $thong_tin_them, 
                                               $trang_thai);

                        if (mysqli_stmt_execute($stmt_insert)) {
                            $successMessage = "Thêm mới hình thức thanh toán thành công!";
                            // Reset giá trị form sau khi thêm thành công
                            $ten_hinh_thuc = '';
                            $mo_ta = '';
                            $thong_tin_them = '';
                            $trang_thai = 0;
                            // Tùy chọn: Chuyển hướng về trang danh sách (ví dụ: payment.php)
                            // header("Location: payment.php?message=add_success");
                            // exit();
                        } else {
                            // Lỗi khi thực thi câu lệnh
                            $errorMessage = "Lỗi khi thêm hình thức thanh toán (execute failed): " . mysqli_stmt_error($stmt_insert);
                        }
                        mysqli_stmt_close($stmt_insert);
                    }
                }
                mysqli_stmt_close($stmt_check);
            } else {
                 $errorMessage = "Lỗi chuẩn bị câu lệnh kiểm tra trùng lặp: " . mysqli_error($conn);
            }
        }
    }
}
?>

<div class="main-page-content">
    <div class="page-title" style="padding: 20px; background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
        <div class="title" style="font-size: 1.5rem; font-weight: 500;">Thêm Mới Hình Thức Thanh Toán</div>
    </div>

    <div class="form-container" style="padding: 20px;">
        <?php if (!empty($successMessage)): ?>
            <div style="color: green; padding: 10px; border: 1px solid green; margin-bottom: 15px; background-color: #e6ffed;">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <div style="color: red; padding: 10px; border: 1px solid red; margin-bottom: 15px; background-color: #f8d7da;">
                <strong>Lỗi:</strong><br><?php echo nl2br(htmlspecialchars($errorMessage)); // nl2br để xuống dòng nếu có <br> trong errorMessage ?>
            </div>
        <?php endif; ?>

        <form action="payment-add.php" method="POST">
            <div class="form-group">
                <label for="ten_hinh_thuc">Tên hình thức thanh toán <span style="color: red;">*</span></label>
                <input type="text" id="ten_hinh_thuc" name="ten_hinh_thuc" class="form-control" value="<?php echo htmlspecialchars($ten_hinh_thuc); ?>" required>
            </div>

            <div class="form-group">
                <label for="mo_ta">Mô tả</label>
                <textarea id="mo_ta" name="mo_ta" class="form-control" rows="3"><?php echo htmlspecialchars($mo_ta); ?></textarea>
            </div>

            <div class="form-group">
                <label for="thong_tin_them">Thông tin thêm (Ví dụ: Số tài khoản, Hướng dẫn)</label>
                <textarea id="thong_tin_them" name="thong_tin_them" class="form-control" rows="3"><?php echo htmlspecialchars($thong_tin_them); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Trạng thái</label>
                <div>
                    <input type="radio" id="trang_thai_active" name="trang_thai" value="0" <?php echo ($trang_thai == 0) ? 'checked' : ''; ?>>
                    <label for="trang_thai_active" style="margin-right: 15px;">Hoạt động</label>
                    
                    <input type="radio" id="trang_thai_inactive" name="trang_thai" value="1" <?php echo ($trang_thai == 1) ? 'checked' : ''; ?>>
                    <label for="trang_thai_inactive">Không hoạt động</label>
                </div>
            </div>

            <button type="submit" name="submit_add_payment" class="btn btn-primary" style="margin-top: 20px; padding: 10px 20px;">Thêm mới</button>
            <a href="payment.php" class="btn btn-secondary" style="margin-top: 20px; padding: 10px 20px; text-decoration: none; margin-left:10px;">Quay lại danh sách</a>
        </form>
    </div>
</div>

<?php
    // Include file footer để đóng các thẻ HTML
    include_once "admin_footer.php"; 
?>