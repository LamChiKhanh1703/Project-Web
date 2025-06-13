<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    echo "<h3>Kiểm tra file connect.php</h3>";

    // File connect.php nằm cùng cấp thư mục admin
    require_once "connect.php"; 

    if (isset($conn) && $conn instanceof mysqli) {
        echo "<p style='color:green;'>Kết nối CSDL ('webdochoi') thành công!</p>";
        echo "<p>Thông tin host: " . mysqli_get_host_info($conn) . "</p>";
        // mysqli_close($conn); // Có thể đóng hoặc không tùy mục đích test
    } elseif (isset($conn)) {
        echo "<p style='color:orange;'>File connect.php đã được include, nhưng biến \$conn không phải là đối tượng mysqli hợp lệ.</p>";
        echo "Giá trị của \$conn: <pre>";
        var_dump($conn);
        echo "</pre>";
    } else {
        echo "<p style='color:red;'>File connect.php đã được include, nhưng biến \$conn không được thiết lập.</p>";
    }
?>