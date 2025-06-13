<?php
    include_once "sidebar.php";
    require_once "connect.php";
    $message = ''; // Biến để lưu thông báo

    // Lấy id sản phẩm trên đường dẫn URL thông qua method GET
    if(isset($_GET["idsp"])) {
        $idsp = $_GET["idsp"];
        // Hiển thị thông tin chi tiết cho sản phẩm có idsp đã lấy
        $sql = "SELECT sp.*, nh.ten_nhanhieu, ds.ten_dong, dm.ten_danhmuc
                FROM san_pham sp
                INNER JOIN nhan_hieu nh ON nh.id_nhan = sp.id_nhanhieu
                INNER JOIN dong_sanpham ds ON ds.id_dong = sp.id_dong
                INNER JOIN danh_muc dm ON dm.id_danhmuc = sp.id_danhmuc
                WHERE sp.id_sp = $idsp";
        // Thực thi truy vấn
        $query = mysqli_query($conn, $sql);
        // Lấy dữ liệu
        $row = mysqli_fetch_assoc($query);

        // Kiểm tra nếu không tìm thấy sản phẩm
        if (!$row) {
            $message = '<div class="alert-error">Không tìm thấy sản phẩm với ID này hoặc sản phẩm không tồn tại.</div>';
            $idsp_valid = false; // Đánh dấu là ID không hợp lệ
        } else {
            $idsp_valid = true;

            // Truy vấn lấy ảnh đại diện của sản phẩm này
            $sql4 = "SELECT * FROM hinh_anh WHERE id_sp = $idsp AND trang_thai = 0";
            $query4 = mysqli_query($conn, $sql4);
            $anhdaidien = mysqli_fetch_assoc($query4);

            // Truy vấn lấy tất cả các ảnh con của sản phẩm này
            $sql5 = "SELECT * FROM hinh_anh WHERE id_sp = $idsp AND trang_thai = 1";
            $query5 = mysqli_query($conn, $sql5);

            // Truy vấn lấy các sản phẩm chi tiết (biến thể) của idsp trên
            // Loại bỏ JOIN với mau_sac và dung_luong vì bảng sanpham_chitiet không có cột đó
            $sql3 = "SELECT * FROM sanpham_chitiet WHERE id_sp = $idsp";
            $query3 = mysqli_query($conn, $sql3);
        }
    } else {
        $message = '<div class="alert-error">Không có ID sản phẩm được cung cấp.</div>';
        $idsp_valid = false;
    }
?>
<div class="main-content">
    <?php echo $message; ?>
    <div class="page-title">
        <div class="title">Chi tiết sản phẩm</div>
    </div>
    <?php if ($idsp_valid && $row) : ?>
        <form action="" class="form">
            <div class="input-box">
                <label>Mã sản phẩm</label>
                <input type="text" name="masp" value="<?php echo $idsp ?>" readonly/>
            </div>
            <div class="input-box">
                <label>Tên sản phẩm</label>
                <input type="text" name="tensp" value="<?php echo htmlspecialchars($row["ten_sp"]) ?>" readonly/>
            </div>
            <div class="input-box">
                <label>Mô tả ngắn</label>
                <br>
                <textarea name="mo_ta_ngan" rows="3" cols="100" readonly><?php echo htmlspecialchars($row["mo_ta_ngan"]) ?></textarea>
            </div>
            <div class="input-box">
                <label>Mô tả chi tiết</label>
                <br>
                <textarea name="mo_ta_chi_tiet" rows="5" cols="100" readonly><?php echo htmlspecialchars($row["mo_ta_chi_tiet"]) ?></textarea>
            </div>
            <div class="input-box">
                <label>Nhãn hiệu</label>
                <input type="text" value="<?php echo htmlspecialchars($row["ten_nhanhieu"]) ?>" readonly/>
            </div>
            <div class="input-box">
                <label>Dòng sản phẩm</label>
                <input type="text" value="<?php echo htmlspecialchars($row["ten_dong"]) ?>" readonly/>
            </div>
            <div class="input-box">
                <label>Danh mục</label>
                <input type="text" value="<?php echo htmlspecialchars($row["ten_danhmuc"]) ?>" readonly/>
            </div>
            <div class="input-box">
                <label>Trạng thái</label>
                <input type="text" value="<?php echo ($row["trangthai"] == 0) ? "Hiện" : "Ẩn" ?>" readonly/>
            </div>
            <div class="input-box">
                <label>Nổi bật</label>
                <input type="text" value="<?php echo ($row["noi_bat"] == 1) ? "Có" : "Không" ?>" readonly/>
            </div>
            <div class="input-box">
                <label>Ảnh đại diện sản phẩm</label>
                <?php if ($anhdaidien && file_exists("../uploads/" . $anhdaidien['ten_file'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($anhdaidien['ten_file']) ?>" width="120px" height="150px">
                <?php else: ?>
                    <p>Không có ảnh đại diện hoặc file không tồn tại.</p>
                <?php endif; ?>
            </div>
            <div class="input-box">
                <label>Các ảnh con sản phẩm</label>
                <div class="image-gallery">
                    <?php
                    if ($query5 && mysqli_num_rows($query5) > 0) :
                        mysqli_data_seek($query5, 0); // Reset con trỏ
                        while($anh = mysqli_fetch_assoc($query5)):
                            if (file_exists("../uploads/" . $anh['ten_file'])):
                    ?>
                                <img src="../uploads/<?php echo htmlspecialchars($anh['ten_file']) ?>" width="120px" height="150px">
                    <?php
                            endif;
                        endwhile;
                    else:
                    ?>
                        <p>Không có ảnh con.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="action-buttons text-center">
                <a href="product-update.php?idsp=<?php echo $idsp ?>"><button type="button" class="btn btn-primary">
                    <i class="fa-solid fa-pen-to-square"></i> Sửa sản phẩm
                </button></a>
                <a href="product-delete.php?idsp=<?php echo $idsp ?>" onclick="return confirm('Bạn có chắc chắn xóa sản phẩm này không?')"><button type="button" class="btn btn-outline">
                    <i class="fa-solid fa-trash"></i> Xóa sản phẩm
                </button></a>
            </div>
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
                // Kiểm tra lại tồn kho và trạng thái biến thể
                $sql_variants_for_table = "SELECT id_spchitiet, sku, so_luong, gia_nhap, gia_goc, gia_sale, trang_thai FROM sanpham_chitiet WHERE id_sp = $idsp ORDER BY id_spchitiet ASC";
                $query_variants_for_table = mysqli_query($conn, $sql_variants_for_table);

                if (mysqli_num_rows($query_variants_for_table) > 0) :
                    while($row_detail = mysqli_fetch_assoc($query_variants_for_table)) :
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

<?php include_once "footer.php"; ?>
