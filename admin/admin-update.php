<?php
    include_once "sidebar.php";
    require_once "connect.php";
    // Kiểm tra có tồn tại $_GET id hay k
    if(isset($_GET["id_admin"])) {
        $id_admin = $_GET["id_admin"];
        // Viết sql để lấy ra hết thông tin của admin có id_admin trên
        $sql = "SELECT * FROM quan_tri WHERE id_admin = $id_admin";
        // Thực thi câu truy vấn trên
        $query = mysqli_query($conn, $sql);
        // Lấy dữ liệu 
        $row = mysqli_fetch_assoc($query);
    }
    // Kiểm tra đã nhấn nút Lưu chưa
    if(isset($_POST["submit"])) {
        $name = $_POST["hoten"];
        $email = $_POST["email"];
        $role = $_POST["vaitro"];
        $status = $_POST["status"];
        // Viết sql để Cập nhật dữ liệu
        $sql1 = "UPDATE quan_tri SET hoten = '$name', email = '$email', vaitro = $role, trangthai = $status WHERE id_admin = $id_admin";
        // thực thi truy vấn
        $query1 = mysqli_query($conn, $sql1);
        // Kiểm tra xem cập nhật thành công chưa
        if($query1) {
            $message = '<div class="alert-success">Cập nhật thành công!</div>';
        } else {
            $message = '<div class="alert-error">Có lỗi xảy ra. Vui lòng thử lại.</div>';
        }
    }
    
?>
<!-- Main Content -->
<div class="main-content">
    <?php  echo isset($message) ? $message : "" ?>
    <form action="" class="form" method="POST">
        <div class="input-box">
          <label>ID nhân viên</label>
          <input type="text" value="<?php echo $id_admin ?>" readonly/>
        </div>
        <div class="input-box">
          <label>Họ tên</label>
          <input type="text" name="hoten" value="<?php echo $row["hoten"] //hoten là tên cột trong bảng quản trị?>" required/>
        </div>
        <div class="input-box">
          <label>Email</label>
          <input type="text" name="email" value="<?php echo $row["email"] ?>" required/>
        </div>
        <div class="input-box">
          <label>Vai trò</label>
          <div class="select-box">
            <select name="vaitro" required>
                <option hidden>Vai trò</option>
                <option <?php echo $row["vaitro"] == 5 ? "selected" : "" ?> value="5">Quản lý</option>
                <option <?php echo $row["vaitro"] == 1 ? "selected" : "" ?> value="1">Nhân viên sale</option>
                <option <?php echo $row["vaitro"] == 2 ? "selected" : "" ?> value="2">Chăm sóc khách hàng</option>
                <option <?php echo $row["vaitro"] == 3 ? "selected" : "" ?> value="3">Nhân viên kho</option>
                <option <?php echo $row["vaitro"] == 4 ? "selected" : "" ?> value="4">Kế toán</option>
            </select>
        </div>
        <div class="gender-box">
          <h3>Tình trạng</h3>
          <div class="gender-option">
            <div class="gender">
              <input style="width: 30px" type="radio" value="0" name="status" <?php echo $row["trangthai"]==0 ? "checked" : "" ?>/>Hoạt động
            </div>
            <div class="gender">
              <input style="width: 30px" type="radio" value="1" name="status" <?php echo $row["trangthai"]==1 ? "checked" : "" ?>/>Bị khóa
            </div>
          </div>
        </div>
        <button name="submit">Lưu</button>
    </form>
</div>

<?php include_once "footer.php" ?>


