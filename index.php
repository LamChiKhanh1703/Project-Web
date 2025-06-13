<?php include_once "includes/header.php"; ?>

<div class="container">
    

    <h2 class="text-center py-5">Sản phẩm nổi bật</h2>
    <div class="product-grid">
        <?php
      
        $sql_featured_products = "SELECT sp.*, ha.ten_file AS main_image
                                  FROM san_pham sp
                                  LEFT JOIN hinh_anh ha ON sp.id_sp = ha.id_sp AND ha.trang_thai = 0
                                  WHERE sp.noi_bat = 1 AND sp.trangthai = 0
                                  LIMIT 8"; // Lấy 8 sản phẩm nổi bật
        $query_featured_products = mysqli_query($conn, $sql_featured_products);

        if (mysqli_num_rows($query_featured_products) > 0) {
            while ($product = mysqli_fetch_assoc($query_featured_products)) {
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
        } else {
            echo "<p class='text-center'>Không có sản phẩm nổi bật nào.</p>";
        }
        ?>
    </div>

    <h2 class="text-center py-5">Sản phẩm mới nhất</h2>
    <div class="product-grid">
        <?php
        // Lấy sản phẩm mới nhất
        $sql_latest_products = "SELECT sp.*, ha.ten_file AS main_image
                                FROM san_pham sp
                                LEFT JOIN hinh_anh ha ON sp.id_sp = ha.id_sp AND ha.trang_thai = 0
                                WHERE sp.trangthai = 0
                                ORDER BY sp.ngay_tao DESC LIMIT 8";
        $query_latest_products = mysqli_query($conn, $sql_latest_products);

        if (mysqli_num_rows($query_latest_products) > 0) {
            while ($product = mysqli_fetch_assoc($query_latest_products)) {
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
        } else {
            echo "<p class='text-center'>Không có sản phẩm mới nào.</p>";
        }
        ?>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>