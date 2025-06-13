<?php
include_once "includes/header.php"; // Đảm bảo đã bật hiển thị lỗi ở đây

// Kiểm tra xem giỏ hàng có trống không hoặc có đang mua ngay không
// Nếu không có sản phẩm nào để đặt hàng, chuyển hướng về trang giỏ hàng
if (empty($_SESSION['cart']) && !(isset($_GET['action']) && $_GET['action'] == 'buy_now')) {
    header("Location: cart.php?msg=" . urlencode("Giỏ hàng của bạn đang trống."));
    exit();
}

$total_amount_to_pay = 0;
$checkout_items = [];
$current_action = ''; // Biến để lưu trạng thái đang mua ngay hay từ giỏ hàng
$message = ''; // Khởi tạo biến message để luôn có giá trị

// Xử lý mua ngay từ trang chi tiết sản phẩm
if (isset($_GET['action']) && $_GET['action'] == 'buy_now') {
    $current_action = 'buy_now';
    $id_spchitiet = isset($_GET['id_spchitiet']) ? (int)$_GET['id_spchitiet'] : 0;
    $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

    if ($id_spchitiet > 0 && $quantity > 0) {
        $sql_variant_info = "SELECT spct.id_spchitiet, spct.so_luong, spct.gia_goc, spct.gia_sale, sp.ten_sp, ha.ten_file AS main_image, sp.id_sp
                             FROM sanpham_chitiet spct
                             INNER JOIN san_pham sp ON spct.id_sp = sp.id_sp
                             LEFT JOIN hinh_anh ha ON sp.id_sp = ha.id_sp AND ha.trang_thai = 0
                             WHERE spct.id_spchitiet = ?";
        $stmt_variant = mysqli_prepare($conn, $sql_variant_info);

        if ($stmt_variant) {
            mysqli_stmt_bind_param($stmt_variant, 'i', $id_spchitiet);
            mysqli_stmt_execute($stmt_variant);
            $result_variant = mysqli_stmt_get_result($stmt_variant);
            $variant_info = mysqli_fetch_assoc($result_variant);
            mysqli_stmt_close($stmt_variant);

            if ($variant_info && $quantity <= $variant_info['so_luong']) {
                $price_per_item = $variant_info['gia_sale'] ?? $variant_info['gia_goc'];
                $checkout_items[$id_spchitiet] = [
                    'product_id' => $variant_info['id_sp'], // Thêm product_id vào đây
                    'id_spchitiet' => $variant_info['id_spchitiet'],
                    'name' => $variant_info['ten_sp'],
                    'image' => $variant_info['main_image'],
                    'price' => $price_per_item,
                    'quantity' => $quantity,
                    'stock' => $variant_info['so_luong']
                ];
                $total_amount_to_pay = $price_per_item * $quantity;
            } else {
                header("Location: cart.php?msg=" . urlencode("Sản phẩm không đủ số lượng hoặc không tồn tại để mua ngay."));
                exit();
            }
        } else {
            header("Location: cart.php?msg=" . urlencode("Lỗi khi chuẩn bị truy vấn thông tin biến thể (Buy Now): " . mysqli_error($conn)));
            exit();
        }
    } else {
        header("Location: cart.php?msg=" . urlencode("Thông tin mua ngay không hợp lệ."));
        exit();
    }
} else {
    // Mua từ giỏ hàng (kiểm tra lại tồn kho cho từng sản phẩm trong giỏ)
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item_key => $item) {
            $id_spchitiet_cart = $item['id_spchitiet'];
            $quantity_cart = $item['quantity'];

            $sql_variant_check = "SELECT spct.so_luong, spct.gia_goc, spct.gia_sale, sp.ten_sp, ha.ten_file AS main_image, sp.id_sp
                                  FROM sanpham_chitiet spct
                                  INNER JOIN san_pham sp ON spct.id_sp = sp.id_sp
                                  LEFT JOIN hinh_anh ha ON sp.id_sp = ha.id_sp AND ha.trang_thai = 0
                                  WHERE spct.id_spchitiet = ?";
            $stmt_variant_check = mysqli_prepare($conn, $sql_variant_check);
            if ($stmt_variant_check) {
                mysqli_stmt_bind_param($stmt_variant_check, 'i', $id_spchitiet_cart);
                mysqli_stmt_execute($stmt_variant_check);
                $result_variant_check = mysqli_stmt_get_result($stmt_variant_check);
                $variant_info_check = mysqli_fetch_assoc($result_variant_check);
                mysqli_stmt_close($stmt_variant_check);

                if ($variant_info_check && $quantity_cart <= $variant_info_check['so_luong']) {
                    $price_per_item_check = $variant_info_check['gia_sale'] ?? $variant_info_check['gia_goc'];
                    $checkout_items[$item_key] = [
                        'product_id' => $variant_info_check['id_sp'],
                        'id_spchitiet' => $id_spchitiet_cart,
                        'name' => $variant_info_check['ten_sp'],
                        'image' => $variant_info_check['main_image'],
                        'price' => $price_per_item_check,
                        'quantity' => $quantity_cart,
                        'stock' => $variant_info_check['so_luong']
                    ];
                    $total_amount_to_pay += ($price_per_item_check * $quantity_cart);
                } else {
                    // Nếu sản phẩm trong giỏ hàng không hợp lệ (hết hàng, bị xóa, ...)
                    // Loại bỏ khỏi giỏ hàng session và thông báo cho người dùng
                    unset($_SESSION['cart'][$item_key]);
                    $message .= '<div class="alert-error">Sản phẩm "' . htmlspecialchars($item['name']) . '" không đủ số lượng hoặc không khả dụng. Đã loại bỏ khỏi giỏ hàng.</div>';
                }
            } else {
                $message .= '<div class="alert-error">Lỗi khi kiểm tra giỏ hàng: ' . mysqli_error($conn) . '</div>';
            }
        }
    }
    // Nếu sau khi kiểm tra, checkout_items vẫn trống, chuyển hướng
    if (empty($checkout_items)) {
        header("Location: cart.php?msg=" . urlencode("Giỏ hàng của bạn đã được cập nhật hoặc không có sản phẩm nào khả dụng."));
        exit();
    }
}


// Lấy thông tin khách hàng nếu đã đăng nhập để điền sẵn vào form
$customer_info = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql_customer = "SELECT hoten, sodienthoai, email, diachi FROM khach_hang WHERE id_khach = $user_id";
    $query_customer = mysqli_query($conn, $sql_customer);
    $customer_info = mysqli_fetch_assoc($query_customer);
}

// Lấy danh sách phương thức thanh toán
$sql_payments = "SELECT * FROM thanh_toan WHERE trang_thai = 0";
$query_payments = mysqli_query($conn, $sql_payments);
$payment_methods = mysqli_fetch_all($query_payments, MYSQLI_ASSOC);

// Lấy danh sách phương thức vận chuyển
$sql_shippings = "SELECT * FROM van_chuyen WHERE trang_thai = 0";
$query_shippings = mysqli_query($conn, $sql_shippings);
$shipping_methods = mysqli_fetch_all($query_shippings, MYSQLI_ASSOC);


// Xử lý khi đặt hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    // Kiểm tra người dùng đã đăng nhập chưa
    if (!isset($_SESSION['user_id'])) {
        $message = '<div class="alert-error">Bạn cần đăng nhập để đặt hàng.</div>';
    }
    // Kiểm tra giỏ hàng có rỗng không (sau khi đã kiểm tra và lọc ở trên)
    else if (empty($checkout_items)) {
        $message = '<div class="alert-error">Không có sản phẩm nào để đặt hàng sau khi kiểm tra tồn kho. Vui lòng kiểm tra lại giỏ hàng.</div>';
    } else {
        $user_id = $_SESSION['user_id'];
        $ten_nguoi_nhan = trim($_POST['ten_nguoi_nhan']);
        $sodienthoai = trim($_POST['sodienthoai']);
        $email = trim($_POST['email']);
        $diachi = trim($_POST['diachi']);
        $ghichu = trim($_POST['ghichu'] ?? '');
        $id_thanhtoan = (int)($_POST['id_thanhtoan'] ?? 0);
        $id_vanchuyen = (int)($_POST['id_vanchuyen'] ?? 0);

        // Kiểm tra dữ liệu đầu vào cơ bản
        if (empty($ten_nguoi_nhan) || empty($sodienthoai) || empty($email) || empty($diachi) || $id_thanhtoan <= 0 || $id_vanchuyen <= 0) {
            $message = '<div class="alert-error">Vui lòng điền đầy đủ thông tin người nhận và chọn phương thức thanh toán/vận chuyển.</div>';
        } else {
            // Lấy phí ship
            $phi_ship = 0;
            $shipping_method_found = false;
            foreach ($shipping_methods as $method) {
                if ($method['id_vanchuyen'] == $id_vanchuyen) {
                    $phi_ship = $method['phi_ship'];
                    $shipping_method_found = true;
                    break;
                }
            }

            if (!$shipping_method_found) {
                 $message = '<div class="alert-error">Phương thức vận chuyển không hợp lệ hoặc chưa được chọn.</div>';
            } else {
                $final_total_amount = $total_amount_to_pay + $phi_ship;

                // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
                mysqli_begin_transaction($conn);
                try {
                    // 1. Tạo đơn hàng
                    $sql_insert_order = "INSERT INTO don_hang (ten_nguoi_nhan, sodienthoai, email, diachi, ghichu, id_khach, id_thanhtoan, id_vanchuyen, tong_tien, phi_ship, trang_thai, ngay_tao)
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())"; // trang_thai = 0 (mới tạo)
                    $stmt_order = mysqli_prepare($conn, $sql_insert_order);
                    if (!$stmt_order) {
                        throw new Exception("Lỗi chuẩn bị truy vấn đơn hàng: " . mysqli_error($conn));
                    }
                    mysqli_stmt_bind_param($stmt_order, 'sssssiiiii',
                        $ten_nguoi_nhan, $sodienthoai, $email, $diachi, $ghichu,
                        $user_id, $id_thanhtoan, $id_vanchuyen, $final_total_amount, $phi_ship);
                    
                    if (!mysqli_stmt_execute($stmt_order)) {
                        throw new Exception("Lỗi khi tạo đơn hàng: " . mysqli_error($conn));
                    }
                    $order_id = mysqli_insert_id($conn);
                    mysqli_stmt_close($stmt_order);

                    if (!$order_id) {
                        throw new Exception("Không lấy được ID đơn hàng sau khi tạo.");
                    }

                    // 2. Thêm chi tiết đơn hàng và cập nhật số lượng tồn kho
                    foreach ($checkout_items as $item) {
                        $id_spchitiet_item = $item['id_spchitiet'];
                        $quantity_item = $item['quantity'];
                        $price_item = $item['price'];

                        // Kiểm tra lại tồn kho trước khi đặt hàng (trong transaction)
                        $sql_check_stock = "SELECT so_luong FROM sanpham_chitiet WHERE id_spchitiet = ? FOR UPDATE"; // FOR UPDATE để khóa hàng
                        $stmt_check_stock = mysqli_prepare($conn, $sql_check_stock);
                        if (!$stmt_check_stock) {
                             throw new Exception("Lỗi chuẩn bị truy vấn kiểm tra tồn kho: " . mysqli_error($conn));
                        }
                        mysqli_stmt_bind_param($stmt_check_stock, 'i', $id_spchitiet_item);
                        mysqli_stmt_execute($stmt_check_stock);
                        $res_check_stock = mysqli_stmt_get_result($stmt_check_stock);
                        $row_check_stock = mysqli_fetch_assoc($res_check_stock);
                        mysqli_stmt_close($stmt_check_stock);

                        if (!$row_check_stock || $row_check_stock['so_luong'] < $quantity_item) {
                            throw new Exception("Sản phẩm '" . htmlspecialchars($item['name']) . "' không đủ số lượng tồn kho hoặc không tồn tại.");
                        }

                        // Thêm vào donhang_chitiet
                        $sql_insert_order_detail = "INSERT INTO donhang_chitiet (id_donhang, id_spchitiet, soluong, gia_ban)
                                                    VALUES (?, ?, ?, ?)";
                        $stmt_order_detail = mysqli_prepare($conn, $sql_insert_order_detail);
                        if (!$stmt_order_detail) {
                            throw new Exception("Lỗi chuẩn bị truy vấn chi tiết đơn hàng: " . mysqli_error($conn));
                        }
                        mysqli_stmt_bind_param($stmt_order_detail, 'iiii', $order_id, $id_spchitiet_item, $quantity_item, $price_item);
                        if (!mysqli_stmt_execute($stmt_order_detail)) {
                            throw new Exception("Lỗi khi thêm chi tiết đơn hàng: " . mysqli_error($conn));
                        }
                        mysqli_stmt_close($stmt_order_detail);

                        // Cập nhật số lượng tồn kho
                        $sql_update_stock = "UPDATE sanpham_chitiet SET so_luong = so_luong - ? WHERE id_spchitiet = ?";
                        $stmt_update_stock = mysqli_prepare($conn, $sql_update_stock);
                        if (!$stmt_update_stock) {
                            throw new Exception("Lỗi chuẩn bị truy vấn cập nhật tồn kho: " . mysqli_error($conn));
                        }
                        mysqli_stmt_bind_param($stmt_update_stock, 'ii', $quantity_item, $id_spchitiet_item);
                        if (!mysqli_stmt_execute($stmt_update_stock)) {
                            throw new Exception("Lỗi khi cập nhật tồn kho cho sản phẩm " . htmlspecialchars($item['name']));
                        }
                        mysqli_stmt_close($stmt_update_stock);
                    }

                    // 3. Xóa giỏ hàng sau khi đặt hàng thành công (chỉ khi đặt từ giỏ hàng)
                    if ($current_action != 'buy_now') {
                        unset($_SESSION['cart']); // Xóa giỏ hàng sau khi đặt thành công
                    }
                    // Nếu là buy_now, không cần xóa giỏ hàng session (không có session giỏ hàng trong trường hợp này)

                    mysqli_commit($conn);
                    $message = '<div class="alert-success">Đặt hàng thành công! Mã đơn hàng của bạn là: <strong>' . $order_id . '</strong>.</div>';
                    // Clear checkout_items để không hiển thị lại đơn hàng vừa đặt trên form
                    $checkout_items = []; // Xóa các mặt hàng để form trở lại trạng thái ban đầu

                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $message = '<div class="alert-error">Đặt hàng thất bại: ' . $e->getMessage() . ' Vui lòng thử lại.</div>';
                }
            }
        }
    }
}
?>

<div class="container">
    <h2 class="text-center my-3">Thông tin đặt hàng</h2>

    <?php echo $message; // Hiển thị thông báo (nếu có) ?>

    <?php if (empty($checkout_items)): ?>
        <p class="text-center">Bạn không có sản phẩm nào để đặt hàng. <a href="product.php">Tiếp tục mua sắm!</a></p>
    <?php else: ?>
        <form action="" method="POST" class="auth-form-container">
            <h3>Thông tin người nhận</h3>
            <div class="input-box">
                <label for="ten_nguoi_nhan">Họ tên:</label>
                <input type="text" id="ten_nguoi_nhan" name="ten_nguoi_nhan" value="<?php echo htmlspecialchars($customer_info['hoten'] ?? ''); ?>" required>
            </div>
            <div class="input-box">
                <label for="sodienthoai">Số điện thoại:</label>
                <input type="text" id="sodienthoai" name="sodienthoai" value="<?php echo htmlspecialchars($customer_info['sodienthoai'] ?? ''); ?>" required>
            </div>
            <div class="input-box">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($customer_info['email'] ?? ''); ?>" required>
            </div>
            <div class="input-box">
                <label for="diachi">Địa chỉ:</label>
                <textarea id="diachi" name="diachi" rows="3" required><?php echo htmlspecialchars($customer_info['diachi'] ?? ''); ?></textarea>
            </div>
            <div class="input-box">
                <label for="ghichu">Ghi chú (Tùy chọn):</label>
                <textarea id="ghichu" name="ghichu" rows="3"></textarea>
            </div>

            <h3 class="mt-3">Phương thức thanh toán</h3>
            <div class="gender-box">
                <?php if (!empty($payment_methods)): ?>
                    <?php foreach($payment_methods as $payment): ?>
                        <div class="gender-option">
                            <div class="gender">
                                <input type="radio" name="id_thanhtoan" value="<?php echo $payment['id_thanhtoan']; ?>" class="payment-method-radio" data-info="<?php echo htmlspecialchars($payment['thong_tin_them'] ?? ''); ?>" required>
                                <?php echo htmlspecialchars($payment['ten_hinh_thuc']); ?>
                                <?php if (!empty($payment['mo_ta'])): ?>
                                    <small>(<?php echo htmlspecialchars($payment['mo_ta']); ?>)</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div id="payment-info-display" style="margin-top: 10px; font-weight: bold; color: #007bff;"></div>
                <?php else: ?>
                    <p>Không có phương thức thanh toán nào.</p>
                <?php endif; ?>
            </div>

            <h3 class="mt-3">Phương thức vận chuyển</h3>
            <div class="gender-box">
                <?php if (!empty($shipping_methods)): ?>
                    <?php foreach($shipping_methods as $shipping): ?>
                        <div class="gender-option">
                            <div class="gender">
                                <input type="radio" name="id_vanchuyen" value="<?php echo $shipping['id_vanchuyen']; ?>" data-ship-fee="<?php echo $shipping['phi_ship']; ?>" required>
                                <?php echo htmlspecialchars($shipping['ten_vanchuyen']); ?>
                                <?php if (!empty($shipping['mo_ta'])): ?>
                                    <small>(<?php echo htmlspecialchars($shipping['mo_ta']); ?>)</small>
                                <?php endif; ?>
                                (Phí: <?php echo number_format($shipping['phi_ship']); ?> VNĐ - Thời gian: <?php echo htmlspecialchars($shipping['thoi_gian']); ?>)
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Không có phương thức vận chuyển nào.</p>
                <?php endif; ?>
            </div>

            <h3 class="mt-3">
                Tổng tiền sản phẩm: <span id="base-total"><?php echo number_format($total_amount_to_pay); ?></span> VNĐ<br>
                Phí vận chuyển: <span id="shipping-fee">0</span> VNĐ<br>
                Tổng tiền đơn hàng: <span id="final-total"><?php echo number_format($total_amount_to_pay); ?></span> VNĐ
            </h3>

            <button type="submit" name="place_order">Đặt hàng</button>
        </form>

        <h3 class="text-center my-3">Sản phẩm trong đơn hàng</h3>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Tên sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($checkout_items as $item_key => $item): ?>
                    <tr>
                        <td><img src="uploads/<?php echo htmlspecialchars($item['image'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-image"></td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo number_format($item['price']); ?> VNĐ</td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo number_format($item['price'] * $item['quantity']); ?> VNĐ</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const shippingRadios = document.querySelectorAll('input[name="id_vanchuyen"]');
        const paymentRadios = document.querySelectorAll('input[name="id_thanhtoan"]');
        const baseTotalSpan = document.getElementById('base-total');
        const shippingFeeSpan = document.getElementById('shipping-fee');
        const finalTotalSpan = document.getElementById('final-total');
        const paymentInfoDisplay = document.getElementById('payment-info-display');

        const baseTotal = <?php echo $total_amount_to_pay; ?>; // Tổng tiền sản phẩm ban đầu

        function updateFinalTotal() {
            let selectedShipFee = 0;
            shippingRadios.forEach(radio => {
                if (radio.checked) {
                    selectedShipFee = parseInt(radio.dataset.shipFee);
                }
            });
            shippingFeeSpan.textContent = selectedShipFee.toLocaleString('vi-VN');
            const newTotal = baseTotal + selectedShipFee;
            finalTotalSpan.textContent = newTotal.toLocaleString('vi-VN');
        }

        function updatePaymentInfo() {
            let selectedPaymentInfo = '';
            paymentRadios.forEach(radio => {
                if (radio.checked) {
                    selectedPaymentInfo = radio.dataset.info;
                }
            });
            paymentInfoDisplay.textContent = selectedPaymentInfo;
        }

        // Gắn sự kiện change cho cả 2 loại radio button
        shippingRadios.forEach(radio => {
            radio.addEventListener('change', updateFinalTotal);
        });

        paymentRadios.forEach(radio => {
            radio.addEventListener('change', updatePaymentInfo);
        });

        // Cập nhật tổng tiền và thông tin thanh toán ban đầu khi tải trang
        updateFinalTotal();
        updatePaymentInfo();
    });
</script>

<?php include_once "includes/footer.php"; ?>