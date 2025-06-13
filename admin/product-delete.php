<?php
    require_once "connect.php";
    // Lấy id_sp từ URL về thông qua method GET
    if(isset($_GET["idsp"])) {
        $id_sp = $_GET["idsp"];

        // Bước 1: Xóa các hình ảnh liên quan đến sản phẩm
        $sql_delete_images = "DELETE FROM hinh_anh WHERE id_sp = $id_sp";
        mysqli_query($conn, $sql_delete_images);

        // Bước 2: Xóa các chi tiết sản phẩm liên quan
        $sql_delete_details = "DELETE FROM sanpham_chitiet WHERE id_sp = $id_sp";
        mysqli_query($conn, $sql_delete_details);

        // Bước 3: Xóa sản phẩm chính
        $sql_delete_product = "DELETE FROM san_pham WHERE id_sp = $id_sp";
        $query_delete_product = mysqli_query($conn, $sql_delete_product);

        // Kiểm tra xem đã xóa thành công chưa
        if($query_delete_product) {
            header("Location: product-list.php");
        } else {
            echo '<div class="alert-error">Có lỗi xảy ra khi xóa sản phẩm. Vui lòng thử lại.</div>';
        }
    }
?>