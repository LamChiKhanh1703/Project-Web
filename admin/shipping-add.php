<?php
// admin/shipping-add.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once "sidebar.php"; 
require_once "connect.php"; 

$ten_vanchuyen = '';
$mo_ta = '';
$phi_ship_from_form = 0; // Đổi tên biến để rõ ràng hơn so với tên cột DB
$thoi_gian_from_form = ''; // Đổi tên biến
$trang_thai = 0; 

$errorMessage = '';
$successMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_add_shipping'])) {
    $ten_vanchuyen = trim($_POST['ten_vanchuyen']);
    $mo_ta = trim($_POST['mo_ta']);
    // Lấy giá trị từ form, tên input vẫn là phi_ship_co_ban và thoi_gian_uoc_tinh như trong HTML
    $phi_ship_from_form = trim($_POST['phi_ship_co_ban']); 
    $thoi_gian_from_form = trim($_POST['thoi_gian_uoc_tinh']);
    $trang_thai = (int)$_POST['trang_thai'];

    if (empty($ten_vanchuyen)) {
        $errorMessage = "Tên phương thức vận chuyển không được để trống.";
    }
    if (!is_numeric($phi_ship_from_form) || (float)$phi_ship_from_form < 0) {
        $errorMessage .= "<br>Phí ship cơ bản phải là một số không âm.";
        $phi_ship_from_form = 0; 
    }

    if (empty($errorMessage)) {
        if (!$conn || $conn->connect_error) {
            $errorMessage = "Lỗi kết nối CSDL: " . ($conn ? $conn->connect_error : 'Không có đối tượng kết nối');
        } else {
            $sql_check = "SELECT `id_vanchuyen` FROM `van_chuyen` WHERE `ten_vanchuyen` = ?";
            $stmt_check = mysqli_prepare($conn, $sql_check);
            if ($stmt_check) {
                mysqli_stmt_bind_param($stmt_check, "s", $ten_vanchuyen);
                mysqli_stmt_execute($stmt_check);
                mysqli_stmt_store_result($stmt_check);

                if (mysqli_stmt_num_rows($stmt_check) > 0) {
                    $errorMessage = "Tên phương thức vận chuyển này đã tồn tại.";
                } else {
                    // === DÒNG SQL ĐÃ SỬA ĐỂ KHỚP VỚI CSDL CỦA BẠN ===
                    $sql_insert = "INSERT INTO `van_chuyen` (`ten_vanchuyen`, `mo_ta`, `phi_ship`, `thoi_gian`, `trang_thai`) 
                                   VALUES (?, ?, ?, ?, ?)";
                    
                    $stmt_insert = mysqli_prepare($conn, $sql_insert);

                    if ($stmt_insert === false) {
                        $mysql_error_details = mysqli_error($conn);
                        $error_to_display = "LỖI CHI TIẾT TỪ MYSQLI_PREPARE: <strong style='color:red;'>" . htmlspecialchars($mysql_error_details) . "</strong>" .
                                            "<br><br>SQL ĐÃ THỬ: <pre>" . htmlspecialchars($sql_insert) . "</pre>";
                        die("<div style='padding: 20px; border: 2px solid red; background-color: #ffe0e0; font-family: Arial, sans-serif;'>" . $error_to_display . "</div>");
                    } else {
                        // Các biến PHP $phi_ship_from_form và $thoi_gian_from_form sẽ được bind vào các cột tương ứng
                        mysqli_stmt_bind_param($stmt_insert, "ssdsi", 
                                               $ten_vanchuyen, 
                                               $mo_ta, 
                                               $phi_ship_from_form, // Biến này sẽ được chèn vào cột `phi_ship`
                                               $thoi_gian_from_form, // Biến này sẽ được chèn vào cột `thoi_gian`
                                               $trang_thai);

                        if (mysqli_stmt_execute($stmt_insert)) {
                            $successMessage = "Thêm mới phương thức vận chuyển thành công!";
                            $ten_vanchuyen = '';
                            $mo_ta = '';
                            $phi_ship_from_form = 0;
                            $thoi_gian_from_form = '';
                            $trang_thai = 0;
                        } else {
                            $errorMessage = "Lỗi khi thêm phương thức vận chuyển (execute failed): " . mysqli_stmt_error($stmt_insert);
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

// Phần HTML của form không cần thay đổi tên input (vẫn là phi_ship_co_ban và thoi_gian_uoc_tinh)
// vì chúng ta đã xử lý việc gán giá trị từ $_POST vào các biến PHP $phi_ship_from_form và $thoi_gian_from_form.
?>

<div class="main-page-content">
    <div class="page-title" style="padding: 20px; background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
        <div class="title" style="font-size: 1.5rem; font-weight: 500;">Thêm Mới Phương Thức Vận Chuyển</div>
    </div>

    <div class="form-container" style="padding: 20px;">
        <?php if (!empty($successMessage)): ?>
            <div style="color: green; padding: 10px; border: 1px solid green; margin-bottom: 15px; background-color: #e6ffed;">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <div style="color: red; padding: 10px; border: 1px solid red; margin-bottom: 15px; background-color: #f8d7da;">
                <strong>Lỗi:</strong><br><?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <form action="shipping-add.php" method="POST">
            <div class="form-group">
                <label for="ten_vanchuyen">Tên phương thức vận chuyển <span style="color: red;">*</span></label>
                <input type="text" id="ten_vanchuyen" name="ten_vanchuyen" class="form-control" value="<?php echo htmlspecialchars($ten_vanchuyen); ?>" required>
            </div>

            <div class="form-group">
                <label for="mo_ta">Mô tả</label>
                <textarea id="mo_ta" name="mo_ta" class="form-control" rows="3"><?php echo htmlspecialchars($mo_ta); ?></textarea>
            </div>

            <div class="form-group">
                <label for="phi_ship_co_ban">Phí ship cơ bản (VNĐ)</label>
                <input type="number" id="phi_ship_co_ban" name="phi_ship_co_ban" class="form-control" value="<?php echo htmlspecialchars($phi_ship_from_form); ?>" step="1000" min="0">
            </div>

            <div class="form-group">
                <label for="thoi_gian_uoc_tinh">Thời gian ước tính</label>
                <input type="text" id="thoi_gian_uoc_tinh" name="thoi_gian_uoc_tinh" class="form-control" value="<?php echo htmlspecialchars($thoi_gian_from_form); ?>" placeholder="Ví dụ: 2-3 ngày làm việc">
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

            <button type="submit" name="submit_add_shipping" class="btn btn-primary" style="margin-top: 20px; padding: 10px 20px;">Thêm mới</button>
            <a href="shipping.php" class="btn btn-secondary" style="margin-top: 20px; padding: 10px 20px; text-decoration: none; margin-left:10px;">Quay lại danh sách</a>
        </form>
    </div>
</div>

<?php
    include_once "admin_footer.php"; 
?>