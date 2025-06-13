<?php
    include_once "sidebar.php";
    require_once "connect.php";

    $message = '';
    $order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($order_id <= 0) {
        $message = '<div class="alert-error">ID đơn hàng không hợp lệ.</div>';
    } else {
        // Lấy thông tin đơn hàng
        $sql_order = "SELECT dh.*, tt.ten_hinh_thuc, vc.ten_vanchuyen, kh.hoten AS ten_khach_hang, kh.email AS email_khach_hang
                      FROM don_hang dh
                      INNER JOIN thanh_toan tt ON dh.id_thanhtoan = tt.id_thanhtoan
                      INNER JOIN van_chuyen vc ON dh.id_vanchuyen = vc.id_vanchuyen
                      INNER JOIN khach_hang kh ON dh.id_khach = kh.id_khach
                      WHERE dh.id_donhang = ?";
        $stmt_order = mysqli_prepare($conn, $sql_order);
        if ($stmt_order) {
            mysqli_stmt_bind_param($stmt_order, 'i', $order_id);
            mysqli_stmt_execute($stmt_order);
            $result_order = mysqli_stmt_get_result($stmt_order);
            $order = mysqli_fetch_assoc($result_order);
            mysqli_stmt_close($stmt_order);

            if (!$order) {
                $message = '<div class="alert-error">Không tìm thấy đơn hàng này.</div>';
            } else {
                // Lấy chi tiết các sản phẩm trong đơn hàng
                $sql_order_details = "SELECT dhct.soluong, dhct.gia_ban, sp.ten_sp, spct.sku, ha.ten_file AS main_image
                                      FROM donhang_chitiet dhct
                                      INNER JOIN sanpham_chitiet spct ON dhct.id_spchitiet = spct.id_spchitiet
                                      INNER JOIN san_pham sp ON spct.id_sp = sp.id_sp
                                      LEFT JOIN hinh_anh ha ON sp.id_sp = ha.id_sp AND ha.trang_thai = 0
                                      WHERE dhct.id_donhang = ?";
                $stmt_order_details = mysqli_prepare($conn, $sql_order_details);
                if ($stmt_order_details) {
                    mysqli_stmt_bind_param($stmt_order_details, 'i', $order_id);
                    mysqli_stmt_execute($stmt_order_details);
                    $order_details = mysqli_stmt_get_result($stmt_order_details);
                    mysqli_stmt_close($stmt_order_details);
                } else {
                    $message .= '<div class="alert-error">Lỗi khi tải chi tiết sản phẩm đơn hàng: ' . mysqli_error($conn) . '</div>';
                    $order_details = null;
                }
            }
        } else {
            $message = '<div class="alert-error">Lỗi chuẩn bị truy vấn đơn hàng: ' . mysqli_error($conn) . '</div>';
            $order = null;
        }
    }

    // Xử lý cập nhật trạng thái đơn hàng
    if (isset($_POST['update_status']) && $order) {
        $new_status = (int)$_POST['new_status'];
        $order_id_to_update = (int)$_POST['order_id'];

        $sql_update_status = "UPDATE don_hang SET trang_thai = ? WHERE id_donhang = ?";
        $stmt_update_status = mysqli_prepare($conn, $sql_update_status);
        if ($stmt_update_status) {
            mysqli_stmt_bind_param($stmt_update_status, 'ii', $new_status, $order_id_to_update);
            if (mysqli_stmt_execute($stmt_update_status)) {
                $message = '<div class="alert-success">Cập nhật trạng thái đơn hàng thành công!</div>';
                // Cập nhật lại thông tin đơn hàng sau khi sửa
                $order['trang_thai'] = $new_status;
            } else {
                $message = '<div class="alert-error">Lỗi khi cập nhật trạng thái đơn hàng: ' . mysqli_error($conn) . '</div>';
            }
            mysqli_stmt_close($stmt_update_status);
        } else {
            $message = '<div class="alert-error">Lỗi chuẩn bị truy vấn cập nhật trạng thái: ' . mysqli_error($conn) . '</div>';
        }
    }
?>

<div class="main-content">
    <div class="page-title">
        <div class="title">Chi tiết đơn hàng</div>
    </div>

    <?php echo $message; ?>

    <?php if ($order): ?>
        <div class="table-card">
            <h4>Thông tin đơn hàng #<?php echo htmlspecialchars($order['id_donhang']); ?></h4>
            <div class="form">
                <div class="input-box">
                    <label>Tên khách hàng:</label>
                    <input type="text" value="<?php echo htmlspecialchars($order['ten_khach_hang']); ?>" readonly>
                </div>
                <div class="input-box">
                    <label>Email khách hàng:</label>
                    <input type="text" value="<?php echo htmlspecialchars($order['email_khach_hang']); ?>" readonly>
                </div>
                <div class="input-box">
                    <label>Tên người nhận:</label>
                    <input type="text" value="<?php echo htmlspecialchars($order['ten_nguoi_nhan']); ?>" readonly>
                </div>
                <div class="input-box">
                    <label>Số điện thoại:</label>
                    <input type="text" value="<?php echo htmlspecialchars($order['sodienthoai']); ?>" readonly>
                </div>
                <div class="input-box">
                    <label>Địa chỉ:</label>
                    <textarea readonly><?php echo htmlspecialchars($order['diachi']); ?></textarea>
                </div>
                <div class="input-box">
                    <label>Ghi chú:</label>
                    <textarea readonly><?php echo htmlspecialchars($order['ghichu']); ?></textarea>
                </div>
                <div class="input-box">
                    <label>Phương thức thanh toán:</label>
                    <input type="text" value="<?php echo htmlspecialchars($order['ten_hinh_thuc']); ?>" readonly>
                </div>
                <div class="input-box">
                    <label>Phương thức vận chuyển:</label>
                    <input type="text" value="<?php echo htmlspecialchars($order['ten_vanchuyen']); ?>" readonly>
                </div>
                <div class="input-box">
                    <label>Ngày tạo:</label>
                    <input type="text" value="<?php echo htmlspecialchars($order['ngay_tao']); ?>" readonly>
                </div>
                <div class="input-box">
                    <label>Tổng tiền sản phẩm:</label>
                    <input type="text" value="<?php echo number_format($order['tong_tien'] - $order['phi_ship']); ?> VNĐ" readonly>
                </div>
                <div class="input-box">
                    <label>Phí vận chuyển:</label>
                    <input type="text" value="<?php echo number_format($order['phi_ship']); ?> VNĐ" readonly>
                </div>
                <div class="input-box">
                    <label>Tổng tiền đơn hàng:</label>
                    <input type="text" value="<?php echo number_format($order['tong_tien']); ?> VNĐ" readonly>
                </div>
                <div class="input-box">
                    <label>Trạng thái hiện tại:</label>
                    <input type="text" value="<?php
                        switch($order["trang_thai"]) {
                            case 0: echo "Mới tạo"; break;
                            case 1: echo "Đang xử lý"; break;
                            case 2: echo "Đang vận chuyển"; break;
                            case 3: echo "Đã hoàn thành"; break;
                            case 4: echo "Đã hủy"; break;
                            default: echo "Không xác định";
                        }
                    ?>" readonly>
                </div>

                <form action="" method="POST" class="form mt-3">
                    <input type="hidden" name="order_id" value="<?php echo $order['id_donhang']; ?>">
                    <div class="input-box">
                        <label for="new_status">Cập nhật trạng thái:</label>
                        <div class="select-box">
                            <select name="new_status" id="new_status" required>
                                <option value="0" <?php echo ($order['trang_thai'] == 0) ? 'selected' : ''; ?>>Mới tạo</option>
                                <option value="1" <?php echo ($order['trang_thai'] == 1) ? 'selected' : ''; ?>>Đang xử lý</option>
                                <option value="2" <?php echo ($order['trang_thai'] == 2) ? 'selected' : ''; ?>>Đang vận chuyển</option>
                                <option value="3" <?php echo ($order['trang_thai'] == 3) ? 'selected' : ''; ?>>Đã hoàn thành</option>
                                <option value="4" <?php echo ($order['trang_thai'] == 4) ? 'selected' : ''; ?>>Đã hủy</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="update_status">Cập nhật trạng thái</button>
                </form>
            </div>

            <h4 class="mt-4">Chi tiết sản phẩm trong đơn hàng</h4>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>SKU</th>
                        <th>Số lượng</th>
                        <th>Giá bán</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($order_details && mysqli_num_rows($order_details) > 0): ?>
                        <?php mysqli_data_seek($order_details, 0); // Đảm bảo reset con trỏ ?>
                        <?php while ($item = mysqli_fetch_assoc($order_details)): ?>
                            <tr>
                                <td><img src="../uploads/<?php echo htmlspecialchars($item['main_image'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($item['ten_sp']); ?>" width="50"></td>
                                <td><?php echo htmlspecialchars($item['ten_sp']); ?></td>
                                <td><?php echo htmlspecialchars($item['sku']); ?></td>
                                <td><?php echo $item['soluong']; ?></td>
                                <td><?php echo number_format($item['gia_ban']); ?> VNĐ</td>
                                <td><?php echo number_format($item['soluong'] * $item['gia_ban']); ?> VNĐ</td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">Không có sản phẩm nào trong đơn hàng này.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include_once "footer.php"; ?>