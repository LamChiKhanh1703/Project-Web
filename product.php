<?php include_once "includes/header.php"; ?>

<div class="container">
    <h2 class="text-center my-3">Tất cả sản phẩm</h2>
    <?php
    $sql_products = "SELECT sp.*, ha.ten_file AS main_image, nh.ten_nhanhieu, dm.ten_danhmuc
                     FROM san_pham sp
                     LEFT JOIN hinh_anh ha ON sp.id_sp = ha.id_sp AND ha.trang_thai = 0
                     INNER JOIN nhan_hieu nh ON sp.id_nhanhieu = nh.id_nhan
                     INNER JOIN danh_muc dm ON sp.id_danhmuc = dm.id_danhmuc
                     WHERE sp.trangthai = 0";

    $conditions = [];
    $params = [];
    $param_types = '';

    // Tìm kiếm theo tên sản phẩm
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search_term = '%' . $_GET['search'] . '%';
        $conditions[] = "(sp.ten_sp LIKE ? OR sp.mo_ta_ngan LIKE ? OR sp.mo_ta_chi_tiet LIKE ?)";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $param_types .= 'sss';
    }

    // Lọc theo danh mục
    if (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
        $category_id = $_GET['category_id'];
        $conditions[] = "sp.id_danhmuc = ?";
        $params[] = $category_id;
        $param_types .= 'i';
    }

    // Lọc theo thương hiệu
    if (isset($_GET['brand_id']) && is_numeric($_GET['brand_id'])) {
        $brand_id = $_GET['brand_id'];
        $conditions[] = "sp.id_nhanhieu = ?";
        $params[] = $brand_id;
        $param_types .= 'i';
    }

    if (!empty($conditions)) {
        $sql_products .= " AND " . implode(" AND ", $conditions);
    }

    $sql_products .= " ORDER BY sp.ngay_tao DESC";

    $stmt = mysqli_prepare($conn, $sql_products);

    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $param_types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $query_products = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($query_products) > 0) {
    ?>
        <div class="product-grid">
            <?php
            while ($product = mysqli_fetch_assoc($query_products)) {
                // Lấy giá thấp nhất từ sanpham_chitiet
                $sql_min_price = "SELECT id_spchitiet, MIN(gia_sale) AS min_price_sale, MIN(gia_goc) AS min_price_goc, so_luong
                                  FROM sanpham_chitiet
                                  WHERE id_sp = " . $product['id_sp'] . " AND trang_thai = 0";
                $query_min_price = mysqli_query($conn, $sql_min_price);
                $prices_and_spct = mysqli_fetch_assoc($query_min_price);
                
                $display_price_original = $prices_and_spct['min_price_goc'];
                $display_price_sale = $prices_and_spct['min_price_sale'];
                
                $default_id_spchitiet = $prices_and_spct['id_spchitiet'] ?? 0;
                $default_so_luong = $prices_and_spct['so_luong'] ?? 0;
                $is_available = ($default_so_luong > 0);
            ?>
                <div class="product-card">
                    <div class="product-image">
                        <a href="product-detail.php?id=<?php echo $product['id_sp']; ?>">
                            <img src="uploads/<?php echo htmlspecialchars($product['main_image'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($product['ten_sp']); ?>">
                        </a>
                    </div>
                    <div class="product-info">
                        <h3><a href="product-detail.php?id=<?php echo $product['id_sp']; ?>"><?php echo htmlspecialchars($product['ten_sp']); ?></a></h3>
                        <p class="product-brand">Thương hiệu: <?php echo htmlspecialchars($product['ten_nhanhieu']); ?></p>
                        <p class="product-category">Danh mục: <?php echo htmlspecialchars($product['ten_danhmuc']); ?></p>
                        <p class="product-price">
                            <?php if ($display_price_sale !== null && $display_price_sale < $display_price_original): ?>
                                <span class="original-price"><?php echo number_format($display_price_original); ?> VNĐ</span>
                                <span class="sale-price"><?php echo number_format($display_price_sale); ?> VNĐ</span>
                            <?php else: ?>
                                <?php echo number_format($display_price_original); ?> VNĐ
                            <?php endif; ?>
                        </p>
                        
                        <div class="product-actions-group">
                            <button type="button" class="btn btn-sm btn-primary add-to-cart-quick-btn"
                                data-idspchitiet="<?php echo $default_id_spchitiet; ?>"
                                data-quantity="1"
                                <?php echo (!$is_available ? 'disabled' : ''); ?>>
                                <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success buy-now-quick-btn"
                                data-idspchitiet="<?php echo $default_id_spchitiet; ?>"
                                data-quantity="1"
                                <?php echo (!$is_available ? 'disabled' : ''); ?>>
                                <i class="fas fa-money-bill-wave"></i> Mua ngay
                            </button>
                        </div>
                        <?php if (!$is_available): ?>
                            <p class="text-danger mt-2"><small>Hết hàng / Chưa có biến thể</small></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    <?php
    } else {
        echo "<p class='text-center'>Không tìm thấy sản phẩm nào phù hợp.</p>";
    }
    ?>
</div>

<?php include_once "includes/footer.php"; ?>