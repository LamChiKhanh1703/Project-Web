<?php
   // Khởi động phiên làm việc
   session_start();
   // Chèn file kết nối DB
   require_once "connect.php";
    // Kiểm tra xem user đã nhấn vào nút login chưa
   if(isset($_POST["submit"])) {
      $email = $_POST["email"]; // email chính là name của thẻ input
      $pass = md5($_POST["password"]); // password chính là name của thẻ input
      // Kiểm tra email và pass có tồn tại trong DB k?
      $sql = "SELECT * FROM quan_tri WHERE email = '$email' AND matkhau = '$pass'";
      // Thực thi câu lệnh sql trên
      $query = mysqli_query($conn, $sql);
      // Lấy số bản ghi trả về từ câu lệnh truy vấn
      $num = mysqli_num_rows($query);
      if($num == 0) {
         echo "Tài khoản không hợp lệ!";
      } else {
         // Lấy dữ liệu
         $row = mysqli_fetch_assoc($query);
         // Kiểm tra tiếp xem trạng thái của tài khoản có bị khóa k
         if ($row["trangthai"] == 0) {
            // Tạo phiên làm việc session cho user này
            $_SESSION["admin"] = [
               "id_admin" => $row["id_admin"],
               "name" => $row["hoten"],
               "email" => $row["email"],
               "role" => $row["vaitro"],
            ];
            // Điều hướng trang về dashboard
            header("Location: dashboard.php");
         } else {
            echo "Tài khoản của bạn đã bị khóa. Vui lòng liên hệ admin";
         } 
      }
   }
?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <title>Admin | Login</title>
      <link rel="stylesheet" href="../css/login.css">
   </head>
   <body>
      <div class="wrapper">
         <div class="title">
            Login Form
         </div>
         <form method="POST" action="">
            <div class="field">
               <input type="text" name="email" required>
               <label>Email Address</label>
            </div>
            <div class="field">
               <input type="password" name="password" required>
               <label>Password</label>
            </div>
            <div class="content">
               <div class="checkbox">
                  <input type="checkbox" id="remember-me">
                  <label for="remember-me">Remember me</label>
               </div>
            </div>
            <div class="field">
               <input type="submit" name="submit" value="Login">
            </div>
         </form>
      </div>
   </body>
</html>