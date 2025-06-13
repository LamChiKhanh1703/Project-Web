<?php
    include_once "sidebar.php";
    require_once "connect.php";
    // Lấy ra thông tin các đơn hàng
    $sql = "SELECT * FROM don_hang d
            INNER JOIN thanh_toan t ON t.id_thanhtoan = d.id_thanhtoan
            ORDER BY d.ngay_tao DESC";
    $query = mysqli_query($conn, $sql);
?>
<!-- Main Content -->
<div class="main-content">
  <div class="page-title">
    <div class="title">Danh sách đơn hàng</div>
    <div class="action-buttons">
      <button class="btn btn-primary">
        <a href="customer-add.php"><i class="fas fa-plus"></i>
        Thêm mới </a>
      </button>
    </div>
  </div>

  <div class="table-card">
    <div class="card-title">
    </div>
    <table class="data-table">
      <thead>
        <tr>
          <th>ID đơn hàng</th>
          <th>Tên người nhận</th>
          <th>Số điện thoại</th>
          <th>Địa chỉ</th>
          <th>ID Khách</th>
          <th>Thanh Toán</th>
          <th>Ngày tạo</th>
          <th>Trạng thái</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($query as $order) : ?>
            <tr>
              <td><?php echo $order["id_donhang"] ?></td>
              <td><?php echo $order["ten_nguoi_nhan"] ?></td>
              <td><?php echo $order["sodienthoai"] ?></td>
              <td><?php echo $order["diachi"] ?></td>
              <td><?php echo $order["id_khach"] ?></td>
              <td><?php echo $order["ten_hinh_thuc"] ?></td>
              <td><?php echo $order["ngay_tao"] ?></td>
              <td><?php switch($order["trang_thai"]) {
                            case 0: echo "Mới tạo"; break;
                            case 1: echo "Đang xử lý"; break;
                            case 2: echo "Đang vận chuyển"; break;
                            case 3: echo "Đã hoàn thành"; break;
                            case 4: echo "Đã hủy";
                        }
                  ?>
              </td>
              <td>
              <a href="order-detail.php?id=<?php echo $order["id_donhang"]?>"><button class="btn btn-outline btn-sm">
    <i class="fas fa-eye"></i> Xem / Sửa
</button></a>
<a href="order-delete.php?id=<?php echo $order["id_donhang"]?>" onclick="return confirm('Bạn có chắc chắn xóa đơn hàng này không? (Hành động này không thể hoàn tác và không khôi phục tồn kho sản phẩm)')"><button class="btn btn-outline btn-sm">
    <i class="fa-solid fa-trash"></i> Xóa
</button></a>
              </td>
            </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include_once "footer.php" ?>