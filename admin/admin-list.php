<?php
    include_once "sidebar.php";
    require_once "connect.php";
    // Lấy ra tất cả nhân viên
    $sql = "SELECT * FROM quan_tri";
    // Thực thi câu lệnh
    $query = mysqli_query($conn, $sql);

?>
<!-- Main Content -->
<div class="main-content">
  <div class="page-title">
    <div class="title">Danh sách tài khoản Nhân viên</div>
    <div class="action-buttons">
      <button class="btn btn-primary">
        <a href="admin-add.php"><i class="fas fa-plus"></i>
        Thêm mới </a>
      </button>
    </div>
  </div>

  <div class="table-card">
    <table class="data-table">
      <thead>
        <tr>
          <th>Mã nhân viên</th>
          <th>Họ tên</th>
          <th>Email</th>
          <th>Vai trò</th>
          <th>Tình trạng</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
          <?php foreach($query as $nhanvien): ?>
            <tr>
              <td><?php echo $nhanvien["id_admin"] // id_admin chính là tên cột trong bảng quan_tri ?></td>
              <td><?php echo $nhanvien["hoten"] ?></td>
              <td><?php echo $nhanvien["email"] ?></td>
              <td><?php switch($nhanvien["vaitro"]) {
                case 0: echo "admin"; break;
                case 1: echo "Nhân viên Sale"; break;
                case 2: echo "Chăm sóc khách hàng"; break;
                case 3: echo "Nhân viên kho"; break;
                case 4: echo "Kế toán"; break;
                case 5: echo "Quản lý"; break;
              }
              ?></td>
              <td><?php echo $nhanvien["trangthai"]==0 ? "Hoạt động" : "Bị khóa" ?></td>
              <td>
                <a href="admin-update.php?id_admin=<?php echo $nhanvien["id_admin"] ?>"><button class="btn btn-outline btn-sm">
                  <i class="fa-solid fa-pen-to-square"></i>Sửa
                </button></a>
                <a href="admin-delete.php?id_admin=<?php echo $nhanvien["id_admin"] ?>" onclick="return confirm('Bạn có chắc chắn xóa dữ liệu này không?')"><button class="btn btn-outline btn-sm">
                  <i class="fa-solid fa-trash"></i> Xóa
                </button></a>
              </td>
            </tr>

          <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include_once "footer.php"; ?>