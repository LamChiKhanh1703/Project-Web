<?php
    include_once "sidebar.php";
    require_once "connect.php";
    // Lấy danh sách sản phẩm
    // Sử dụng LEFT JOIN để đảm bảo sản phẩm vẫn hiển thị ngay cả khi dữ liệu nhãn hiệu/dòng sản phẩm không khớp
    // Thêm điều kiện WHERE để chỉ lấy sản phẩm có trạng thái là 'Hiện' (0)
    // Sắp xếp theo ngày cập nhật giảm dần để sản phẩm mới nhất lên đầu
    $sql = "SELECT sp.*, nh.ten_nhanhieu, ds.ten_dong
            FROM san_pham sp
            LEFT JOIN nhan_hieu nh ON nh.id_nhan = sp.id_nhanhieu
            LEFT JOIN dong_sanpham ds ON ds.id_dong = sp.id_dong
            WHERE sp.trangthai = 0 -- Chỉ hiển thị các sản phẩm có trạng thái 'Hiện'
            ORDER BY sp.ngaycapnhat DESC"; // Hiển thị sản phẩm mới nhất lên đầu
    // Thực thi truy vấn
    $query = mysqli_query($conn, $sql);

?>
<div class="main-content">
    <div class="page-title">
      <div class="title">Danh sách sản phẩm</div>
      <div class="action-buttons">
        <button class="btn btn-primary">
          <a href="product-add.php"><i class="fas fa-plus"></i>
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
            <th>Mã sản phẩm</th>
            <th>Tên sản phẩm</th>
            <th>Nhãn hiệu</th>
            <th>Dòng sản phẩm</th>
            <th>Ngày cập nhật</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
            <?php foreach($query as $sp) : ?>
                <tr>
                    <td><?php echo $sp["id_sp"] ?></td>
                    <td><?php echo $sp["ten_sp"] ?></td>
                    <td><?php echo htmlspecialchars($sp["ten_nhanhieu"]) ?></td>
                    <td><?php echo htmlspecialchars($sp["ten_dong"]) ?></td>
                    <td><?php echo $sp["ngaycapnhat"] ?></td>
                    <td><?php echo $sp["trangthai"] == 0 ? "Hiện" : "Ẩn" ?></td>
                    <td>
                        <a href="product-detail.php?idsp=<?php echo $sp["id_sp"]?>"><button class="btn btn-outline btn-sm">
                            <i class="fas fa-eye"></i> View
                        </button></a>
                        <a href="product-update.php?idsp=<?php echo $sp["id_sp"]?>"><button class="btn btn-outline btn-sm">
                            <i class="fa-solid fa-pen-to-square"></i>Sửa
                        </button></a>
                        <a href="product-delete.php?idsp=<?php echo $sp["id_sp"]?>" onclick="return confirm('Bạn có chắc chắn xóa sản phẩm này không?')"><button class="btn btn-outline btn-sm">
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