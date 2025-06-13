<?php
    include_once "sidebar.php";
    require_once "connect.php";
    // Kiểm tra user đã nhấn vào nút Lưu chưa
    if(isset($_POST["submit"])) {
        // Lấy dữ liệu từ form thông qua name của thẻ input
        $old_pass = md5($_POST["old_pass"]);
        $new_pass = $_POST["new_pass"];
        $new_pass2 = $_POST["new_pass2"];
        // Kiểm tra 2 mật khẩu đã giống nhau chưa
        if($new_pass == $new_pass2) {
            // Kiểm tra tiếp mật khẩu cũ có nhập đúng chưa
            $sql = "SELECT * FROM quan_tri WHERE id_admin= $id_admin AND matkhau = '$old_pass'";
            // thực thi truy vấn
            $query = mysqli_query($conn, $sql);
            // Lấy số dòng dữ liệu trả về
            $num = mysqli_num_rows($query);
            if($num == 0) {
                $message = '<div class="alert-error">Mật khẩu cũ không đúng.</div>';
            } else {
                // Nếu nhập đúng thì cho update password vào db
                $new_pass = md5($new_pass);
                $sql1 = "UPDATE quan_tri SET matkhau = '$new_pass' WHERE id_admin = $id_admin";
                $query1 = mysqli_query($conn, $sql1);
                if($query1) {
                    $message = '<div class="alert-success">Cập nhật mật khẩu thành công!</div>';
                } else {
                    $message = '<div class="alert-error">Có lỗi xảy ra. Vui lòng thử lại.</div>';
                }
            }
        } else {
            $message = '<div class="alert-error">Hai mật khẩu mới chưa trùng khớp.</div>';
        }
    }
?>
<!-- Main Content -->
<div class="main-content">
    <?php  echo isset($message) ? $message : "" ?>
    <form action="" class="form" method="POST">
        <div class="input-box">
          <label>Mật khẩu cũ</label>
          <input type="password" name="old_pass" required/>
        </div>
        <div class="input-box">
          <label>Mật khẩu mới</label>
          <input type="password" name="new_pass" required/>
        </div>
        <div class="input-box">
          <label>Nhập lại mật khẩu mới</label>
          <input type="password" name="new_pass2" required/>
        </div>
        <button name="submit">Lưu</button>
    </form>
</div>

<?php include_once "footer.php" ?>


