</main>
    <footer class="main-footer">
        <div class="container footer-content">
            <div class="footer-section about">
                <h3>Về chúng tôi</h3>
                <p>Web Đồ Chơi cung cấp những sản phẩm đồ chơi chất lượng cao, an toàn và giáo dục cho trẻ em.</p>
            </div>
            <div class="footer-section links">
                <h3>Liên kết nhanh</h3>
                <ul>
                    <li><a href="index.php">Trang chủ</a></li>
                    <li><a href="product.php">Sản phẩm</a></li>
                    <li><a href="contact.php">Liên hệ</a></li>
                    <li><a href="#">Chính sách bảo mật</a></li>
                </ul>
            </div>
            <div class="footer-section contact-info">
                <h3>Liên hệ</h3>
                <?php
                // Lấy thông tin liên hệ từ CSDL
                $sql_contact = "SELECT * FROM lien_he LIMIT 1";
                $query_contact = mysqli_query($conn, $sql_contact);
                $contact_info = mysqli_fetch_assoc($query_contact);
                if ($contact_info) {
                    echo "<p><i class='fas fa-map-marker-alt'></i> " . htmlspecialchars($contact_info['diachi']) . "</p>";
                    echo "<p><i class='fas fa-phone-alt'></i> " . htmlspecialchars($contact_info['hotline']) . "</p>";
                    echo "<p><i class='fas fa-envelope'></i> " . htmlspecialchars($contact_info['email']) . "</p>";
                }
                ?>
            </div>
            <div class="footer-section social-media">
                <h3>Theo dõi chúng tôi</h3>
                <a href="<?php echo htmlspecialchars($contact_info['link_facebook'] ?? '#'); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="<?php echo htmlspecialchars($contact_info['link_youtube'] ?? '#'); ?>" target="_blank"><i class="fab fa-youtube"></i></a>
                <a href="<?php echo htmlspecialchars($contact_info['link_tiktok'] ?? '#'); ?>" target="_blank"><i class="fab fa-tiktok"></i></a>
                <a href="<?php echo htmlspecialchars($contact_info['link_instagram'] ?? '#'); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> Web Đồ Chơi. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cartBadge = document.querySelector('.cart-badge'); // Chọn badge giỏ hàng

            // Xử lý nút "Thêm vào giỏ" nhanh trên trang chủ/sản phẩm (AJAX)
            document.querySelectorAll('.add-to-cart-quick-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const idSpChiTiet = this.dataset.idspchitiet;
                    const quantity = parseInt(this.dataset.quantity);

                    if (!idSpChiTiet || idSpChiTiet === '0' || quantity <= 0) {
                        alert('Không thể thêm sản phẩm vào giỏ hàng: Sản phẩm không có biến thể hợp lệ hoặc số lượng không hợp lệ.');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('action', 'add');
                    formData.append('id_spchitiet', idSpChiTiet);
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
                        alert('Đã xảy ra lỗi khi thêm sản phẩm vào giỏ hàng. Vui lòng kiểm tra console để biết chi tiết.');
                    });
                });
            });

            // Xử lý nút "Mua ngay" nhanh trên trang chủ/sản phẩm
            document.querySelectorAll('.buy-now-quick-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const idSpChiTiet = this.dataset.idspchitiet;
                    const quantity = parseInt(this.dataset.quantity);

                    if (!idSpChiTiet || idSpChiTiet === '0' || quantity <= 0) {
                        alert('Vui lòng chọn sản phẩm và số lượng hợp lệ để mua ngay.');
                        return;
                    }

                    // Chuyển hướng đến trang checkout.php với tham số mua ngay
                    window.location.href = `checkout.php?action=buy_now&id_spchitiet=${idSpChiTiet}&quantity=${quantity}`;
                });
            });

            // Logic cho các nút "Thêm vào giỏ" và "Mua ngay" trong bảng biến thể trên trang chi tiết (nếu có)
            document.querySelectorAll('.add-to-cart-detail-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const idSpChiTiet = this.dataset.idspchitiet;
                    const quantityInput = this.closest('tr').querySelector('.quantity-input-detail');
                    const quantity = parseInt(quantityInput.value);

                    if (!idSpChiTiet || idSpChiTiet === '0' || quantity <= 0) {
                        alert('Vui lòng chọn biến thể và số lượng hợp lệ để thêm vào giỏ hàng.');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('action', 'add');
                    formData.append('id_spchitiet', idSpChiTiet);
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

            document.querySelectorAll('.buy-now-detail-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const idSpChiTiet = this.dataset.idspchitiet;
                    const quantityInput = this.closest('tr').querySelector('.quantity-input-detail');
                    const quantity = parseInt(quantityInput.value);

                    if (!idSpChiTiet || idSpChiTiet === '0' || quantity <= 0) {
                        alert('Vui lòng chọn biến thể và số lượng hợp lệ để mua ngay.');
                        return;
                    }

                    window.location.href = `checkout.php?action=buy_now&id_spchitiet=${idSpChiTiet}&quantity=${quantity}`;
                });
            });

            document.querySelectorAll('.quantity-input-detail').forEach(input => {
                input.addEventListener('input', function() {
                    const stock = parseInt(this.dataset.stock);
                    if (parseInt(this.value) > stock) {
                        this.value = stock;
                        alert('Số lượng không thể vượt quá số lượng tồn kho: ' + stock);
                    }
                    if (parseInt(this.value) < this.min) {
                        this.value = this.min;
                    }
                });
            });

        });
    </script>
</body>
</html>
<?php
// Đây là phần đóng kết nối CSDL và các thẻ HTML cuối cùng.
// Đảm bảo không có gì khác sau khối PHP này.
if (isset($conn) && is_object($conn) && get_class($conn) === 'mysqli') {
    if (mysqli_thread_id($conn)) {
        mysqli_close($conn);
    }
}
?>