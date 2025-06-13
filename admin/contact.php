<?php
    include_once "sidebar.php";
    require_once "connect.php";
    // Lấy thông tin từ bảng liên hệ
    $sql = "SELECT * FROM lien_he";
    $query = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($query);
?>
<!-- Main Content -->
<div class="main-content">
  <form action="#" class="form">
    <div class="input-box">
      <label>Hotline</label>
      <input type="text" value="<?php echo $row["hotline"] ?>" required/>
    </div>
    <div class="input-box">
      <label>Email</label>
      <input type="text" value="<?php echo $row["email"] ?>" required/>
    </div>
    <div class="input-box">
      <label>Địa chỉ</label>
      <input type="email" value="<?php echo $row["diachi"] ?>" required/>
    </div>
    <div class="input-box">
      <label>Link facebook</label>
      <input type="text" value="<?php echo $row["link_facebook"] ?>"/>
    </div>
    <div class="input-box">
      <label>Link youtube</label>
      <input type="text" value="<?php echo $row["link_youtube"] ?>"/>
    </div>
    <div class="input-box">
      <label>Link tiktok</label>
      <input type="text" value="<?php echo $row["link_tiktok"] ?>"/>
    </div>
    <button name="submit">Lưu</button>
  </form>
</div>

<?php include_once "footer.php" ?>

