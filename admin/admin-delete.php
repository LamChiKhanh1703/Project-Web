<?php
    require_once "connect.php";
    // lấy id_admin từ URL về thông qua method GET
    if(isset($_GET["id_admin"])) {
        $id_admin = $_GET["id_admin"];
        // Viết sql để xóa đi admin trong bảng quản trị
        $sql = "DELETE FROM quan_tri WHERE id_admin = $id_admin";
        // Thực thi câu lệnh truy vấn trên
        $query = mysqli_query($conn, $sql);
        // Kiểm tra đã xóa thành công chưa
        if($query) {
            header("Location: admin-list.php");
        } else {
            echo '<div class="alert-error">Có lỗi xảy ra. Vui lòng thử lại.</div>';
        }
    }
?>