<?php
      include_once "sidebar.php";
      require_once "connect.php";
      // Lấy ra tất cả phương thức vận chuyển
      $sql = "SELECT * FROM van_chuyen"; // Thay đổi bảng
      $query = mysqli_query($conn, $sql);
  ?>
  <div class="main-content">
      <div class="page-title">
        <div class="title">Phương thức vận chuyển</div>
        <div class="action-buttons">
          <button class="btn btn-primary">
            <a href="shipping-add.php"><i class="fas fa-plus"></i>
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
              <th>Mã vận chuyển</th>
              <th>Tên vận chuyển</th>
              <th>Phí ship</th>
              <th>Thời gian</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
              <?php foreach($query as $shipping_method): ?>
                <tr>
                  <td><?php echo $shipping_method["id_vanchuyen"] ?></td>
                  <td><?php echo $shipping_method["ten_vanchuyen"] ?></td>
                  <td><?php echo number_format($shipping_method["phi_ship"]) ?> VNĐ</td>
                  <td><?php echo $shipping_method["thoi_gian"] ?></td>
                  <td><?php echo $shipping_method["trang_thai"] == 0 ? "Hiện" : "Ẩn" ?></td>
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