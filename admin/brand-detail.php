<?php
    include_once "sidebar.php";
    require_once "connect.php";
    // Lấy ra id nhãn hiệu từ URL
    if(isset($_GET["brandid"])) {
        $brandid = $_GET["brandid"];
        $brandname = $_GET["brandname"];
        // Lấy ra tất cả dòng sản phẩm thuộc brand này
        $sql = "SELECT * FROM dong_sanpham WHERE id_nhanhieu = $brandid";
        $query = mysqli_query($conn, $sql);
    }
?>
<!-- Main Content -->
<div class="main-content">
  <div class="page-title">
    <div class="title">Danh sách dòng sản phẩm</div>
    <div class="title">Thương hiệu: <?php echo $brandname ?></div>
    <div class="action-buttons">
      <button class="btn btn-primary">
        <a href="admin-add.php"><i class="fas fa-plus"></i>
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
          <th>Mã dòng</th>
          <th>Tên dòng</th>
          <th>Trạng thái</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
          <?php foreach($query as $dong): ?>
            <tr>
              <td><?php echo $dong["id_dong"] // id_nhan là tên cột trong bảng nhãn hiệu ?></td>
              <td><?php echo $dong["ten_dong"] ?></td>
              <td><?php echo $dong["trangthai"] == 0 ? "Hiện" : "Ẩn" ?></td>
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