<?php
session_start();
require_once __DIR__ . '/config/connect.php'; // Đảm bảo đường dẫn đúng

header('Content-Type: application/json'); // Trả về JSON

$response = [
    'success' => false,
    'message' => 'Lỗi không xác định.',
    'cart_item_count' => 0
];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $id_spchitiet = (int)($_POST['id_spchitiet'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($action === 'add' && $id_spchitiet > 0 && $quantity > 0) {
        // Lấy thông tin chi tiết sản phẩm (biến thể) để kiểm tra tồn kho và giá
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

            if ($variant_info) {
                $available_stock = $variant_info['so_luong'];
                $price_per_item = $variant_info['gia_sale'] ?? $variant_info['gia_goc'];
                $item_key = (string)$id_spchitiet;

                $current_quantity_in_cart = $_SESSION['cart'][$item_key]['quantity'] ?? 0;
                $new_total_quantity = $current_quantity_in_cart + $quantity;

                if ($new_total_quantity > $available_stock) {
                    $response['message'] = 'Số lượng yêu cầu vượt quá số lượng tồn kho (' . $available_stock . ').';
                } else {
                    if (isset($_SESSION['cart'][$item_key])) {
                        $_SESSION['cart'][$item_key]['quantity'] += $quantity;
                    } else {
                        $_SESSION['cart'][$item_key] = [
                            'product_id' => $variant_info['id_sp'],
                            'id_spchitiet' => $variant_info['id_spchitiet'],
                            'name' => $variant_info['ten_sp'],
                            'image' => $variant_info['main_image'],
                            'price' => $price_per_item,
                            'quantity' => $quantity,
                            'stock' => $available_stock
                        ];
                    }
                    $response['success'] = true;
                    $response['message'] = 'Đã thêm sản phẩm vào giỏ hàng!';
                }
            } else {
                $response['message'] = 'Sản phẩm chi tiết không tồn tại hoặc không khả dụng.';
            }
        } else {
            $response['message'] = 'Lỗi chuẩn bị truy vấn thông tin sản phẩm.';
        }
    } else {
        $response['message'] = 'Dữ liệu thêm vào giỏ hàng không hợp lệ.';
    }
} else {
    $response['message'] = 'Yêu cầu không hợp lệ.';
}

// Cập nhật số lượng sản phẩm trong giỏ để gửi về client
$total_cart_items = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_cart_items += $item['quantity'];
}
$response['cart_item_count'] = $total_cart_items;

echo json_encode($response);

// Đóng kết nối CSDL
if (isset($conn) && is_object($conn) && get_class($conn) === 'mysqli' && mysqli_thread_id($conn)) {
    mysqli_close($conn);
}
?>