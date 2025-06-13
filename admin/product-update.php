<?php
    include_once "sidebar.php";
    require_once "connect.php";

    $message = '';
    $idsp = null; // Khởi tạo $idsp

    if(isset($_GET["idsp"])) {
        $idsp = $_GET["idsp"];

        // Lấy thông tin sản phẩm hiện tại
        $sql_product = "SELECT sp.*, nh.ten_nhanhieu, ds.ten_dong, dm.ten_danhmuc
                        FROM san_pham sp
                        INNER JOIN nhan_hieu nh ON nh.id_nhan = sp.id_nhanhieu
                        INNER JOIN dong_sanpham ds ON ds.id_dong = sp.id_dong
                        INNER JOIN danh_muc dm ON dm.id_danhmuc = sp.id_danhmuc
                        WHERE sp.id_sp = $idsp";
        $query_product = mysqli_query($conn, $sql_product);
        $row_product = mysqli_fetch_assoc($query_product);

        // Nếu không tìm thấy sản phẩm, chuyển hướng hoặc báo lỗi
        if (!$row_product) {
            $message = '<div class="alert-error">Sản phẩm không tồn tại hoặc đã bị xóa.</div>';
            $idsp_valid = false;
        } else {
            $idsp_valid = true;

            // Lấy tất cả nhãn hiệu cho dropdown
            $sql_brands = "SELECT * FROM nhan_hieu WHERE trangthai = 0";
            $query_brands = mysqli_query($conn, $sql_brands);

            // Lấy tất cả dòng sản phẩm cho dropdown
            $sql_series = "SELECT * FROM dong_sanpham WHERE trangthai = 0";
            $query_series = mysqli_query($conn, $sql_series);

            // Lấy tất cả danh mục cho dropdown
            $sql_categories = "SELECT * FROM danh_muc WHERE trangthai = 0";
            $query_categories = mysqli_query($conn, $sql_categories);

            // Lấy ảnh đại diện
            $sql_main_image = "SELECT * FROM hinh_anh WHERE id_sp = $idsp AND trang_thai = 0";
            $query_main_image = mysqli_query($conn, $sql_main_image);
            $main_image = mysqli_fetch_assoc($query_main_image);

            // Lấy các ảnh con
            $sql_sub_images = "SELECT * FROM hinh_anh WHERE id_sp = $idsp AND trang_thai = 1";
            $query_sub_images = mysqli_query($conn, $sql_sub_images);
        }
    } else {
        $message = '<div class="alert-error">Không có ID sản phẩm được cung cấp để cập nhật.</div>';
        $idsp_valid = false;
    }

    // Xử lý khi submit form
    if(isset($_POST["submit"]) && $idsp_valid) {
        $tensp = $_POST["tensp"];
        $mo_ta_ngan = $_POST["mo_ta_ngan"];
        $mo_ta_chi_tiet = $_POST["mo_ta_chi_tiet"];
        $nhanhieu = $_POST["nhanhieu"];
        $dong = $_POST["dongsp"];
        $danhmuc = $_POST["danhmuc"];
        $trangthai = $_POST["trangthai"];
        $noi_bat = isset($_POST["noi_bat"]) ? 1 : 0;

        // Cập nhật thông tin sản phẩm
        $sql_update_product = "UPDATE san_pham SET
                                ten_sp = ?,
                                mo_ta_ngan = ?,
                                mo_ta_chi_tiet = ?,
                                id_nhanhieu = ?,
                                id_dong = ?,
                                id_danhmuc = ?,
                                trangthai = ?,
                                noi_bat = ?,
                                ngaycapnhat = NOW()
                                WHERE id_sp = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update_product);
        if ($stmt_update) {
            mysqli_stmt_bind_param($stmt_update, 'sssiiiiii',
                $tensp, $mo_ta_ngan, $mo_ta_chi_tiet, $nhanhieu, $dong, $danhmuc, $trangthai, $noi_bat, $idsp);
            $update_success = mysqli_stmt_execute($stmt_update);

            if ($update_success) {
                // Xử lý cập nhật ảnh đại diện
                if(isset($_FILES["anhdaidien"]) && $_FILES["anhdaidien"]["name"] != "" && $_FILES["anhdaidien"]["error"] == UPLOAD_ERR_OK) {
                    $new_main_image_name = basename($_FILES["anhdaidien"]["name"]);
                    $new_main_image_tmp = $_FILES["anhdaidien"]["tmp_name"];
                    $folder = "../uploads/";

                    // Xóa ảnh đại diện cũ khỏi DB và thư mục (nếu có)
                    if ($main_image) {
                        unlink($folder . $main_image['ten_file']); // Xóa file vật lý
                        $sql_delete_old_main_image = "DELETE FROM hinh_anh WHERE id_hinh = ".$main_image['id_hinh'];
                        mysqli_query($conn, $sql_delete_old_main_image);
                    }

                    // Thêm ảnh đại diện mới vào DB
                    $sql_insert_new_main_image = "INSERT INTO hinh_anh(id_sp, ten_file, trang_thai) VALUES (?, ?, 0)";
                    $stmt_new_main_image = mysqli_prepare($conn, $sql_insert_new_main_image);
                    mysqli_stmt_bind_param($stmt_new_main_image, 'is', $idsp, $new_main_image_name);
                    if (mysqli_stmt_execute($stmt_new_main_image)) {
                        move_uploaded_file($new_main_image_tmp, $folder . $new_main_image_name);
                    } else {
                        $message .= '<div class="alert-error">Lỗi khi lưu ảnh đại diện mới vào CSDL.</div>';
                    }
                    mysqli_stmt_close($stmt_new_main_image);
                }

                // Xử lý cập nhật ảnh con (xóa hết ảnh cũ và thêm lại)
                if(isset($_FILES['anhcon']) && !empty($_FILES['anhcon']['name'][0]) && $_FILES['anhcon']['error'][0] == UPLOAD_ERR_OK){
                    $folder = "../uploads/";

                    // Lấy và xóa tất cả ảnh con cũ khỏi DB và thư mục
                    $sql_delete_old_sub_images_query = "SELECT ten_file FROM hinh_anh WHERE id_sp = $idsp AND trang_thai = 1";
                    $res_old_sub_images = mysqli_query($conn, $sql_delete_old_sub_images_query);
                    while ($sub_img_row = mysqli_fetch_assoc($res_old_sub_images)) {
                        if (file_exists($folder . $sub_img_row['ten_file'])) {
                            unlink($folder . $sub_img_row['ten_file']);
                        }
                    }
                    $sql_delete_old_sub_images = "DELETE FROM hinh_anh WHERE id_sp = $idsp AND trang_thai = 1";
                    mysqli_query($conn, $sql_delete_old_sub_images);

                    // Thêm các ảnh con mới
                    $names = $_FILES['anhcon']['name'];
                    $tmp_names = $_FILES['anhcon']['tmp_name'];
                    foreach (array_combine($tmp_names, $names) as $temp_file => $file_name) {
                        $sql_insert_new_sub_image = "INSERT INTO hinh_anh(id_sp, ten_file, trang_thai) VALUES (?, ?, 1)";
                        $stmt_new_sub_image = mysqli_prepare($conn, $sql_insert_new_sub_image);
                        mysqli_stmt_bind_param($stmt_new_sub_image, 'is', $idsp, $file_name);
                        if (mysqli_stmt_execute($stmt_new_sub_image)) {
                            move_uploaded_file($temp_file, $folder . $file_name);
                        } else {
                            $message .= '<div class="alert-error">Lỗi khi lưu ảnh con ' . htmlspecialchars($file_name) . ' vào CSDL.</div>';
                        }
                        mysqli_stmt_close($stmt_new_sub_image);
                    }
                }

                // Tải lại dữ liệu sản phẩm sau khi cập nhật để hiển thị thông tin mới nhất
                $sql_product_reloaded = "SELECT sp.*, nh.ten_nhanhieu, ds.ten_dong, dm.ten_danhmuc FROM san_pham sp INNER JOIN nhan_hieu nh ON nh.id_nhan = sp.id_nhanhieu INNER JOIN dong_sanpham ds ON ds.id_dong = sp.id_dong INNER JOIN danh_muc dm ON dm.id_danhmuc = sp.id_danhmuc WHERE sp.id_sp = $idsp";
                $query_product_reloaded = mysqli_query($conn, $sql_product_reloaded);
                $row_product = mysqli_fetch_assoc($query_product_reloaded); // Cập nhật $row_product
                
                $sql_main_image_reloaded = "SELECT * FROM hinh_anh WHERE id_sp = $idsp AND trang_thai = 0";
                $query_main_image_reloaded = mysqli_query($conn, $sql_main_image_reloaded);
                $main_image = mysqli_fetch_assoc($query_main_image_reloaded); // Cập nhật ảnh đại diện

                $sql_sub_images_reloaded = "SELECT * FROM hinh_anh WHERE id_sp = $idsp AND trang_thai = 1";
                $query_sub_images_reloaded = mysqli_query($conn, $sql_sub_images_reloaded); // Cập nhật ảnh con

                // Cập nhật thông báo
                if (empty($message)) {
                    $message = '<div class="alert-success">Cập nhật sản phẩm thành công!</div>';
                }
            } else {
                $message = '<div class="alert-error">Có lỗi xảy ra khi cập nhật sản phẩm: ' . mysqli_error($conn) . '</div>';
            }
            mysqli_stmt_close($stmt_update);
        } else {
            $message = '<div class="alert-error">Lỗi chuẩn bị truy vấn SQL cập nhật sản phẩm: ' . mysqli_error($conn) . '</div>';
        }
    }
?>
<div class="main-content">
    <?php echo $message; ?>
    <div class="page-title">
        <div class="title">Cập nhật sản phẩm</div>
    </div>
    <?php if ($idsp_valid && $row_product) : ?>
        <form action="" class="form" method="POST" enctype="multipart/form-data">
            <div class="input-box">
                <label>Mã sản phẩm</label>
                <input type="text" value="<?php echo $idsp ?>" readonly/>
            </div>
            <div class="input-box">
                <label>Tên sản phẩm</label>
                <input type="text" name="tensp" value="<?php echo htmlspecialchars($row_product["ten_sp"]) ?>" required/>
            </div>
            <div class="input-box">
                <label>Mô tả ngắn</label>
                <br>
                <textarea name="mo_ta_ngan" rows="3" cols="100"><?php echo htmlspecialchars($row_product["mo_ta_ngan"]) ?></textarea>
            </div>
            <div class="input-box">
                <label>Mô tả chi tiết</label>
                <br>
                <textarea name="mo_ta_chi_tiet" rows="5" cols="100"><?php echo htmlspecialchars($row_product["mo_ta_chi_tiet"]) ?></textarea>
            </div>
            <div class="input-box">
                <label>Nhãn hiệu</label>
                <div class="select-box">
                    <select name="nhanhieu" required>
                        <?php
                        // Reset con trỏ cho query_brands trước khi lặp
                        if ($query_brands) mysqli_data_seek($query_brands, 0);
                        while ($brand_item = mysqli_fetch_assoc($query_brands)) :
                        ?>
                            <option value="<?php echo $brand_item["id_nhan"] ?>" <?php echo ($row_product["id_nhanhieu"] == $brand_item["id_nhan"]) ? "selected" : "" ?>>
                                <?php echo htmlspecialchars($brand_item["ten_nhanhieu"]) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="input-box">
                <label>Dòng sản phẩm</label>
                <div class="select-box">
                    <select name="dongsp" required>
                        <?php
                        // Reset con trỏ cho query_series trước khi lặp
                        if ($query_series) mysqli_data_seek($query_series, 0);
                        while ($series_item = mysqli_fetch_assoc($query_series)) :
                        ?>
                            <option value="<?php echo $series_item["id_dong"] ?>" <?php echo ($row_product["id_dong"] == $series_item["id_dong"]) ? "selected" : "" ?>>
                                <?php echo htmlspecialchars($series_item["ten_dong"]) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="input-box">
                <label>Danh mục</label>
                <div class="select-box">
                    <select name="danhmuc" required>
                        <?php
                        // Reset con trỏ cho query_categories trước khi lặp
                        if ($query_categories) mysqli_data_seek($query_categories, 0);
                        while ($category_item = mysqli_fetch_assoc($query_categories)) :
                        ?>
                            <option value="<?php echo $category_item["id_danhmuc"] ?>" <?php echo ($row_product["id_danhmuc"] == $category_item["id_danhmuc"]) ? "selected" : "" ?>>
                                <?php echo htmlspecialchars($category_item["ten_danhmuc"]) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="gender-box">
                <h3>Trạng thái</h3>
                <div class="gender-option">
                    <div class="gender">
                    <input style="width: 30px" type="radio" value="0" name="trangthai" <?php echo ($row_product["trangthai"] == 0) ? "checked" : "" ?>/>Hiện
                    </div>
                    <div class="gender">
                    <input style="width: 30px" type="radio" value="1" name="trangthai" <?php echo ($row_product["trangthai"] == 1) ? "checked" : "" ?>/>Ẩn
                    </div>
                </div>
            </div>
            <div class="gender-box">
                <h3>Nổi bật</h3>
                <div class="gender-option">
                    <div class="gender">
                    <input style="width: 30px" type="radio" value="1" name="noi_bat" <?php echo ($row_product["noi_bat"] == 1) ? "checked" : "" ?>/>Có
                    </div>
                    <div class="gender">
                    <input style="width: 30px" type="radio" value="0" name="noi_bat" <?php echo ($row_product["noi_bat"] == 0) ? "checked" : "" ?>/>Không
                    </div>
                </div>
            </div>
            <div class="input-box">
                <label>Ảnh đại diện hiện tại</label>
                <?php if ($main_image && file_exists("../uploads/" . $main_image['ten_file'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($main_image['ten_file']) ?>" width="120px" height="150px">
                <?php else: ?>
                    <p>Chưa có ảnh đại diện hoặc file không tồn tại.</p>
                <?php endif; ?>
                <label>Thay đổi ảnh đại diện</label>
                <input type="file" name="anhdaidien" accept="image/*">
            </div>
            <div class="input-box">
                <label>Các ảnh con hiện tại</label>
                <div class="image-gallery">
                    <?php
                    // Để hiển thị lại ảnh con sau khi update, cần reset con trỏ của query_sub_images
                    if ($query_sub_images && mysqli_num_rows($query_sub_images) > 0) :
                        mysqli_data_seek($query_sub_images, 0);
                        while($anh_con = mysqli_fetch_assoc($query_sub_images)):
                            if (file_exists("../uploads/" . $anh_con['ten_file'])):
                    ?>
                                <img src="../uploads/<?php echo htmlspecialchars($anh_con['ten_file']) ?>" width="120px" height="150px">
                    <?php
                            endif;
                        endwhile;
                    else:
                    ?>
                        <p>Không có ảnh con.</p>
                    <?php endif; ?>
                </div>
                <label>Thêm hoặc thay đổi ảnh con (sẽ xóa ảnh cũ và thêm ảnh mới)</label>
                <input type="file" name="anhcon[]" multiple accept="image/*">
            </div>
            <button name="submit" type="submit">Cập nhật</button>
        </form>
        <br>
        <div class="table-card">
            <div class="card-title"><h3>Danh sách chi tiết sản phẩm (Biến thể)</h3>
            <div class="action-buttons">
                <button class="btn btn-primary">
                <a href="product-detail-add.php?idsp=<?php echo $idsp ?>"><i class="fas fa-plus"></i>
                Thêm biến thể </a>
                </button>
            </div>
        </div>
              
        <table class="data-table">
            <thead>
                <tr>
                <th>Mã SP chi tiết</th>
                <th>SKU</th>
                <th>Số lượng</th>
                <th>Giá nhập</th>
                <th>Giá gốc</th>
                <th>Giá sale</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Lấy lại chi tiết sản phẩm sau khi có thể đã update
                $sql_product_details = "SELECT * FROM sanpham_chitiet WHERE id_sp = $idsp";
                $query_product_details = mysqli_query($conn, $sql_product_details);

                if (mysqli_num_rows($query_product_details) > 0) :
                    while($row_detail = mysqli_fetch_assoc($query_product_details)) :
                ?>
                    <tr>
                        <td><?php echo $row_detail["id_spchitiet"] ?></td>
                        <td><?php echo htmlspecialchars($row_detail["sku"]) ?></td>
                        <td><?php echo $row_detail["so_luong"] ?></td>
                        <td><?php echo number_format($row_detail["gia_nhap"]) ?> VNĐ</td>
                        <td><?php echo number_format($row_detail["gia_goc"]) ?> VNĐ</td>
                        <td><?php echo number_format($row_detail["gia_sale"]) ?> VNĐ</td>
                        <td><?php echo $row_detail["trang_thai"] == 0 ? "Hiện" : "Ẩn" ?></td>
                        <td>
                            <a href="product-detail-update.php?idspchitiet=<?php echo $row_detail["id_spchitiet"] ?>"><button class="btn btn-outline btn-sm">
                                <i class="fa-solid fa-pen-to-square"></i>Sửa
                            </button></a>
                            <a href="product-detail-delete.php?idspchitiet=<?php echo $row_detail["id_spchitiet"] ?>" onclick="return confirm('Bạn có chắc chắn xóa biến thể này không?')"><button class="btn btn-outline btn-sm">
                                <i class="fa-solid fa-trash"></i> Xóa
                            </button></a>
                        </td>
                    </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr>
                        <td colspan="7" class="text-center">Chưa có biến thể sản phẩm nào.</td>
                    </tr>
                <?php
                endif;
                ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include_once "footer.php" ?>