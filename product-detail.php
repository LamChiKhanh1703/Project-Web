<?php include_once "includes/header.php"; ?>

<div class="container">
    <?php
    $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $message = ''; // Khởi tạo biến message

    if ($product_id > 0) {
        $sql_product = "SELECT sp.*, nh.ten_nhanhieu, ds.ten_dong, dm.ten_danhmuc
                        FROM san_pham sp
                        INNER JOIN nhan_hieu nh ON nh.id_nhan = sp.id_nhanhieu
                        INNER JOIN dong_sanpham ds ON ds.id_dong = sp.id_dong
                        INNER JOIN danh_muc dm ON dm.id_danhmuc = sp.id_danhmuc
                        WHERE sp.id_sp = ? AND sp.trangthai = 0";
        $stmt_product = mysqli_prepare($conn, $sql_product);
        
        if ($stmt_product) {
            mysqli_stmt_bind_param($stmt_product, 'i', $product_id);
            mysqli_stmt_execute($stmt_product);
            $result_product = mysqli_stmt_get_result($stmt_product);
            $product = mysqli_fetch_assoc($result_product);
            mysqli_stmt_close($stmt_product);

            if ($product) {
                // Lấy ảnh đại diện và ảnh con
                $sql_images = "SELECT ten_file, trang_thai FROM hinh_anh WHERE id_sp = ? ORDER BY trang_thai ASC";
                $stmt_images = mysqli_prepare($conn, $sql_images);
                if ($stmt_images) {
                    mysqli_stmt_bind_param($stmt_images, 'i', $product_id);
                    mysqli_stmt_execute($stmt_images);
                    $result_images = mysqli_stmt_get_result($stmt_images);
                    $main_image = 'default.jpg'; // Ảnh mặc định
                    $sub_images = [];
                    while ($img = mysqli_fetch_assoc($result_images)) {
                        if ($img['trang_thai'] == 0) {
                            $main_image = $img['ten_file'];
                        } else {
                            $sub_images[] = $img['ten_file'];
                        }
                    }
                    mysqli_stmt_close($stmt_images);
                } else {
                    $message .= '<div class="alert-error">Lỗi chuẩn bị truy vấn ảnh sản phẩm: ' . mysqli_error($conn) . '</div>';
                }

                // Lấy các biến thể sản phẩm
                $sql_variants = "SELECT id_spchitiet, sku, so_luong, gia_goc, gia_sale, id_khuyenmai, trang_thai
                                 FROM sanpham_chitiet
                                 WHERE id_sp = ? AND trang_thai = 0
                                 ORDER BY gia_sale ASC, gia_goc ASC";
                $stmt_variants = mysqli_prepare($conn, $sql_variants);
                if ($stmt_variants) {
                    mysqli_stmt_bind_param($stmt_variants, 'i', $product_id);
                    mysqli_stmt_execute($stmt_variants);
                    $result_variants = mysqli_stmt_get_result($stmt_variants);
                    $variants = mysqli_fetch_all($result_variants, MYSQLI_ASSOC);
                    mysqli_stmt_close($stmt_variants);
                } else {
                    $message .= '<div class="alert-error">Lỗi chuẩn bị truy vấn biến thể sản phẩm: ' . mysqli_error($conn) . '</div>';
                    $variants = []; // Đảm bảo $variants là mảng rỗng nếu có lỗi
                }

                $display_price_original = null;
                $display_price_sale = null;
                $display_price_current = 'Liên hệ'; // Giá mặc định nếu không có biến thể

                $selected_variant_id = null; // Khởi tạo biến này
                $lowest_price_variant = null; // Khởi tạo biến này

                if (!empty($variants)) {
                    $lowest_price_variant = $variants[0]; // Biến thể có giá thấp nhất
                    $display_price_original = $lowest_price_variant['gia_goc'];
                    $display_price_sale = $lowest_price_variant['gia_sale'];

                    if ($display_price_sale !== null && $display_price_sale < $display_price_original) {
                        $display_price_current = number_format($display_price_sale) . ' VNĐ';
                    } else {
                        $display_price_current = number_format($display_price_original) . ' VNĐ';
                    }
                    $selected_variant_id = $lowest_price_variant['id_spchitiet'];
                }
        ?>
                <div class="product-detail-container">
                    <div class="product-detail-image">
                        <img src="uploads/<?php echo htmlspecialchars($main_image); ?>" alt="<?php echo htmlspecialchars($product['ten_sp']); ?>">
                        <?php if (!empty($sub_images)): ?>
                            <div class="image-gallery my-3">
                                <?php foreach ($sub_images as $sub_img_file): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($sub_img_file); ?>" alt="Ảnh con" width="80" height="80">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="product-detail-info">
                        <h1><?php echo htmlspecialchars($product['ten_sp']); ?></h1>
                        <p class="brand-series">Thương hiệu: <?php echo htmlspecialchars($product['ten_nhanhieu']); ?> | Dòng sản phẩm: <?php echo htmlspecialchars($product['ten_dong']); ?></p>
                        <p class="price">
                            <?php if ($display_price_sale !== null && $display_price_sale < $display_price_original): ?>
                                <span class="original-price"><?php echo number_format($display_price_original); ?> VNĐ</span>
                                <span class="sale-price"><?php echo number_format($display_price_sale); ?> VNĐ</span>
                            <?php else: ?>
                                <?php echo $display_price_current; ?>
                            <?php endif; ?>
                        </p>

                        <p class="description"><strong>Mô tả ngắn:</strong> <?php echo nl2br(htmlspecialchars($product['mo_ta_ngan'])); ?></p>
                        <p class="description"><strong>Mô tả chi tiết:</strong> <?php echo nl2br(htmlspecialchars($product['mo_ta_chi_tiet'])); ?></p>

                        <div class="form-group mb-3">
                            <label for="variant_select">Chọn biến thể:</label>
                            <select name="id_spchitiet" id="variant_select" class="form-control">
                                <?php if (!empty($variants)): ?>
                                    <?php foreach ($variants as $variant_item): ?>
                                        <option value="<?php echo htmlspecialchars($variant_item['id_spchitiet']); ?>"
                                                data-price="<?php echo htmlspecialchars($variant_item['gia_sale'] ?? $variant_item['gia_goc']); ?>"
                                                data-stock="<?php echo htmlspecialchars($variant_item['so_luong']); ?>"
                                                <?php echo ($variant_item['id_spchitiet'] == $selected_variant_id) ? 'selected' : ''; ?>>
                                            SKU: <?php echo htmlspecialchars($variant_item['sku']); ?>
                                            (Giá: <?php echo number_format($variant_item['gia_sale'] ?? $variant_item['gia_goc']); ?> VNĐ)
                                            <?php echo ($variant_item['so_luong'] > 0) ? '(Còn: ' . $variant_item['so_luong'] . ' sản phẩm)' : '(Hết hàng)'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">Không có biến thể nào</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="quantity-selector">
                            <label for="quantity">Số lượng:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="100" required>
                        </div>
                        <div class="product-detail-actions">
                            <button type="button" class="btn btn-primary add-to-cart-detail-btn" <?php echo empty($variants) || ($lowest_price_variant['so_luong'] ?? 0) <= 0 ? 'disabled' : ''; ?>>
                                Thêm vào giỏ hàng
                            </button>
                            <button type="button" class="btn btn-outline-success buy-now-detail-btn" <?php echo empty($variants) || ($lowest_price_variant['so_luong'] ?? 0) <= 0 ? 'disabled' : ''; ?>>
                                Mua ngay
                            </button>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const variantSelect = document.getElementById('variant_select');
                        const quantityInput = document.getElementById('quantity');
                        const addToCartBtn = document.querySelector('.product-detail-actions .add-to-cart-detail-btn');
                        const buyNowBtn = document.querySelector('.product-detail-actions .buy-now-detail-btn');

                        // Hàm này chỉ chạy nếu có biến thể được chọn
                        function updateQuantityAndButtons() {
                            if (variantSelect.options.length === 0 || variantSelect.selectedIndex === -1) {
                                // Không có biến thể nào được chọn hoặc dropdown rỗng
                                quantityInput.value = 0;
                                quantityInput.max = 0;
                                addToCartBtn.disabled = true;
                                buyNowBtn.disabled = true;
                                addToCartBtn.textContent = "Hết hàng";
                                buyNowBtn.textContent = "Hết hàng";
                                return;
                            }

                            const selectedOption = variantSelect.options[variantSelect.selectedIndex];
                            const stock = parseInt(selectedOption.dataset.stock);

                            quantityInput.max = stock;
                            if (parseInt(quantityInput.value) > stock) {
                                quantityInput.value = stock > 0 ? stock : 1;
                            }
                            if (parseInt(quantityInput.value) < quantityInput.min) {
                                quantityInput.value = quantityInput.min;
                            }

                            if (stock <= 0) {
                                quantityInput.value = 0;
                                addToCartBtn.disabled = true;
                                buyNowBtn.disabled = true;
                                addToCartBtn.textContent = "Hết hàng";
                                buyNowBtn.textContent = "Hết hàng";
                            } else {
                                addToCartBtn.disabled = false;
                                buyNowBtn.disabled = false;
                                addToCartBtn.textContent = "Thêm vào giỏ hàng";
                                buyNowBtn.textContent = "Mua ngay";
                            }
                        }

                        variantSelect.addEventListener('change', updateQuantityAndButtons);
                        // Cập nhật ban đầu khi tải trang
                        updateQuantityAndButtons();

                        // Xử lý nút Mua ngay
                        buyNowBtn.addEventListener('click', function() {
                            const selectedVariantId = variantSelect.value;
                            const quantity = quantityInput.value;
                            if (selectedVariantId && quantity > 0) {
                                window.location.href = `checkout.php?action=buy_now&id_spchitiet=${selectedVariantId}&quantity=${quantity}`;
                            } else {
                                alert("Vui lòng chọn biến thể và số lượng hợp lệ.");
                            }
                        });

                        // Xử lý nút Thêm vào giỏ hàng (AJAX)
                        addToCartBtn.addEventListener('click', function() {
                            const selectedVariantId = variantSelect.value;
                            const quantity = quantityInput.value;
                            const cartBadge = document.querySelector('.cart-badge'); // Lấy badge giỏ hàng

                            if (!selectedVariantId || quantity <= 0) {
                                alert('Vui lòng chọn biến thể và số lượng hợp lệ.');
                                return;
                            }

                            const formData = new FormData();
                            formData.append('action', 'add');
                            formData.append('id_spchitiet', selectedVariantId);
                            formData.append('quantity', quantity);

                            fetch('add-to-cart-ajax.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok ' + response.statusText);
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    alert(data.message);
                                    if (cartBadge) {
                                        cartBadge.textContent = data.cart_item_count;
                                    }
                                } else {
                                    alert('Lỗi: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Lỗi AJAX:', error);
                                alert('Đã xảy ra lỗi khi thêm sản phẩm vào giỏ hàng. Vui lòng kiểm tra console.');
                            });
                        });
                    });
                </script>
        <?php
            } else {
                echo "<p class='text-center'>Không tìm thấy sản phẩm này hoặc sản phẩm không khả dụng.</p>";
            }
        } else {
            echo "<p class='text-center'>Lỗi chuẩn bị truy vấn sản phẩm: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p class='text-center'>ID sản phẩm không hợp lệ.</p>";
    }
    ?>
</div>

<?php include_once "includes/footer.php"; ?>