

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HL SOUND - HoangLong Music</title>
    <link rel="stylesheet" href="./assets/css/index.css">
</head>
    <?php
        include("includes/header.php");
    ?>
    
    
    <!-- Thông báo lỗi truy cập -->
    <?php if(isset($_GET['error']) && $_GET['error'] === 'access_denied'): ?>
        <div class="thong-bao-loi" style="
            background: rgba(255, 71, 87, 0.1);
            border: 1px solid rgba(255, 71, 87, 0.3);
            color: #ff4757;
            padding: 15px 20px;
            margin: 20px auto;
            max-width: 600px;
            border-radius: 10px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        ">
            <i class="fas fa-exclamation-triangle" style="font-size: 20px;"></i>
            <span>Bạn không có quyền truy cập vào trang này. Chỉ tài khoản admin mới được phép xóa bài hát.</span>
        </div>
    <?php endif; ?>
    
    <!-- hero  PHẦN BANNER CHÍNH -->
    <section class="hero">
        <div class="hero-content">
            <!-- Dòng chữ chào mừng -->
            <p class="hero-subtitle">Chào mừng đến với</p>

            <!-- Tiêu đề chính -->
            <h1 class="hero-title">HoangLong Music</h1>

            <!-- Mô tả -->
            <p class="hero-description">
                Khám phá thế giới âm nhạc tuyệt vời với những bản nhạc hay nhất.
                Trải nghiệm chất lượng âm thanh hoàn hảo và giao diện hiện đại.
            </p>
            <!-- Nút hành động -->
            <a href="songs/song.php" class="cta-button" onclick="createParticles()">Khám Phá Ngay</a>
        </div>
    </section>
    <?php
        include("includes/footer.php");
    ?>
    <script src="assets/js/index.js"></script>
</body>

</html>
