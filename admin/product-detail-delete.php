<?php
    require_once "connect.php";
    // Lấy id_spchitiet từ URL về thông qua method GET
    if(isset($_GET["idspchitiet"])) {
        $id_spchitiet = $_GET["idspchitiet"];

        // Lấy id_sp để quay lại trang chi tiết sản phẩm sau khi xóa
        $sql_get_idsp = "SELECT id_sp FROM sanpham_chitiet WHERE id_spchitiet = $id_spchitiet";
        $query_get_idsp = mysqli_query($conn, $sql_get_idsp);
        $row_get_idsp = mysqli_fetch_assoc($query_get_idsp);
        $id_sp_parent = $row_get_idsp['id_sp'];

        // Viết sql để xóa chi tiết sản phẩm
        $sql = "DELETE FROM sanpham_chitiet WHERE id_spchitiet = $id_spchitiet";
        // Thực thi câu lệnh truy vấn trên
        $query = mysqli_query($conn, $sql);
        // Kiểm tra xem đã xóa thành công chưa
        if($query) {
            header("Location: product-detail.php?idsp=$id_sp_parent"); // Quay lại trang chi tiết sản phẩm cha
        } else {
            echo '<div class="alert-error">Có lỗi xảy ra khi xóa biến thể sản phẩm. Vui lòng thử lại.</div>';
        }
    }
?>