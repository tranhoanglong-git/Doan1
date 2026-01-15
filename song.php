<?php
session_start();
include("connect.php");

// Lấy danh sách bài hát đã duyệt từ database
$sql = "SELECT * FROM songs WHERE status = 'approved' ORDER BY id DESC";
$result = $conn->query($sql);

// Lấy danh sách bài hát yêu thích của user (nếu đã đăng nhập)
$favorites = [];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $fav_sql = "SELECT song_id FROM favorites WHERE user_id = ?";
    $fav_stmt = $conn->prepare($fav_sql);
    $fav_stmt->bind_param("i", $user_id);
    $fav_stmt->execute();
    $fav_result = $fav_stmt->get_result();
    
    while ($fav_row = $fav_result->fetch_assoc()) {
        $favorites[] = $fav_row['song_id'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Bài Hát</title>
    <link rel="stylesheet" href="./Css/song.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .the-bai-hat {
            position: relative;
        }
        
        .favorite-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 10;
            color: #ccc;
        }
        
        .favorite-btn:hover {
            transform: scale(1.1);
            background: rgba(255, 255, 255, 1);
        }
        
        .favorite-btn.active {
            color: #e74c3c;
        }
        
        .favorite-btn.active:hover {
            color: #c0392b;
        }
        
        .favorite-btn i {
            font-size: 16px;
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            z-index: 1000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification.success {
            background: #27ae60;
        }
        
        .notification.error {
            background: #e74c3c;
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>
    <div class="khung-chinh">
        <div class="danh-sach-bai-hat">
            <h1>Danh Sách Bài Hát</h1>
            
            <!-- Thông báo -->
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            <div class="luoi-bai-hat">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $isFavorite = in_array($row['id'], $favorites);
                        ?>
                        <div class="the-bai-hat" data-song="<?php echo htmlspecialchars($row['file_song']); ?>"
                             data-image="<?php echo htmlspecialchars($row['avt_song']); ?>"
                             data-title="<?php echo htmlspecialchars($row['name_song']); ?>"
                             data-artist="<?php echo htmlspecialchars($row['artist']); ?>">
                            
                            <!-- Nút yêu thích -->
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <button class="favorite-btn <?php echo $isFavorite ? 'active' : ''; ?>" 
                                        data-song-id="<?php echo $row['id']; ?>"
                                        onclick="toggleFavorite(<?php echo $row['id']; ?>, this)">
                                    <i class="fas fa-heart"></i>
                                </button>
                            <?php endif; ?>
                            
                            <!-- Nút xóa (chỉ admin) -->
                            <?php if(isset($_SESSION['username']) && $_SESSION['username'] === 'admin'): ?>
                                <button class="delete-btn" 
                                        data-song-id="<?php echo $row['id']; ?>"
                                        onclick="deleteSong(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['name_song']); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php endif; ?>
                            
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
                        <?php
                    }
                } else {
                    echo "<p class='khong-co-bai-hat'>Chưa có bài hát nào được thêm vào.</p>";
                }
                ?>
            </div>
        </div>

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

    <!-- Thông báo -->
    <div id="notification" class="notification"></div>

    <?php include("footer.php"); ?>
    <script src="./js/song.js"></script>
    <script>
        function toggleFavorite(songId, button) {
            const isFavorite = button.classList.contains('active');
            const action = isFavorite ? 'remove' : 'add';
            
            fetch('toggle_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `song_id=${songId}&action=${action}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (action === 'add') {
                        button.classList.add('active');
                        showNotification(data.message, 'success');
                    } else {
                        button.classList.remove('active');
                        showNotification(data.message, 'success');
                    }
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Có lỗi xảy ra', 'error');
            });
        }
        
        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.classList.add('show');
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }
    </script>
</body>
</html>

