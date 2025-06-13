<?php
    include_once "sidebar.php";
    require_once "connect.php";
    // Lấy ra tất cả khách hàng
    $sql = "SELECT * FROM khach_hang";
    $query = mysqli_query($conn, $sql);
?>
<!-- Main Content -->
<div class="main-content">
  <div class="page-title">
    <div class="title">Danh sách khách hàng</div>
    <div class="action-buttons">
      <button class="btn btn-primary">
        <a href="customer-add.php"><i class="fas fa-plus"></i>
        Thêm mới </a>
      </button>
    </div>
  </div>

  <div class="table-card">
    <table class="data-table">
      <thead>
        <tr>
          <th>Mã khách</th>
          <th>Họ tên</th>
          <th>Số điện thoại</th>
          <th>Email</th>
          <th>Trạng thái</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($query as $khach) : ?>
            <tr>
              <td><?php echo $khach["id_khach"] ?></td>
              <td><?php echo $khach["hoten"] ?></td>
              <td><?php echo $khach["sodienthoai"] ?></td>
              <td><?php echo $khach["email"] ?></td>
              <td><?php echo $khach["trangthai"] == 0 ? "Hoạt động" : "Ngưng hoạt động" ?></td>
              <td>
                  <a href="customer-orders.php? id_khach=<?php echo $khach["id_khach"]?>"><button class="btn btn-outline btn-sm">

                      <i class="fas fa-eye"></i> Xem đơn hàng
                  </button></a>
                  <a href="customer-update.php? id_khach=<?php echo $khach["id_khach"]?>"><button class="btn btn-outline btn-sm">
                  <i class="">Sửa</i>
                  </a>
                  
              </td>
            </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include_once "footer.php" ?>