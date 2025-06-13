<?php
    include_once "sidebar.php";
    require_once "connect.php";

    $message = ''; // Biến để lưu thông báo

    // Kiểm tra xem người dùng đã nhấn nút Lưu chưa
    if(isset($_POST["submit"])) {
        $ten_nhanhieu = $_POST["ten_nhanhieu"];
        $trangthai = $_POST["trangthai"];

        // Xử lý upload logo (nếu có)
        $logo_filename = null;
        if (isset($_FILES["logo"]) && $_FILES["logo"]["name"] != "") {
            $target_dir = "../uploads/"; // Thư mục lưu logo
            $logo_filename = basename($_FILES["logo"]["name"]);
            $target_file = $target_dir . $logo_filename;
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Kiểm tra định dạng file ảnh
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                $message = '<div class="alert-error">Chỉ chấp nhận file JPG, JPEG, PNG & GIF.</div>';
                $uploadOk = 0;
            }

            // Kiểm tra nếu uploadOk vẫn là 1 thì tiến hành upload
            if ($uploadOk == 1) {
                if (!move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                    $message = '<div class="alert-error">Có lỗi khi tải lên logo.</div>';
                    $logo_filename = null; // Đặt lại null nếu upload thất bại
                }
            }
        }

        // Viết câu lệnh SQL để insert dữ liệu vào bảng nhan_hieu
        $sql = "INSERT INTO nhan_hieu(ten_nhanhieu, logo, trangthai) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssi', $ten_nhanhieu, $logo_filename, $trangthai);
        $query_success = mysqli_stmt_execute($stmt);

        // Kiểm tra xem đã insert thành công chưa
        if($query_success) {
            $message = '<div class="alert-success">Thêm thương hiệu mới thành công!</div>';
        } else {
            $message = '<div class="alert-error">Có lỗi xảy ra khi thêm thương hiệu. Vui lòng thử lại.</div>';
        }
    }
?>
<div class="main-content">
    <?php echo $message; ?>
    <div class="page-title">
        <div class="title">Thêm mới Thương hiệu</div>
    </div>

    <form action="" class="form" method="POST" enctype="multipart/form-data">
        <div class="input-box">
            <label>Tên Thương hiệu</label>
            <input type="text" name="ten_nhanhieu" required/>
        </div>
        <div class="input-box">
            <label>Logo (tùy chọn)</label>
            <input type="file" name="logo" accept="image/*"/>
        </div>
        <div class="gender-box">
            <h3>Trạng thái</h3>
            <div class="gender-option">
                <div class="gender">
                    <input style="width: 30px" type="radio" value="0" name="trangthai" checked/>Hiện
                </div>
                <div class="gender">
                    <input style="width: 30px" type="radio" value="1" name="trangthai" />Ẩn
                </div>
            </div>
        </div>
        <button name="submit" type="submit">Lưu</button>
    </form>
</div>

<?php include_once "footer.php"; ?>