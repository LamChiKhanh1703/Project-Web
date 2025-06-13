<?php
      include_once "sidebar.php";
      require_once "connect.php";
      // Lấy ra tất cả hình thức thanh toán
      $sql = "SELECT * FROM thanh_toan"; // Thay đổi bảng
      $query = mysqli_query($conn, $sql);
  ?>
  <div class="main-content">
      <div class="page-title">
        <div class="title">Hình thức thanh toán</div>
        <div class="action-buttons">
          <button class="btn btn-primary">
            <a href="payment-add.php"><i class="fas fa-plus"></i>
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
              <th>Mã hình thức</th>
              <th>Tên hình thức</th>
              <th>Mô tả</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
              <?php foreach($query as $payment_method): ?>
                <tr>
                  <td><?php echo $payment_method["id_thanhtoan"] ?></td>
                  <td><?php echo $payment_method["ten_hinh_thuc"] ?></td>
                  <td><?php echo $payment_method["mo_ta"] ?></td>
                  <td><?php echo $payment_method["trang_thai"] == 0 ? "Hiện" : "Ẩn" ?></td>
                  <td>
                      <a href=""><button class="btn btn-outline btn-sm">
                          <i class="fa-solid fa-pen-to-square"></i>Sửa
                      </button></a>
                      <a href="" onclick="return confirm('Bạn có chắc chắn xóa dữ liệu này không?')"><button class="btn btn-outline btn-sm">
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