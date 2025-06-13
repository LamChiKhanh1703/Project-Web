<?php
    include_once "sidebar.php";
?>
<!-- Main Content -->
<div class="main-content">
    <form action="#" class="form">
        <div class="input-box">
          <label>ID nhân viên</label>
          <input type="text" value="<?php echo $id_admin ?>" readonly/>
        </div>
        <div class="input-box">
          <label>Họ tên</label>
          <input type="text" value="<?php echo $name ?>" readonly/>
        </div>
        <div class="input-box">
          <label>Email</label>
          <input type="text" value="<?php echo $email ?>" readonly/>
        </div>
        <div class="input-box">
          <label>Vai trò</label>
          <input type="text" value="<?php switch($role) {
                case 0: echo "admin"; break;
                case 1: echo "Nhân viên Sale"; break;
                case 2: echo "Chăm sóc khách hàng"; break;
                case 3: echo "Nhân viên kho"; break;
                case 4: echo "Kế toán"; break;
                case 5: echo "Quản lý"; break;
          } 
          ?>" readonly/>
        </div>
        <div class="input-box">
          <label>Trạng thái</label>
          <input type="text" value="Đang hoạt động" readonly/>
        </div>
    </form>
</div>

<?php include_once "footer.php" ?>


