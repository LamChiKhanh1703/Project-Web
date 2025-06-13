<?php
include_once "includes/header.php";

$message = '';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $id_spchitiet = (int)($_POST['id_spchitiet'] ?? 0); 
    $quantity = (int)($_POST['quantity'] ?? 1); 

    
    $sql_variant_info = "SELECT spct.id_spchitiet, spct.so_luong, spct.gia_goc, spct.gia_sale, sp.ten_sp, ha.ten_file AS main_image, sp.id_sp AS product_id_parent
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

        if (!$variant_info) {
            $message = '<div class="alert-error">Sản phẩm chi tiết không tồn tại hoặc không khả dụng.</div>';
        } else {
            $available_stock = $variant_info['so_luong'];
            $price_per_item = $variant_info['gia_sale'] ?? $variant_info['gia_goc'];

            $item_key = (string)$id_spchitiet; // Dùng id_spchitiet làm key cho giỏ hàng, đảm bảo là string

            switch ($action) {
                case 'add':
                    if ($quantity <= 0) {
                        $message = '<div class="alert-error">Số lượng không hợp lệ.</div>';
                        break;
                    }
                    // Tính tổng số lượng hiện tại trong giỏ + số lượng muốn thêm
                    $current_quantity_in_cart = $_SESSION['cart'][$item_key]['quantity'] ?? 0;
                    $new_total_quantity = $current_quantity_in_cart + $quantity;

                    if ($new_total_quantity > $available_stock) {
                        $message = '<div class="alert-error">Số lượng yêu cầu vượt quá số lượng tồn kho (' . $available_stock . ').</div>';
                    } else {
                        if (isset($_SESSION['cart'][$item_key])) {
                            $_SESSION['cart'][$item_key]['quantity'] += $quantity;
                        } else {
                            $_SESSION['cart'][$item_key] = [
                                'product_id' => $variant_info['product_id_parent'],
                                'id_spchitiet' => $variant_info['id_spchitiet'],
                                'name' => $variant_info['ten_sp'],
                                'image' => $variant_info['main_image'],
                                'price' => $price_per_item,
                                'quantity' => $quantity,
                                'stock' => $available_stock
                            ];
                        }
                        $message = '<div class="alert-success">Đã thêm sản phẩm vào giỏ hàng.</div>';
                    }
                    break;

                case 'update':
                    if (isset($_SESSION['cart'][$item_key])) {
                        if ($quantity <= 0) {
                            unset($_SESSION['cart'][$item_key]); // Xóa nếu số lượng bằng 0
                            $message = '<div class="alert-success">Đã xóa sản phẩm khỏi giỏ hàng.</div>';
                        } else if ($quantity > $available_stock) {
                            $_SESSION['cart'][$item_key]['quantity'] = $available_stock; // Cập nhật bằng số lượng tồn kho
                            $message = '<div class="alert-error">Số lượng yêu cầu vượt quá số lượng tồn kho (' . $available_stock . '). Số lượng đã được điều chỉnh.</div>';
                        } else {
                            $_SESSION['cart'][$item_key]['quantity'] = $quantity;
                            $message = '<div class="alert-success">Số lượng đã được cập nhật.</div>';
                        }
                    }
                    break;

                case 'delete':
                    if (isset($_SESSION['cart'][$item_key])) {
                        unset($_SESSION['cart'][$item_key]);
                        $message = '<div class="alert-success">Đã xóa sản phẩm khỏi giỏ hàng.</div>';
                    }
                    break;
            }
        }
    } else {
        $message = '<div class="alert-error">Lỗi khi chuẩn bị truy vấn thông tin biến thể.</div>';
    }
    
   
}

// Lấy thông báo từ URL sau khi redirect (chỉ hoạt động nếu có redirect từ trang khác)
if (isset($_GET['msg'])) {
   $message = urldecode($_GET['msg']);
}

$total_cart_amount = 0;
?>

<div class="container">
    <h2 class="text-center my-3">Giỏ hàng của bạn</h2>

    <?php echo $message; // Hiển thị thông báo ?>

    <?php if (empty($_SESSION['cart'])): ?>
        <p class="text-center">Giỏ hàng của bạn đang trống. <a href="product.php">Mua sắm ngay!</a></p>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Tên sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Tổng tiền</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $item_key => $item): ?>
                    <?php
                    $subtotal = $item['price'] * $item['quantity'];
                    $total_cart_amount += $subtotal;
                    ?>
                    <tr>
                        <td><img src="uploads/<?php echo htmlspecialchars($item['image'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-image"></td>
                        <td><a href="product-detail.php?id=<?php echo $item['product_id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a></td>
                        <td><?php echo number_format($item['price']); ?> VNĐ</td>
                        <td>
                            <form action="cart.php" method="POST" class="quantity-update-form">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id_spchitiet" value="<?php echo $item['id_spchitiet']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" class="quantity-input">
                                <button type="submit" style="display:none;">Cập nhật</button> </form>
                        </td>
                        <td><?php echo number_format($subtotal); ?> VNĐ</td>
                        <td>
                            <form action="cart.php" method="POST">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_spchitiet" value="<?php echo $item['id_spchitiet']; ?>">
                                <button type="submit" class="cart-actions">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <h3>Tổng tiền giỏ hàng:</h3>
            <p><?php echo number_format($total_cart_amount); ?> VNĐ</p>
            <a href="checkout.php" class="checkout-btn">Tiến hành đặt hàng</a>
        </div>
    <?php endif; ?>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInputs = document.querySelectorAll('.quantity-input');
        quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                this.closest('form').submit(); // Gửi form khi số lượng thay đổi
            });
        });
    });
</script>

<?php include_once "includes/footer.php"; ?>