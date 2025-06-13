<?php
    include_once "sidebar.php";
    require_once "connect.php";
    // Lấy ra tất cả thương hiệu
    $sql = "SELECT * FROM nhan_hieu";
    $query = mysqli_query($conn, $sql);

?>
<!-- Main Content -->
<div class="main-content">
  <div class="page-title">
    <div class="title">Danh sách thương hiệu</div>
    <div class="action-buttons">
      <button class="btn btn-primary">
      <a href="brand-add.php"><i class="fas fa-plus"></i>
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
          <th>Mã nhãn hiệu</th>
          <th>Tên nhãn hiệu</th>
          <th>Trạng thái</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
          <?php foreach($query as $nhan): ?>
            <tr>
              <td><?php echo $nhan["id_nhan"] // id_nhan là tên cột trong bảng nhãn hiệu ?></td>
              <td><?php echo $nhan["ten_nhanhieu"] ?></td>
              <td><?php echo $nhan["trangthai"] == 0 ? "Hiện" : "Ẩn" ?></td>
              <td>
                  <a href="brand-detail.php?brandid=<?php echo $nhan["id_nhan"]?>&brandname=<?php echo $nhan["ten_nhanhieu"] ?>"><button class="btn btn-outline btn-sm">
                      <i class="fas fa-eye"></i> Xem dòng sản phẩm
                  </button></a>
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