<?php
include("connect.php");

// Lấy từ khóa tìm kiếm
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

if (!empty($search)) {
    // Tìm kiếm trong database
    $sql = "SELECT * FROM songs WHERE name_song LIKE ? OR artist LIKE ? ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$search%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = null;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm - HL Music</title>
    <link rel="stylesheet" href="./Css/style.css">
    <link rel="stylesheet" href="./Css/header.css">
    <link rel="stylesheet" href="./Css/song.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .ket-qua-tim-kiem {
            margin-top: 20px;
        }
        
        .ket-qua-tim-kiem h2 {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .khong-tim-thay {
            text-align: center;
            padding: 40px 20px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
        }
        
        .khong-tim-thay i {
            font-size: 48px;
            color: rgba(255, 107, 107, 0.3);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>
    
    <div class="main-content">
        <div class="container">
            <div class="ket-qua-tim-kiem">
                <?php if (!empty($search)): ?>
                    <h2>Kết quả tìm kiếm cho "<?php echo htmlspecialchars($search); ?>"</h2>
                    
                    <?php if ($result && $result->num_rows > 0): ?>
                        <div class="luoi-bai-hat">
                            <?php while($row = $result->fetch_assoc()): ?>
                                <div class="the-bai-hat" data-song="<?php echo htmlspecialchars($row['file_song']); ?>"
                                     data-image="<?php echo htmlspecialchars($row['avt_song']); ?>"
                                     data-title="<?php echo htmlspecialchars($row['name_song']); ?>"
                                     data-artist="<?php echo htmlspecialchars($row['artist']); ?>">
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
                        <div class="khong-tim-thay">
                            <i class="fas fa-search"></i>
                            <p>Không tìm thấy bài hát nào phù hợp với từ khóa "<?php echo htmlspecialchars($search); ?>"</p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include("footer.php"); ?>
    <script src="./js/song.js"></script>
</body>
</html> 