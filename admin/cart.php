<?php

  
    include_once "sidebar.php"; 

    require_once "connect.php"; 

    // Khởi tạo các biến cần thiết
    $carts_data = [];
    $errorMessage = ''; // Biến để lưu trữ thông báo lỗi

    // Bước 1: Kiểm tra biến $conn từ connect.php
    if (!isset($conn) || !$conn || !($conn instanceof mysqli)) {
        $errorMessage = "LỖI NGHIÊM TRỌNG: Kết nối cơ sở dữ liệu không thành công hoặc biến \$conn không hợp lệ. Vui lòng kiểm tra file 'connect.php'.";
    } else {
        // Bước 2: Nếu kết nối ổn, thực hiện truy vấn SQL
        $sql = "SELECT gh.id_gio, kh.hoten AS customer_name, kh.sodienthoai AS customer_phone, gh.ngaycapnhat,
                       sp.ten_sp, ghct.soluong, spct.gia_ban
                FROM gio_hang gh
                INNER JOIN khach_hang kh ON kh.id_khach = gh.id_khach
                INNER JOIN giohang_chitiet ghct ON ghct.id_gio = gh.id_gio
                INNER JOIN sanpham_chitiet spct ON spct.id_spchitiet = ghct.id_spchitiet
                INNER JOIN san_pham sp ON sp.id_sp = spct.id_sp
                ORDER BY gh.ngaycapnhat DESC, gh.id_gio ASC";
        
        $query = mysqli_query($conn, $sql);

        // Bước 3: Kiểm tra kết quả truy vấn
        if (!$query) {
            $errorMessage = "Lỗi truy vấn SQL: " . mysqli_error($conn) . "<br>Câu lệnh SQL: " . htmlspecialchars($sql);
        } else {
            // Bước 4: Xử lý dữ liệu nếu truy vấn thành công
            if (mysqli_num_rows($query) > 0) {
                while ($row = mysqli_fetch_assoc($query)) {
                    $cart_id = $row['id_gio'];
                    if (!isset($carts_data[$cart_id])) {
                        $carts_data[$cart_id] = [
                            'customer_name' => $row['customer_name'],
                            'customer_phone' => $row['customer_phone'],
                            'ngaycapnhat' => $row['ngaycapnhat'],
                            'items' => [],
                            'total_amount' => 0
                        ];
                    }
                    $carts_data[$cart_id]['items'][] = [
                        'ten_sp' => $row['ten_sp'],
                        'soluong' => $row['soluong'],
                        'gia_ban' => $row['gia_ban']
                    ];
                    $carts_data[$cart_id]['total_amount'] += ($row['soluong'] * $row['gia_ban']);
                }
            }
           
        }
    }
?>

<div class="main-page-content"> <?php /* Đảm bảo CSS của bạn định vị class này đúng */ ?>
    <div class="page-title" style="padding: 20px; background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
        <div class="title" style="font-size: 1.5rem; font-weight: 500;">Danh sách giỏ hàng</div>
    </div>

    <div class="table-card" style="padding: 20px;">
        <?php // Hiển thị lỗi nếu có ?>
        <?php if (!empty($errorMessage)): ?>
            <div style="color: red; padding: 15px; border: 1px solid red; margin-bottom: 15px; background-color: #f8d7da;">
                <strong>Lỗi:</strong> <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <?php // Hiển thị bảng dữ liệu nếu không có lỗi và có dữ liệu ?>
        <?php if (empty($errorMessage) && !empty($carts_data)): ?>
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
              <thead>
                <tr style="background-color: #e9ecef;">
                  <th style="padding: 8px; border: 1px solid #dee2e6; text-align: left;">ID Giỏ hàng</th>
                  <th style="padding: 8px; border: 1px solid #dee2e6; text-align: left;">Tên Khách hàng</th>
                  <th style="padding: 8px; border: 1px solid #dee2e6; text-align: left;">Số điện thoại</th>
                  <th style="padding: 8px; border: 1px solid #dee2e6; text-align: left;">Ngày cập nhật</th>
                  <th style="padding: 8px; border: 1px solid #dee2e6; text-align: left;">Sản phẩm</th>
                  <th style="padding: 8px; border: 1px solid #dee2e6; text-align: left;">Số lượng</th>
                  <th style="padding: 8px; border: 1px solid #dee2e6; text-align: left;">Giá bán</th>
                  <th style="padding: 8px; border: 1px solid #dee2e6; text-align: left;">Thành tiền (mục)</th>
                </tr>
              </thead>
              <tbody>
                <?php
                foreach($carts_data as $cart_id => $cart_info) :
                    $rowspan = count($cart_info['items']);
                    // Đảm bảo rowspan ít nhất là 1 để không bị lỗi nếu items rỗng
                    if ($rowspan == 0) {
                        $rowspan = 1; 
                    }
                    $first_item = true;
                    
                    if (empty($cart_info['items'])) : 
                ?>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #dee2e6;"><?php echo $cart_id; ?></td>
                            <td style="padding: 8px; border: 1px solid #dee2e6;"><?php echo htmlspecialchars($cart_info['customer_name']); ?></td>
                            <td style="padding: 8px; border: 1px solid #dee2e6;"><?php echo htmlspecialchars($cart_info['customer_phone']); ?></td>
                            <td style="padding: 8px; border: 1px solid #dee2e6;"><?php echo $cart_info['ngaycapnhat']; ?></td>
                            <td colspan="4" style="text-align:center; padding: 8px; border: 1px solid #dee2e6;"><em>Giỏ hàng này không có sản phẩm nào (dữ liệu items rỗng).</em></td>
                        </tr>
                <?php
                    else:
                        foreach ($cart_info['items'] as $item) :
                ?>
                    <tr>
                      <?php if ($first_item) : ?>
                        <td style="padding: 8px; border: 1px solid #dee2e6;" rowspan="<?php echo $rowspan; ?>"><?php echo $cart_id; ?></td>
                        <td style="padding: 8px; border: 1px solid #dee2e6;" rowspan="<?php echo $rowspan; ?>"><?php echo htmlspecialchars($cart_info['customer_name']); ?></td>
                        <td style="padding: 8px; border: 1px solid #dee2e6;" rowspan="<?php echo $rowspan; ?>"><?php echo htmlspecialchars($cart_info['customer_phone']); ?></td>
                        <td style="padding: 8px; border: 1px solid #dee2e6;" rowspan="<?php echo $rowspan; ?>"><?php echo $cart_info['ngaycapnhat']; ?></td>
                      <?php endif; ?>
                      <td style="padding: 8px; border: 1px solid #dee2e6;"><?php echo htmlspecialchars($item['ten_sp']); ?></td>
                      <td style="padding: 8px; border: 1px solid #dee2e6;"><?php echo $item['soluong']; ?></td>
                      <td style="padding: 8px; border: 1px solid #dee2e6;"><?php echo number_format((float)$item['gia_ban']); ?> VNĐ</td>
                      <td style="padding: 8px; border: 1px solid #dee2e6;"><?php echo number_format((float)$item['soluong'] * (float)$item['gia_ban']); ?> VNĐ</td>
                    </tr>
                <?php
                            $first_item = false;
                        endforeach;
                    endif; 
                ?>
                    <tr>
                      <td colspan="7" style="text-align: right; font-weight: bold; border-top: 1px solid #ccc; padding: 8px;">Tổng tiền giỏ hàng:</td>
                      <td style="font-weight: bold; border-top: 1px solid #ccc; padding: 8px;"><?php echo number_format((float)$cart_info['total_amount']); ?> VNĐ</td>
                    </tr>
                     <tr><td colspan="8"><hr style="border-top: 1px dashed #eee; margin: 5px 0;"></td></tr>
                <?php endforeach; ?>
              </tbody>
            </table>
        <?php elseif (empty($errorMessage) && empty($carts_data)): ?>
            <p style="text-align: center; padding: 20px; font-size: 16px;">Hiện tại không có giỏ hàng nào trong hệ thống.</p>
        <?php endif; ?>
    </div>
</div> <?php

    include_once "admin_footer.php"; 
?>