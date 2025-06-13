<?php
    include_once "sidebar.php";
    require_once "connect.php";

    $message = ''; // Biến để lưu thông báo

    // Viết truy vấn để lấy tất cả nhãn hiệu
    $sql1 = "SELECT * FROM nhan_hieu WHERE trangthai = 0";
    $query1 = mysqli_query($conn, $sql1);

    // Viết truy vấn để lấy tất cả dòng sản phẩm
    $sql2 = "SELECT * FROM dong_sanpham WHERE trangthai = 0";
    $query2 = mysqli_query($conn, $sql2);

    // Viết truy vấn để lấy tất cả danh mục
    $sql3 = "SELECT * FROM danh_muc WHERE trangthai = 0";
    $query3 = mysqli_query($conn, $sql3);

    // Bắt sự kiện khi user nhấn vào nút submit
    if(isset($_POST["submit"])) {
        // Lấy dữ liệu từ form thông qua name của các thẻ
        $tensp = $_POST["tensp"];
        $mo_ta_ngan = $_POST["mo_ta_ngan"];
        $mo_ta_chi_tiet = $_POST["mo_ta_chi_tiet"];
        $nhanhieu = $_POST["nhanhieu"];
        $dong = $_POST["dongsp"];
        $danhmuc = $_POST["danhmuc"];
        $trangthai = $_POST["trangthai"];
        $noi_bat = isset($_POST["noi_bat"]) ? 1 : 0;

        // Dữ liệu cho sanpham_chitiet (Biến thể đầu tiên)
        $sku = $_POST['sku'];
        $so_luong = $_POST['so_luong'];
        $gia_nhap = $_POST['gia_nhap'];
        $gia_goc = $_POST['gia_goc'];
        $gia_sale = $_POST['gia_sale'];
        $trang_thai_spct = $_POST['trang_thai_spct']; // Trạng thái riêng cho SPCT

        // Xử lý upload ảnh đại diện
        $tenfile_main_image = null;
        $tmp_name_main_image = null;
        if (isset($_FILES["anhdaidien"]) && $_FILES["anhdaidien"]["error"] == UPLOAD_ERR_OK) {
            $tenfile_main_image = basename($_FILES["anhdaidien"]["name"]);
            $tmp_name_main_image = $_FILES['anhdaidien']['tmp_name'];
        }

        $folder = "../uploads/"; // Thư mục lưu ảnh

        // Bắt đầu transaction
        mysqli_begin_transaction($conn);
        $transaction_success = true;

        try {
            // Insert dữ liệu vào bảng san_pham
            $sql_insert_sp = "INSERT INTO san_pham(ten_sp, mo_ta_ngan, mo_ta_chi_tiet, id_nhanhieu, id_dong, id_danhmuc, trangthai, noi_bat, ngay_tao, ngaycapnhat)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $stmt_sp = mysqli_prepare($conn, $sql_insert_sp);
            if (!$stmt_sp) {
                throw new Exception("Lỗi chuẩn bị truy vấn SQL SP: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt_sp, 'sssiiiii',
                $tensp, $mo_ta_ngan, $mo_ta_chi_tiet, $nhanhieu, $dong, $danhmuc, $trangthai, $noi_bat);
            if (!mysqli_stmt_execute($stmt_sp)) {
                throw new Exception("Lỗi khi thêm sản phẩm vào CSDL: " . mysqli_error($conn));
            }
            $idsp = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt_sp);

            // Insert ảnh đại diện vào bảng hinh_anh
            if ($tenfile_main_image) {
                $sql_insert_main_image = "INSERT INTO hinh_anh(id_sp, ten_file, trang_thai) VALUES (?, ?, 0)";
                $stmt_main_image = mysqli_prepare($conn, $sql_insert_main_image);
                if (!$stmt_main_image) {
                    throw new Exception("Lỗi chuẩn bị truy vấn SQL ảnh đại diện: " . mysqli_error($conn));
                }
                mysqli_stmt_bind_param($stmt_main_image, 'is', $idsp, $tenfile_main_image);
                if (!mysqli_stmt_execute($stmt_main_image)) {
                    throw new Exception("Lỗi khi lưu ảnh đại diện vào CSDL: " . mysqli_error($conn));
                }
                if (!move_uploaded_file($tmp_name_main_image, $folder . $tenfile_main_image)) {
                    throw new Exception("Có lỗi khi di chuyển file ảnh đại diện.");
                }
                mysqli_stmt_close($stmt_main_image);
            }

            // Xử lý ảnh con
            if(isset($_FILES['anhcon']) && !empty($_FILES['anhcon']['name'][0]) && $_FILES['anhcon']['error'][0] == UPLOAD_ERR_OK){
                $names = $_FILES['anhcon']['name'];
                $tmp_names = $_FILES['anhcon']['tmp_name'];
                
                foreach (array_combine($tmp_names, $names) as $temp_path => $file_name) {
                    $sql_insert_sub_image = "INSERT INTO hinh_anh(id_sp, ten_file, trang_thai) VALUES (?, ?, 1)";
                    $stmt_sub_image = mysqli_prepare($conn, $sql_insert_sub_image);
                    if (!$stmt_sub_image) {
                        throw new Exception("Lỗi chuẩn bị truy vấn SQL ảnh con: " . mysqli_error($conn));
                    }
                    mysqli_stmt_bind_param($stmt_sub_image, 'is', $idsp, $file_name);
                    if (!mysqli_stmt_execute($stmt_sub_image)) {
                        throw new Exception("Lỗi khi lưu ảnh con " . htmlspecialchars($file_name) . " vào CSDL: " . mysqli_error($conn));
                    }
                    if (!move_uploaded_file($temp_path, $folder . $file_name)) {
                        throw new Exception("Có lỗi khi di chuyển file ảnh con: " . htmlspecialchars($file_name) . ".");
                    }
                    mysqli_stmt_close($stmt_sub_image);
                }
            }

            // Insert dữ liệu vào bảng sanpham_chitiet (biến thể đầu tiên)
            $sql_insert_spct = "INSERT INTO sanpham_chitiet(id_sp, sku, so_luong, gia_nhap, gia_goc, gia_sale, trang_thai)
                                VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_spct = mysqli_prepare($conn, $sql_insert_spct);
            if (!$stmt_spct) {
                throw new Exception("Lỗi chuẩn bị truy vấn SQL SPCT: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt_spct, 'isiiiii',
                $idsp, $sku, $so_luong, $gia_nhap, $gia_goc, $gia_sale, $trang_thai_spct);
            if (!mysqli_stmt_execute($stmt_spct)) {
                throw new Exception("Lỗi khi thêm chi tiết sản phẩm vào CSDL: " . mysqli_error($conn));
            }
            mysqli_stmt_close($stmt_spct);

            mysqli_commit($conn);
            $message = '<div class="alert-success">Chúc mừng bạn đã thêm sản phẩm thành công!</div>';
            // Để reset form sau khi thành công, có thể redirect
            // header("Location: product-add.php?success=1");
            // exit();

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $message = '<div class="alert-error">Đặt hàng thất bại: ' . $e->getMessage() . '</div>';
        }
    }
?>
<div class="main-content">
    <?php echo $message; ?>
    <div class="page-title">
        <div class="title">Thêm mới sản phẩm</div>
    </div>

    <form action="" class="form" method="POST" enctype="multipart/form-data">
        <h3>Thông tin sản phẩm chính</h3>
        <div class="input-box">
            <label>Tên sản phẩm</label>
            <input type="text" name="tensp" required value="<?php echo isset($_POST['tensp']) ? htmlspecialchars($_POST['tensp']) : ''; ?>"/>
        </div>
        <div class="input-box">
            <label>Mô tả ngắn</label>
            <br>
            <textarea name="mo_ta_ngan" rows="3" cols="100"><?php echo isset($_POST['mo_ta_ngan']) ? htmlspecialchars($_POST['mo_ta_ngan']) : ''; ?></textarea>
        </div>
        <div class="input-box">
            <label>Mô tả chi tiết</label>
            <br>
            <textarea name="mo_ta_chi_tiet" rows="5" cols="100"><?php echo isset($_POST['mo_ta_chi_tiet']) ? htmlspecialchars($_POST['mo_ta_chi_tiet']) : ''; ?></textarea>
        </div>
        <div class="input-box">
            <label>Nhãn hiệu</label>
            <div class="select-box">
                <select name="nhanhieu" required>
                    <option hidden>-- Chọn nhãn hiệu --</option>
                    <?php
                    $selected_nhanhieu = isset($_POST['nhanhieu']) ? $_POST['nhanhieu'] : '';
                    mysqli_data_seek($query1, 0);
                    while ($nhanhieu = mysqli_fetch_assoc($query1)) :
                    ?>
                        <option value="<?php echo $nhanhieu["id_nhan"] ?>" <?php echo ($selected_nhanhieu == $nhanhieu["id_nhan"]) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($nhanhieu["ten_nhanhieu"]) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <div class="input-box">
            <label>Dòng sản phẩm</label>
            <div class="select-box">
                <select name="dongsp" required>
                    <option hidden>-- Chọn dòng sản phẩm --</option>
                    <?php
                    $selected_dong = isset($_POST['dongsp']) ? $_POST['dongsp'] : '';
                    mysqli_data_seek($query2, 0);
                    while ($dong = mysqli_fetch_assoc($query2)) :
                    ?>
                        <option value="<?php echo $dong["id_dong"] ?>" <?php echo ($selected_dong == $dong["id_dong"]) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dong["ten_dong"]) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <div class="input-box">
            <label>Danh mục</label>
            <div class="select-box">
                <select name="danhmuc" required>
                    <option hidden>-- Chọn danh mục --</option>
                    <?php
                    $selected_danhmuc = isset($_POST['danhmuc']) ? $_POST['danhmuc'] : '';
                    mysqli_data_seek($query3, 0);
                    while ($danhmuc = mysqli_fetch_assoc($query3)) :
                    ?>
                        <option value="<?php echo $danhmuc["id_danhmuc"] ?>" <?php echo ($selected_danhmuc == $danhmuc["id_danhmuc"]) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($danhmuc["ten_danhmuc"]) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <div class="gender-box">
            <h3>Trạng thái sản phẩm chính</h3>
            <div class="gender-option">
                <div class="gender">
                    <input style="width: 30px" type="radio" value="0" name="trangthai" <?php echo (!isset($_POST['trangthai']) || $_POST['trangthai'] == '0') ? 'checked' : ''; ?>/>Hiện
                </div>
                <div class="gender">
                    <input style="width: 30px" type="radio" value="1" name="trangthai" <?php echo (isset($_POST['trangthai']) && $_POST['trangthai'] == '1') ? 'checked' : ''; ?>/>Ẩn
                </div>
            </div>
        </div>
        <div class="gender-box">
            <h3>Sản phẩm nổi bật</h3>
            <div class="gender-option">
                <div class="gender">
                    <input style="width: 30px" type="radio" value="1" name="noi_bat" <?php echo (isset($_POST['noi_bat']) && $_POST['noi_bat'] == '1') ? 'checked' : ''; ?>/>Có
                </div>
                <div class="gender">
                    <input style="width: 30px" type="radio" value="0" name="noi_bat" <?php echo (!isset($_POST['noi_bat']) || $_POST['noi_bat'] == '0') ? 'checked' : ''; ?>/>Không
                </div>
            </div>
        </div>
        <div class="input-box">
            <label>Ảnh đại diện</label>
            <input type="file" name="anhdaidien" accept="image/*">
        </div>
        <div class="input-box">
            <label>Các ảnh con</label>
            <input type="file" name="anhcon[]" multiple accept="image/*">
        </div>
       
        <hr class="my-3"> <h3>Thông tin biến thể sản phẩm (mã mới ko trùng)</h3>
        <p><small>Bạn có thể thêm nhiều biến thể hơn ở trang chỉnh sửa sản phẩm sau.</small></p>
        <div class="input-box">
            <label>SKU (Mã sản phẩm chi tiết)</label>
            <input type="text" name="sku" required value="<?php echo isset($_POST['sku']) ? htmlspecialchars($_POST['sku']) : ''; ?>"/>
        </div>
        <div class="input-box">
            <label>Số lượng tồn kho</label>
            <input type="number" name="so_luong" min="0" required value="<?php echo isset($_POST['so_luong']) ? htmlspecialchars($_POST['so_luong']) : ''; ?>"/>
        </div>
        <div class="input-box">
            <label>Giá nhập</label>
            <input type="number" name="gia_nhap" min="0" required value="<?php echo isset($_POST['gia_nhap']) ? htmlspecialchars($_POST['gia_nhap']) : ''; ?>"/>
        </div>
        <div class="input-box">
            <label>Giá gốc (Giá bán lẻ đề xuất)</label>
            <input type="number" name="gia_goc" min="0" required value="<?php echo isset($_POST['gia_goc']) ? htmlspecialchars($_POST['gia_goc']) : ''; ?>"/>
        </div>
        <div class="input-box">
            <label>Giá sale (Giá bán thực tế, để trống nếu không có)</label>
            <input type="number" name="gia_sale" min="0" value="<?php echo isset($_POST['gia_sale']) ? htmlspecialchars($_POST['gia_sale']) : ''; ?>"/>
        </div>
        <div class="gender-box">
            <h3>Trạng thái biến thể</h3>
            <div class="gender-option">
                <div class="gender">
                    <input style="width: 30px" type="radio" value="0" name="trang_thai_spct" <?php echo (!isset($_POST['trang_thai_spct']) || $_POST['trang_thai_spct'] == '0') ? 'checked' : ''; ?>/>Hiện
                </div>
                <div class="gender">
                    <input style="width: 30px" type="radio" value="1" name="trang_thai_spct" <?php echo (isset($_POST['trang_thai_spct']) && $_POST['trang_thai_spct'] == '1') ? 'checked' : ''; ?>/>Ẩn
                </div>
            </div>
        </div>

        <button name="submit" type="submit">Lưu sản phẩm</button>
    </form>
</div>

<?php include_once "footer.php"; ?>