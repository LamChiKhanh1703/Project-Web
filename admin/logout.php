<?php 
    // khởi động phiên
    session_start();
    // Hủy toàn bộ phiên 
    session_destroy();
    header("Location: login.php");
?>