</body>
</html>
<?php
    // Kiểm tra xem biến $conn có tồn tại và có phải là một đối tượng mysqli không
    if (isset($conn) && is_object($conn) && get_class($conn) === 'mysqli') {
        // Kiểm tra xem kết nối có còn mở không trước khi đóng
        // mysqli_thread_id sẽ trả về false nếu kết nối đã đóng
        if (mysqli_thread_id($conn)) {
            mysqli_close($conn);
        }
    }
?>