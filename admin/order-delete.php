<?php
    require_once "connect.php";
    // lấy id từ URL về thông qua method GET
    if(isset($_GET["id"])) {
        $order_id = (int)$_GET["id"];

        // Lấy chi tiết đơn hàng để hoàn trả tồn kho
        $sql_get_items = "SELECT id_spchitiet, soluong FROM donhang_chitiet WHERE id_donhang = ?";
        $stmt_get_items = mysqli_prepare($conn, $sql_get_items);
        if ($stmt_get_items) {
            mysqli_stmt_bind_param($stmt_get_items, 'i', $order_id);
            mysqli_stmt_execute($stmt_get_items);
            $result_items = mysqli_stmt_get_result($stmt_get_items);
            mysqli_stmt_close($stmt_get_items);

            mysqli_begin_transaction($conn);
            try {
                while ($item = mysqli_fetch_assoc($result_items)) {
                    $id_spchitiet = $item['id_spchitiet'];
                    $soluong = $item['soluong'];
                    // Hoàn trả tồn kho
                    $sql_return_stock = "UPDATE sanpham_chitiet SET so_luong = so_luong + ? WHERE id_spchitiet = ?";
                    $stmt_return_stock = mysqli_prepare($conn, $sql_return_stock);
                    if (!$stmt_return_stock) throw new Exception("Lỗi prepared statement hoàn trả tồn kho.");
                    mysqli_stmt_bind_param($stmt_return_stock, 'ii', $soluong, $id_spchitiet);
                    if (!mysqli_stmt_execute($stmt_return_stock)) throw new Exception("Lỗi hoàn trả tồn kho.");
                    mysqli_stmt_close($stmt_return_stock);
                }

                // Xóa chi tiết đơn hàng
                $sql_delete_details = "DELETE FROM donhang_chitiet WHERE id_donhang = ?";
                $stmt_delete_details = mysqli_prepare($conn, $sql_delete_details);
                if (!$stmt_delete_details) throw new Exception("Lỗi prepared statement xóa chi tiết đơn hàng.");
                mysqli_stmt_bind_param($stmt_delete_details, 'i', $order_id);
                if (!mysqli_stmt_execute($stmt_delete_details)) throw new Exception("Lỗi xóa chi tiết đơn hàng.");
                mysqli_stmt_close($stmt_delete_details);

                // Xóa đơn hàng chính
                $sql_delete_order = "DELETE FROM don_hang WHERE id_donhang = ?";
                $stmt_delete_order = mysqli_prepare($conn, $sql_delete_order);
                if (!$stmt_delete_order) throw new Exception("Lỗi prepared statement xóa đơn hàng.");
                mysqli_stmt_bind_param($stmt_delete_order, 'i', $order_id);
                if (!mysqli_stmt_execute($stmt_delete_order)) throw new Exception("Lỗi xóa đơn hàng.");
                mysqli_stmt_close($stmt_delete_order);

                mysqli_commit($conn);
                header("Location: order.php");
                exit();
            } catch (Exception $e) {
                mysqli_rollback($conn);
                echo '<div class="alert-error">Có lỗi xảy ra khi xóa đơn hàng: ' . $e->getMessage() . '. Vui lòng thử lại.</div>';
            }
        } else {
            echo '<div class="alert-error">Lỗi khi lấy thông tin đơn hàng để xóa.</div>';
        }
    } else {
        echo '<div class="alert-error">Không có ID đơn hàng để xóa.</div>';
    }
?>