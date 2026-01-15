<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Detect if we're being called from root or subdirectory
$is_root = (basename(dirname($_SERVER['SCRIPT_FILENAME'])) === basename($_SERVER['DOCUMENT_ROOT']) || 
            basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'Doan1');
$base_path = $is_root ? './' : '../';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HL SOUND - HoangLong Music</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- HEADER - THANH ĐIỀU HƯỚNG TRÊN CÙNG -->
    <header class="header">
        <div class="nav-container">
            <!-- Logo trang web -->
            <div class="logo">HL Music</div>
            <!-- thanhtimkiem -->
            <form action="<?php echo $base_path; ?>pages/search.php" method="GET" class="search">
                <!-- kinhlup -->
                <svg viewBox="0 0 24 24" aria-hidden="true" class="search-icon">
                    <g>
                        <path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path>
                    </g>
                </svg>

                <input class="search-bar" type="search" name="q" placeholder="Tìm kiếm bài hát, nghệ sĩ..." 
                       value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
            </form>
            <!-- Menu điều hướng -->
            <nav>
                <ul class="nav-menu">
                    <li><a href="<?php echo $base_path; ?>index.php">Trang Chủ</a></li>
                    <li><a href="<?php echo $base_path; ?>songs/song.php">Bài Hát</a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="user-profile" style="position:relative;">
                            <a href="#" class="user-avatar" id="userAvatar">
                                <i class="fas fa-user-circle"></i>
                            </a>
                            <div class="dropdown-menu" id="dropdownMenu" style="display:none;">
                                <a href="<?php echo $base_path; ?>songs/favorites.php"><i class="fas fa-heart"></i> Bài Hát Yêu Thích</a>
                                <a href="<?php echo $base_path; ?>songs/my_uploads.php"><i class="fas fa-music"></i> Bài Hát Của Tôi</a>
                                <a href="<?php echo $base_path; ?>songs/upload.php"><i class="fas fa-upload"></i> Upload Nhạc</a>
                                <?php if(isset($_SESSION['username']) && $_SESSION['username'] === 'admin'): ?>
                                    <a href="<?php echo $base_path; ?>admin/approval.php"><i class="fas fa-shield-alt"></i> Duyệt Bài Hát</a>
                                <?php endif; ?>
                                <a href="<?php echo $base_path; ?>auth/logout.php"><i class="fas fa-sign-out-alt"></i> Đăng Xuất</a>
                            </div>
                        </li>
                    <?php else: ?>
                        <li><a href="<?php echo $base_path; ?>auth/login.php">Đăng Nhập</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="main-content">
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const avatar = document.getElementById('userAvatar');
        const menu = document.getElementById('dropdownMenu');
        let menuOpen = false;

        if (avatar && menu) {
            avatar.addEventListener('click', function(e) {
                e.preventDefault();
                menuOpen = !menuOpen;
                menu.style.display = menuOpen ? 'block' : 'none';
            });

            document.addEventListener('click', function(e) {
                if (!avatar.contains(e.target) && !menu.contains(e.target)) {
                    menu.style.display = 'none';
                    menuOpen = false;
                }
            });
        }
    });
    </script>
</body>

</html>
