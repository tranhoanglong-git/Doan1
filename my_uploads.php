<?php
session_start();
require_once 'connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Lấy danh sách bài hát của user
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM songs WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Thống kê
$stats_sql = "SELECT 
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
                COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_count,
                COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_count
              FROM songs WHERE user_id = ?";
$stats_stmt = $conn->prepare($stats_sql);
$stats_stmt->bind_param("i", $user_id);
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result();
$stats = $stats_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài Hát Của Tôi</title>
    <link rel="stylesheet" href="./Css/style.css">
    <link rel="stylesheet" href="./Css/my_upload.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include("header.php"); ?>
    
    <div class="my-uploads-container">
        <h1 class="page-title">
            <i class="fas fa-music"></i> Bài Hát Của Tôi
        </h1>
        
        <!-- Thống kê -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['pending_count']; ?></div>
                <div class="stat-label">Chờ Duyệt</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['approved_count']; ?></div>
                <div class="stat-label">Đã Duyệt</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['rejected_count']; ?></div>
                <div class="stat-label">Đã Từ Chối</div>
            </div>
        </div>
        
        <!-- Danh sách bài hát -->
        <?php if ($result->num_rows > 0): ?>
            <div class="song-grid">
                <?php while ($song = $result->fetch_assoc()): ?>
                    <div class="song-card">
                        <div class="song-image">
                            <?php if (!empty($song['avt_song'])): ?>
                                <img src="<?php echo htmlspecialchars($song['avt_song']); ?>" 
                                     alt="<?php echo htmlspecialchars($song['name_song']); ?>">
                            <?php else: ?>
                                <div class="no-image">🎵</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="song-info">
                            <h3><?php echo htmlspecialchars($song['name_song']); ?></h3>
                            <div class="song-artist">
                                <i class="fas fa-microphone"></i> 
                                <?php echo htmlspecialchars($song['artist']); ?>
                            </div>
                            
                            <div class="song-meta">
                                <div><i class="fas fa-calendar"></i> Upload: <?php echo date('d/m/Y H:i', strtotime($song['created_at'])); ?></div>
                                <?php if ($song['status'] === 'approved' && $song['approved_at']): ?>
                                    <div><i class="fas fa-check-circle"></i> Duyệt: <?php echo date('d/m/Y H:i', strtotime($song['approved_at'])); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="status-badge status-<?php echo $song['status']; ?>">
                                <?php 
                                switch($song['status']) {
                                    case 'pending':
                                        echo '<i class="fas fa-clock"></i> Chờ Duyệt';
                                        break;
                                    case 'approved':
                                        echo '<i class="fas fa-check"></i> Đã Duyệt';
                                        break;
                                    case 'rejected':
                                        echo '<i class="fas fa-times"></i> Đã Từ Chối';
                                        break;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-songs">
                <i class="fas fa-music"></i>
                <h3>Bạn chưa upload bài hát nào!</h3>
                <p>Hãy bắt đầu chia sẻ âm nhạc của bạn với cộng đồng.</p>
                <a href="upload.php" class="upload-now-btn">
                    <i class="fas fa-upload"></i> Upload Ngay
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include("footer.php"); ?>
</body>
</html>
