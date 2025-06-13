<?php
    include_once "sidebar.php";
    require_once "connect.php"; 

    $message = '';
    $customer_id = isset($_GET['id_khach']) ? (int)$_GET['id_khach'] : 0;
    $customer_name = "Khách hàng"; // Tên mặc định

    if ($customer_id > 0) {
        // Lấy thông tin khách hàng
        $sql_customer_info = "SELECT hoten FROM khach_hang WHERE id_khach = ?";
        $stmt_customer_info = mysqli_prepare($conn, $sql_customer_info);
        if ($stmt_customer_info) {
            mysqli_stmt_bind_param($stmt_customer_info, 'i', $customer_id);
            mysqli_stmt_execute($stmt_customer_info);
            $result_customer_info = mysqli_stmt_get_result($stmt_customer_info);
            $customer_row = mysqli_fetch_assoc($result_customer_info);
            mysqli_stmt_close($stmt_customer_info);
            if ($customer_row) {
                $customer_name = htmlspecialchars($customer_row['hoten']);
            } else {
                $message = '<div class="alert-error">Không tìm thấy thông tin khách hàng.</div>';
            }
        } else {
            $message = '<div class="alert-error">Lỗi chuẩn bị truy vấn thông tin khách hàng: ' . mysqli_error($conn) . '</div>';
        }


        // Lấy danh sách đơn hàng của khách hàng này
        $sql_orders = "SELECT dh.*, tt.ten_hinh_thuc, vc.ten_vanchuyen
                       FROM don_hang dh
                       INNER JOIN thanh_toan tt ON dh.id_thanhtoan = tt.id_thanhtoan
                       INNER JOIN van_chuyen vc ON dh.id_vanchuyen = vc.id_vanchuyen
                       WHERE dh.id_khach = ?
                       ORDER BY dh.ngay_tao DESC";

        $stmt_orders = mysqli_prepare($conn, $sql_orders);

        if ($stmt_orders) {
            mysqli_stmt_bind_param($stmt_orders, 'i', $customer_id);
            mysqli_stmt_execute($stmt_orders);
            $result_orders = mysqli_stmt_get_result($stmt_orders);
            $orders = mysqli_fetch_all($result_orders, MYSQLI_ASSOC);
            mysqli_stmt_close($stmt_orders);
        } else {
            $message = '<div class="alert-error">Lỗi khi chuẩn bị truy vấn đơn hàng: ' . mysqli_error($conn) . '</div>';
            $orders = []; // Đảm bảo $orders là một mảng rỗng nếu có lỗi
        }
    } else {
        $message = '<div class="alert-error">ID khách hàng không hợp lệ.</div>';
        $orders = [];
    }
?>

<div class="main-content">
  <div class="page-title">
    <div class="title">Đơn hàng của khách hàng: <?php echo $customer_name; ?></div>
    <div class="action-buttons">
      <button class="btn btn-outline">
        <a href="customer.php"><i class="fas fa-arrow-left"></i>
        Quay lại </a>
      </button>
    </div>
  </div>

  <?php echo $message; ?>

  <div class="table-card">
    <table class="data-table">
      <thead>
        <tr>
          <th>ID Đơn hàng</th>
          <th>Ngày tạo</th>
          <th>Tổng tiền</th>
          <th>Phí ship</th>
          <th>Hình thức TT</th>
          <th>Vận chuyển</th>
          <th>Trạng thái</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($orders)): ?>
            <?php foreach($orders as $order) : ?>
                <tr>
                    <td><?php echo $order["id_donhang"] ?></td>
                    <td><?php echo $order["ngay_tao"] ?></td>
                    <td><?php echo number_format($order["tong_tien"]) ?> VNĐ</td>
                    <td><?php echo number_format($order["phi_ship"]) ?> VNĐ</td>
                    <td><?php echo htmlspecialchars($order["ten_hinh_thuc"]) ?></td>
                    <td><?php echo htmlspecialchars($order["ten_vanchuyen"]) ?></td>
                    <td>
                        <?php
                        switch($order["trang_thai"]) {
                            case 0: echo "<span class='status pending'>Mới tạo</span>"; break;
                            case 1: echo "<span class='status pending'>Đang xử lý</span>"; break;
                            case 2: echo "<span class='status pending'>Đang vận chuyển</span>"; break;
                            case 3: echo "<span class='status active'>Đã hoàn thành</span>"; break;
                            case 4: echo "<span class='status cancelled'>Đã hủy</span>"; break;
                            default: echo "<span class='status pending'>Không xác định</span>";
                        }
                        ?>
                    </td>
                    <td>
                        <a href="order-detail.php?id=<?php echo $order["id_donhang"]?>"><button class="btn btn-outline btn-sm">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </button></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="8" class="text-center">Khách hàng này chưa có đơn hàng nào.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include_once "footer.php" ?>