<?php
session_start();
require_once '../includes/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Xử lý xóa bài hát khỏi danh sách yêu thích
if (isset($_POST['remove_favorite'])) {
    $song_id = $_POST['song_id'];
    $sql = "DELETE FROM favorites WHERE user_id = ? AND song_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $song_id);
    $stmt->execute();
    
    // Redirect để tránh resubmit form
    header("Location: favorites.php");
    exit();
}

// Lấy danh sách bài hát yêu thích
$sql = "SELECT s.* FROM songs s 
        INNER JOIN favorites f ON s.id = f.song_id 
        WHERE f.user_id = ? 
        ORDER BY f.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài Hát Yêu Thích - HL SOUND</title>
    <link rel="stylesheet" href="../assets/css/song.css">
    <link rel="stylesheet" href="../assets/css/favorites.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include("../includes/header.php"); ?>
    
    <div class="khung-chinh">
        <div class="danh-sach-bai-hat">
            <div class="favorites-header">
                <h1><i class="fas fa-heart"></i> Bài Hát Yêu Thích</h1>
                <p>Những bài hát bạn đã thêm vào danh sách yêu thích</p>
            </div>
            
            <?php if ($result->num_rows > 0): ?>
                <div class="luoi-bai-hat">
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="the-bai-hat" data-song="<?php echo htmlspecialchars($row['file_song']); ?>"
                             data-image="<?php echo htmlspecialchars($row['avt_song']); ?>"
                             data-title="<?php echo htmlspecialchars($row['name_song']); ?>"
                             data-artist="<?php echo htmlspecialchars($row['artist']); ?>">
                            
                            <!-- Nút xóa khỏi yêu thích -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="song_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="remove_favorite" class="remove-favorite-btn" 
                                        onclick="return confirm('Bạn có chắc muốn xóa bài hát này khỏi danh sách yêu thích?')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            
                            <div class="anh-bai-hat">
                                <?php if (!empty($row['avt_song'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['avt_song']); ?>" alt="<?php echo htmlspecialchars($row['name_song']); ?>">
                                <?php else: ?>
                                    <div class="khong-co-anh">🎵</div>
                                <?php endif; ?>
                            </div>
                            <div class="thong-tin-bai-hat">
                                <h3><?php echo htmlspecialchars($row['name_song']); ?></h3>
                                <p><?php echo htmlspecialchars($row['artist']); ?></p>
                            </div>
                            <button class="nut-phat">
                                <i class="fas fa-play"></i>
                            </button>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-favorites">
                    <i class="fas fa-heart-broken"></i>
                    <h3>Chưa có bài hát yêu thích nào</h3>
                    <p>Hãy khám phá và thêm những bài hát bạn yêu thích vào danh sách này!</p>
                    <a href="song.php" class="browse-songs-btn">
                        <i class="fas fa-music"></i> Khám Phá Bài Hát
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Trình phát nhạc -->
        <div class="trinh-phat-nhac" id="musicPlayer">
            <button class="nut-dieu-khien-trinh-phat" id="togglePlayer">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="nut-thu-nho" id="toggleMinimize">
                <i class="fas fa-minus"></i>
            </button>
            <div class="dau-trinh-phat">
                <div class="dang-phat">
                    <div class="anh-bai-hat">
                        <img id="currentSongImage" src="" alt="Album Art">
                    </div>
                    <div class="chi-tiet-bai-hat">
                        <h3 id="currentSongTitle">Chọn bài hát để phát</h3>
                        <p id="currentSongArtist"></p>
                    </div>
                </div>
            </div>

            <div class="dieu-khien">
                <div class="thanh-tien-trinh">
                    <div class="tien-trinh" id="progress"></div>
                    <input type="range" id="thanhTienTrinh" min="0" max="100" value="0">
                </div>

                <div class="nut-dieu-khien">
                    <button id="prevBtn" class="nut">
                        <i class="fas fa-backward"></i>
                    </button>
                    <button id="playBtn" class="nut nut-phat">
                        <i class="fas fa-play"></i>
                    </button>
                    <button id="nextBtn" class="nut">
                        <i class="fas fa-forward"></i>
                    </button>
                </div>

                <div class="dieu-khien-am-luong">
                    <i class="fas fa-volume-up"></i>
                    <input type="range" id="thanhAmLuong" min="0" max="100" value="100">
                </div>
            </div>

            <audio id="audioPlayer"></audio>
        </div>
    </div>

    <?php include("../includes/footer.php"); ?>
    <script src="../assets/js/song.js"></script>
</body>
</html>
