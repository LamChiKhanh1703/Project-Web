<!DOCTYPE html>
<?php
    // Kiểm tra xem có phiên làm việc hay k?

    session_start();
    if(!isset($_SESSION["admin"])) {
        header("Location: login.php");
    } else {
        $id_admin = $_SESSION["admin"]["id_admin"];
        $name = $_SESSION["admin"]["name"];
        $email = $_SESSION["admin"]["email"];
        $role = $_SESSION["admin"]["role"];
    }
?>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/form.css" />
  </head>
  <body>
    <div class="container">
      <!-- Sidebar -->
      <div class="sidebar">
        <div class="logo">
          <h1>Admin<span>/ Quản lý</span></h1>
        </div>
        <div class="nav-menu">
          <!-- Phần quản lý chính (hay thao tác) -->
          <div class="menu-heading">Chính</div>
          <div class="nav-item active">
            <i class="fas fa-chart-pie"></i>
            <span><a href="dashboard.php">Tổng quan</a></span>
          </div>
          <div class="nav-item">
            <i class="fas fa-users"></i>
            <span><a href="customer.php">Khách hàng</a></span>
          </div>
          <div class="nav-item">
            <i class="fa-solid fa-mobile-screen-button"></i>
            <a href="product-list.php"><span>Sản phẩm</span></a>
          </div>
          <div class="nav-item">
          <i class="fas fa-shopping-cart"></i>
    <a href="order.php"><span>Đơn hàng</span></a>
</div>


          <!-- Phần Thêm (ít thao tác hơn)-->
          <div class="menu-heading">Thêm</div>
          <div class="nav-item">
            <i class="fa-solid fa-circle-info"></i>
            <a href="brand.php"><span>Thương hiệu</span></a>
          </div>
         
          <div class="nav-item">
            <i class="fa-solid fa-credit-card"></i>
            <a href="payment.php"><span>HT thanh toán</span></a>
          </div>
          <div class="nav-item">
            <i class="fa-solid fa-truck-fast"></i>
            <a href="shipping.php"><span>PT vận chuyển</span></a>
          </div>
          <div class="nav-item">
            <i class="fa-solid fa-phone-volume"></i>
            <a href="contact.php"><span>Liên hệ</span></a>
          </div>

          <!-- Phần Quản lý tài khoản -->
          <div class="menu-heading">Admin</div>
          <div class="nav-item">
            <i class="fa-solid fa-circle-user"></i>
            <a href="profile.php"><span>Thông tin</span></a>
          </div>
          <?php 
          // Kiểm tra có phải tài khoản admin không thì mới hiển thị phần Quản lý tài khoản nhân viên
            if($role == 0) {
              echo '<div class="nav-item">
                      <i class="fa-solid fa-id-badge"></i>
                      <a href="admin-list.php"><span>Quản lý tài khoản</span></a>
                    </div>';
            }
          ?>
          <div class="nav-item">
            <i class="fa-solid fa-key"></i>
            <a href="change-password.php"><span>Đổi mật khẩu</span></a>
          </div>
          <div class="nav-item">
            <i class="fa-solid fa-right-from-bracket"></i>
            <a href="logout.php"><span>Đăng xuất</span></a>
          </div>
        </div>
      </div>

      <!-- Header -->
      <div class="header">
        <div class="search-bar">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="Search..." />
        </div>
        <div class="header-actions">
          <div class="notification">
            <i class="fas fa-bell"></i>
            <div class="badge">3</div>
          </div>
          <div class="notification">
            <i class="fas fa-envelope"></i>
            <div class="badge">5</div>
          </div>
          <div class="user-profile">
            <div class="profile-img">JD</div>
            <div class="user-info">
              <div class="user-name"><?php echo $name ?></div>
              <div class="user-role">
                <?php switch($role) {
                    case 0: echo "Admin"; break;
                    case 1: echo "Sale Marketing"; break;
                    case 2: echo "Chăm sóc khách hàng"; break;
                    case 3: echo "Nhân viên kho"; break;
                    case 4: echo "Quản lý"; break;
                } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
