<?php
session_start(); // Khởi động session cho người dùng
require_once __DIR__ . '/../config/connect.php'; // Đường dẫn tương đối đến file connect.php

// Lấy danh mục sản phẩm để hiển thị trên menu
$sql_categories = "SELECT * FROM danh_muc WHERE trangthai = 0";
$query_categories = mysqli_query($conn, $sql_categories);

// Lấy nhãn hiệu sản phẩm để hiển thị trên menu
$sql_brands = "SELECT * FROM nhan_hieu WHERE trangthai = 0";
$query_brands = mysqli_query($conn, $sql_brands);


$cart_item_count = 0;
if (isset($_SESSION['user_id'])) { 
    $user_id = $_SESSION['user_id'];
    $sql_cart_count = "SELECT SUM(ghct.soluong) AS total_items
                       FROM gio_hang gh
                       INNER JOIN giohang_chitiet ghct ON gh.id_gio = ghct.id_gio
                       WHERE gh.id_khach = $user_id";
    $query_cart_count = mysqli_query($conn, $sql_cart_count);
    $row_cart_count = mysqli_fetch_assoc($query_cart_count);
    if ($row_cart_count['total_items']) {
        $cart_item_count = $row_cart_count['total_items'];
    } else { 
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $cart_item_count += $item['quantity'];
            }
        }
    }
} else if (isset($_SESSION['cart'])) { 
    foreach ($_SESSION['cart'] as $item) {
        $cart_item_count += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Đồ Chơi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="css/frontend.css"> </head>
<body>
    <header class="main-header">
        <div class="container header-content">
            <div class="logo">
                <a href="index.php">Web Đồ Chơi</a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Trang chủ</a></li>
                    <li class="dropdown">
                        <a href="product.php?category=all">Sản phẩm <i class="fas fa-caret-down"></i></a>
                        <div class="dropdown-content">
                            <?php mysqli_data_seek($query_categories, 0); // Reset con trỏ
                            while($category = mysqli_fetch_assoc($query_categories)) : ?>
                                <a href="product.php?category_id=<?php echo $category['id_danhmuc']; ?>"><?php echo htmlspecialchars($category['ten_danhmuc']); ?></a>
                            <?php endwhile; ?>
                        </div>
                    </li>
                    <li class="dropdown">
                        <a href="#">Thương hiệu <i class="fas fa-caret-down"></i></a>
                        <div class="dropdown-content">
                            <?php mysqli_data_seek($query_brands, 0); // Reset con trỏ
                            while($brand = mysqli_fetch_assoc($query_brands)) : ?>
                                <a href="product.php?brand_id=<?php echo $brand['id_nhan']; ?>"><?php echo htmlspecialchars($brand['ten_nhanhieu']); ?></a>
                            <?php endwhile; ?>
                        
                        </div>
                    </li>
                    <li><a href="contact.php">Liên hệ</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <form action="product.php" method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
                <div class="cart-icon">
                    <a href="cart.php">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-badge"><?php echo $cart_item_count; ?></span>
                    </a>
                </div>
                <div class="user-actions">
                    <?php if (isset($_SESSION['user_name'])): ?>
                        <div class="dropdown user-dropdown">
                            <span class="user-name-display">Chào, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! <i class="fas fa-caret-down"></i></span>
                            <div class="dropdown-content">
                                <a href="profile.php">Thông tin tài khoản</a>
                                <a href="order-history.php">Đơn hàng của tôi</a>
                                <a href="logout.php">Đăng xuất</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline">Đăng nhập</a>
                        <a href="register.php" class="btn btn-primary">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <main class="main-content">
