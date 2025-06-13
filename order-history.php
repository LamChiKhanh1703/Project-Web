<?php
include_once "includes/header.php"; 
$message = '';


if (!isset($_SESSION['user_id'])) {
    
    header("Location: login.php?redirect=order-history.php");
    exit();
}

$user_id = $_SESSION['user_id'];


$sql_orders = "SELECT dh.*, tt.ten_hinh_thuc, vc.ten_vanchuyen
               FROM don_hang dh
               INNER JOIN thanh_toan tt ON dh.id_thanhtoan = tt.id_thanhtoan
               INNER JOIN van_chuyen vc ON dh.id_vanchuyen = vc.id_vanchuyen
               WHERE dh.id_khach = ?
               ORDER BY dh.ngay_tao DESC";

$stmt_orders = mysqli_prepare($conn, $sql_orders);

if ($stmt_orders) {
    mysqli_stmt_bind_param($stmt_orders, 'i', $user_id);
    mysqli_stmt_execute($stmt_orders);
    $result_orders = mysqli_stmt_get_result($stmt_orders);
    $orders = mysqli_fetch_all($result_orders, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt_orders);
} else {
    $message = '<div class="alert-error">Lỗi khi chuẩn bị truy vấn đơn hàng: ' . mysqli_error($conn) . '</div>';
    $orders = [];
}
?>
<div class="container">
    <h2 class="text-center my-3">Lịch sử đơn hàng của bạn</h2>

    <?php echo $message; ?>

    <?php if (empty($orders)): ?>
        <p class="text-center">Bạn chưa có đơn hàng nào. <a href="product.php">Bắt đầu mua sắm ngay!</a></p>
    <?php else: ?>
        <div class="order-list">
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <h4>Đơn hàng #<?php echo htmlspecialchars($order['id_donhang']); ?></h4>
                        <span class="order-status">
                            <?php
                            switch($order["trang_thai"]) {
                                case 0: echo "<span class='status new-order'>Mới tạo</span>"; break;
                                case 1: echo "<span class='status processing'>Đang xử lý</span>"; break;
                                case 2: echo "<span class='status shipping'>Đang vận chuyển</span>"; break;
                                case 3: echo "<span class='status active'>Đã hoàn thành</span>"; break;
                                case 4: echo "<span class='status cancelled'>Đã hủy</span>"; break;
                                default: echo "<span class='status pending'>Không xác định</span>";
                            }
                            ?>
                        </span>
                    </div>
                    <div class="order-details">
                        <p><strong>Ngày đặt:</strong> <?php echo htmlspecialchars($order['ngay_tao']); ?></p>
                        <p><strong>Tổng tiền:</strong> <?php echo number_format($order['tong_tien']); ?> VNĐ</p>
                        <p><strong>Địa chỉ giao hàng:</strong> <?php echo htmlspecialchars($order['diachi']); ?></p>
                        <p><strong>Phương thức thanh toán:</strong> <?php echo htmlspecialchars($order['ten_hinh_thuc']); ?></p>
                        <p><strong>Phương thức vận chuyển:</strong> <?php echo htmlspecialchars($order['ten_vanchuyen']); ?></p>
                    </div>
                    <div class="order-items">
                        <h5>Sản phẩm:</h5>
                        <ul>
                            <?php
                            // Lấy chi tiết sản phẩm của đơn hàng
                            $sql_order_details = "SELECT dhct.soluong, dhct.gia_ban, sp.ten_sp, ha.ten_file AS main_image
                                                  FROM donhang_chitiet dhct
                                                  INNER JOIN sanpham_chitiet spct ON dhct.id_spchitiet = spct.id_spchitiet
                                                  INNER JOIN san_pham sp ON spct.id_sp = sp.id_sp
                                                  LEFT JOIN hinh_anh ha ON sp.id_sp = ha.id_sp AND ha.trang_thai = 0
                                                  WHERE dhct.id_donhang = ?";
                            $stmt_order_details = mysqli_prepare($conn, $sql_order_details);
                            if ($stmt_order_details) {
                                mysqli_stmt_bind_param($stmt_order_details, 'i', $order['id_donhang']);
                                mysqli_stmt_execute($stmt_order_details);
                                $result_order_details = mysqli_stmt_get_result($stmt_order_details);
                                while ($item = mysqli_fetch_assoc($result_order_details)):
                            ?>
                                <li>
                                    <img src="uploads/<?php echo htmlspecialchars($item['main_image'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($item['ten_sp']); ?>" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                    <span><?php echo htmlspecialchars($item['ten_sp']); ?> x <?php echo $item['soluong']; ?> (<?php echo number_format($item['gia_ban']); ?> VNĐ/sp) = **<?php echo number_format($item['soluong'] * $item['gia_ban']); ?> VNĐ**</span>
                                </li>
                            <?php
                                endwhile;
                                mysqli_stmt_close($stmt_order_details);
                            } else {
                                echo "<li>Lỗi khi tải chi tiết đơn hàng.</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include_once "includes/footer.php"; ?>