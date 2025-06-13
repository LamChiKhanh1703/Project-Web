<?php
    include_once "sidebar.php";
    require_once "connect.php";

    $idspchitiet = null;
    if(isset($_GET['idspchitiet'])){
        $idspchitiet = $_GET['idspchitiet'];

        $sql_detail = "SELECT * FROM sanpham_chitiet WHERE id_spchitiet = $idspchitiet";
        $query_detail = mysqli_query($conn, $sql_detail);
        $row_detail = mysqli_fetch_assoc($query_detail);
    }

    if(isset($_POST['submit'])){
        $sku = $_POST['sku'];
        $so_luong = $_POST['so_luong'];
        $gia_nhap = $_POST['gia_nhap'];
        $gia_goc = $_POST['gia_goc'];
        $gia_sale = $_POST['gia_sale'];
        $trang_thai = $_POST['trang_thai'];

        $sql = "UPDATE sanpham_chitiet SET 
                sku = '$sku', 
                so_luong = $so_luong, 
                gia_nhap = $gia_nhap, 
                gia_goc = $gia_goc, 
                gia_sale = $gia_sale, 
                trang_thai = $trang_thai
                WHERE id_spchitiet = $idspchitiet";

        $query = mysqli_query($conn, $sql);

        if($query){
            $message = '<div class="alert-success">Cập nhật biến thể sản phẩm thành công!</div>';
            // Cập nhật lại dữ liệu sau khi update
            $sql_detail = "SELECT * FROM sanpham_chitiet WHERE id_spchitiet = $idspchitiet";
            $query_detail = mysqli_query($conn, $sql_detail);
            $row_detail = mysqli_fetch_assoc($query_detail);
        } else {
            $message = '<div class="alert-error">Có lỗi xảy ra. Vui lòng thử lại.</div>';
        }
    }
?>
<div class="main-content">
    <?php echo isset($message) ? $message : "" ?>
    <div class="page-title">
        <div class="title">Chỉnh sửa biến thể sản phẩm ID: <?php echo $idspchitiet ?></div>
    </div>
    <form action="" class="form" method="POST">
        <div class="input-box">
            <label>SKU</label>
            <input type="text" name="sku" value="<?php echo htmlspecialchars($row_detail['sku']) ?>" required/>
        </div>
        <div class="input-box">
            <label>Số lượng</label>
            <input type="number" name="so_luong" value="<?php echo htmlspecialchars($row_detail['so_luong']) ?>" required/>
        </div>
        <div class="input-box">
            <label>Giá nhập</label>
            <input type="number" name="gia_nhap" value="<?php echo htmlspecialchars($row_detail['gia_nhap']) ?>" required/>
        </div>
        <div class="input-box">
            <label>Giá gốc</label>
            <input type="number" name="gia_goc" value="<?php echo htmlspecialchars($row_detail['gia_goc']) ?>" required/>
        </div>
        <div class="input-box">
            <label>Giá sale</label>
            <input type="number" name="gia_sale" value="<?php echo htmlspecialchars($row_detail['gia_sale']) ?>"/>
        </div>
        <div class="gender-box">
            <h3>Trạng thái</h3>
            <div class="gender-option">
                <div class="gender">
                <input style="width: 30px" type="radio" value="0" name="trang_thai" <?php echo ($row_detail['trang_thai'] == 0) ? "checked" : "" ?>/>Hiện
                </div>
                <div class="gender">
                <input style="width: 30px" type="radio" value="1" name="trang_thai" <?php echo ($row_detail['trang_thai'] == 1) ? "checked" : "" ?>/>Ẩn
                </div>
            </div>
        </div>
        <button name="submit">Lưu</button>
    </form>
</div>
<?php include_once "footer.php"; ?>