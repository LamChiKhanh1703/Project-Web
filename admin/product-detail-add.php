<?php
    include_once "sidebar.php";
    require_once "connect.php";

    $idsp = null;
    if(isset($_GET['idsp'])){
        $idsp = $_GET['idsp'];
    }

    if(isset($_POST['submit'])){
        $sku = $_POST['sku'];
        $so_luong = $_POST['so_luong'];
        $gia_nhap = $_POST['gia_nhap'];
        $gia_goc = $_POST['gia_goc'];
        $gia_sale = $_POST['gia_sale'];
        $trang_thai = $_POST['trang_thai'];
        // Bạn có thể thêm các trường như id_khuyenmai, id_mau, id_dungluong nếu cần cho đồ chơi
        // Hiện tại webdochoi.sql không có bảng mau_sac hay dung_luong trong sanpham_chitiet,
        // nếu bạn muốn thêm, cần cập nhật CSDL và code tương ứng.

        $sql = "INSERT INTO sanpham_chitiet (id_sp, sku, so_luong, gia_nhap, gia_goc, gia_sale, trang_thai)
                VALUES ($idsp, '$sku', $so_luong, $gia_nhap, $gia_goc, $gia_sale, $trang_thai)";

        $query = mysqli_query($conn, $sql);

        if($query){
            $message = '<div class="alert-success">Thêm biến thể sản phẩm thành công!</div>';
        } else {
            $message = '<div class="alert-error">Có lỗi xảy ra. Vui lòng thử lại.</div>';
        }
    }
?>
<div class="main-content">
    <?php echo isset($message) ? $message : "" ?>
    <div class="page-title">
        <div class="title">Thêm biến thể sản phẩm cho SP ID: <?php echo $idsp ?></div>
    </div>
    <form action="" class="form" method="POST">
        <div class="input-box">
            <label>SKU</label>
            <input type="text" name="sku" required/>
        </div>
        <div class="input-box">
            <label>Số lượng</label>
            <input type="number" name="so_luong" required/>
        </div>
        <div class="input-box">
            <label>Giá nhập</label>
            <input type="number" name="gia_nhap" required/>
        </div>
        <div class="input-box">
            <label>Giá gốc</label>
            <input type="number" name="gia_goc" required/>
        </div>
        <div class="input-box">
            <label>Giá sale</label>
            <input type="number" name="gia_sale"/>
        </div>
        <div class="gender-box">
            <h3>Trạng thái</h3>
            <div class="gender-option">
                <div class="gender">
                <input style="width: 30px" type="radio" value="0" name="trang_thai" checked/>Hiện
                </div>
                <div class="gender">
                <input style="width: 30px" type="radio" value="1" name="trang_thai" />Ẩn
                </div>
            </div>
        </div>
        <button name="submit">Lưu biến thể</button>
    </form>
</div>
<?php include_once "footer.php"; ?>