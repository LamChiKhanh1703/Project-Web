<?php
include_once "includes/header.php";

// Lấy thông tin liên hệ từ CSDL
$sql_contact = "SELECT * FROM lien_he LIMIT 1";
$query_contact = mysqli_query($conn, $sql_contact);
$contact_info = mysqli_fetch_assoc($query_contact);
?>

<div class="container auth-form-container">
    <h2>Liên hệ với chúng tôi</h2>
    <?php if ($contact_info): ?>
        <p>Nếu bạn có bất kỳ câu hỏi hoặc phản hồi nào, xin đừng ngần ngại liên hệ với chúng tôi theo thông tin dưới đây:</p>
        <div class="contact-details my-3">
            <p><i class="fas fa-map-marker-alt"></i> <strong>Địa chỉ:</strong> <?php echo htmlspecialchars($contact_info['diachi']); ?></p>
            <p><i class="fas fa-phone-alt"></i> <strong>Hotline:</strong> <?php echo htmlspecialchars($contact_info['hotline']); ?></p>
            <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?php echo htmlspecialchars($contact_info['email']); ?></p>
            <?php if (!empty($contact_info['link_facebook'])): ?>
                <p><i class="fab fa-facebook-f"></i> <a href="<?php echo htmlspecialchars($contact_info['link_facebook']); ?>" target="_blank">Facebook</a></p>
            <?php endif; ?>
            <?php if (!empty($contact_info['link_youtube'])): ?>
                <p><i class="fab fa-youtube"></i> <a href="<?php echo htmlspecialchars($contact_info['link_youtube']); ?>" target="_blank">Youtube</a></p>
            <?php endif; ?>
            <?php if (!empty($contact_info['link_tiktok'])): ?>
                <p><i class="fab fa-tiktok"></i> <a href="<?php echo htmlspecialchars($contact_info['link_tiktok']); ?>" target="_blank">Tiktok</a></p>
            <?php endif; ?>
            <?php if (!empty($contact_info['link_instagram'])): ?>
                <p><i class="fab fa-instagram"></i> <a href="<?php echo htmlspecialchars($contact_info['link_instagram']); ?>" target="_blank">Instagram</a></p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>Thông tin liên hệ đang được cập nhật. Vui lòng quay lại sau.</p>
    <?php endif; ?>

    </div>

<?php include_once "includes/footer.php"; ?>