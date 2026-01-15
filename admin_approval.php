<?php
session_start();
require_once 'connect.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Kiểm tra quyền admin
if ($_SESSION['username'] !== 'admin') {
    header("Location: index.php?error=access_denied");
    exit();
}

// Xử lý duyệt/từ chối bài hát
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $song_id = intval($_POST['song_id']);
    $action = $_POST['action']; // 'approve' hoặc 'reject'
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
    
    try {
        if ($action === 'approve') {
            // Duyệt bài hát
            $sql = "UPDATE songs SET status = 'approved', approved_at = NOW(), approved_by = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $_SESSION['user_id'], $song_id);
            
            if ($stmt->execute()) {
                // Lưu lịch sử duyệt
                $history_sql = "INSERT INTO approval_history (song_id, action, admin_id, reason) VALUES (?, 'approved', ?, ?)";
                $history_stmt = $conn->prepare($history_sql);
                $history_stmt->bind_param("iis", $song_id, $_SESSION['user_id'], $reason);
                $history_stmt->execute();
                
                $success_message = "Đã duyệt bài hát thành công!";
            } else {
                $error_message = "Lỗi khi duyệt bài hát!";
            }
        } elseif ($action === 'reject') {
            // Từ chối bài hát
            $sql = "UPDATE songs SET status = 'rejected', approved_at = NOW(), approved_by = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $_SESSION['user_id'], $song_id);
            
            if ($stmt->execute()) {
                // Lưu lịch sử từ chối
                $history_sql = "INSERT INTO approval_history (song_id, action, admin_id, reason) VALUES (?, 'rejected', ?, ?)";
                $history_stmt = $conn->prepare($history_sql);
                $history_stmt->bind_param("iis", $song_id, $_SESSION['user_id'], $reason);
                $history_stmt->execute();
                
                $success_message = "Đã từ chối bài hát!";
            } else {
                $error_message = "Lỗi khi từ chối bài hát!";
            }
        }
    } catch (Exception $e) {
        $error_message = "Lỗi: " . $e->getMessage();
    }
}

// Lấy danh sách bài hát chờ duyệt
$pending_sql = "SELECT s.*, u.username as uploader_name 
                FROM songs s 
                LEFT JOIN users u ON s.user_id = u.id 
                WHERE s.status = 'pending' 
                ORDER BY s.created_at ASC";
$pending_result = $conn->query($pending_sql);

// Lấy danh sách bài hát đã duyệt gần đây
$approved_sql = "SELECT s.*, u.username as uploader_name, au.username as approver_name 
                 FROM songs s 
                 LEFT JOIN users u ON s.user_id = u.id 
                 LEFT JOIN users au ON s.approved_by = au.id 
                 WHERE s.status = 'approved' 
                 ORDER BY s.approved_at DESC 
                 LIMIT 10";
$approved_result = $conn->query($approved_sql);

// Lấy thống kê
$stats_sql = "SELECT 
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
                COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_count,
                COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_count
              FROM songs";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Duyệt Bài Hát</title>
    <link rel="stylesheet" href="./Css/style.css">
    <link rel="stylesheet" href="./Css/admin_approval.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<body>
    <?php include("header.php"); ?>
    
    <div class="admin-container">
        <h1 style="text-align: center; color: #fff; margin-bottom: 30px; font-size: 2.5rem;">
            <i class="fas fa-shield-alt"></i> Admin Panel - Duyệt Bài Hát
        </h1>
        
        <!-- Thông báo -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
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
        
        <!-- Bài hát chờ duyệt -->
        <div class="section">
            <h2><i class="fas fa-clock"></i> Bài Hát Chờ Duyệt (<?php echo $pending_result->num_rows; ?>)</h2>
            
            <?php if ($pending_result->num_rows > 0): ?>
                <?php while ($song = $pending_result->fetch_assoc()): ?>
                    <div class="song-item">
                        <div class="song-info">
                            <div class="song-details">
                                <h3><?php echo htmlspecialchars($song['name_song']); ?></h3>
                                <div class="song-meta">
                                    <strong>Ca sĩ:</strong> <?php echo htmlspecialchars($song['artist']); ?><br>
                                    <strong>Upload bởi:</strong> <?php echo htmlspecialchars($song['uploader_name']); ?><br>
                                    <strong>Thời gian:</strong> <?php echo date('d/m/Y H:i', strtotime($song['created_at'])); ?>
                                </div>
                            </div>
                            <div class="song-actions">
                                <button class="btn btn-approve" onclick="approveSong(<?php echo $song['id']; ?>, '<?php echo htmlspecialchars($song['name_song']); ?>')">
                                    <i class="fas fa-check"></i> Duyệt
                                </button>
                                <button class="btn btn-reject" onclick="rejectSong(<?php echo $song['id']; ?>, '<?php echo htmlspecialchars($song['name_song']); ?>')">
                                    <i class="fas fa-times"></i> Từ Chối
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-songs">
                    <i class="fas fa-check-circle" style="font-size: 3rem; color: #2ed573; margin-bottom: 15px;"></i>
                    <p>Không có bài hát nào chờ duyệt!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Bài hát đã duyệt gần đây -->
        <div class="section">
            <h2><i class="fas fa-check-circle"></i> Bài Hát Đã Duyệt Gần Đây</h2>
            
            <?php if ($approved_result->num_rows > 0): ?>
                <?php while ($song = $approved_result->fetch_assoc()): ?>
                    <div class="song-item">
                        <div class="song-info">
                            <div class="song-details">
                                <h3><?php echo htmlspecialchars($song['name_song']); ?></h3>
                                <div class="song-meta">
                                    <strong>Ca sĩ:</strong> <?php echo htmlspecialchars($song['artist']); ?><br>
                                    <strong>Upload bởi:</strong> <?php echo htmlspecialchars($song['uploader_name']); ?><br>
                                    <strong>Duyệt bởi:</strong> <?php echo htmlspecialchars($song['approver_name']); ?><br>
                                    <strong>Thời gian duyệt:</strong> <?php echo date('d/m/Y H:i', strtotime($song['approved_at'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-songs">
                    <p>Chưa có bài hát nào được duyệt!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Modal từ chối bài hát -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <h3>Từ Chối Bài Hát</h3>
            <form id="rejectForm" method="POST">
                <input type="hidden" name="song_id" id="rejectSongId">
                <input type="hidden" name="action" value="reject">
                
                <div class="form-group">
                    <label for="rejectReason">Lý do từ chối (tùy chọn):</label>
                    <textarea name="reason" id="rejectReason" placeholder="Nhập lý do từ chối bài hát..."></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal()">Hủy</button>
                    <button type="submit" class="btn btn-reject">Từ Chối</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal duyệt bài hát -->
    <div id="approveModal" class="modal">
        <div class="modal-content">
            <h3>Duyệt Bài Hát</h3>
            <form id="approveForm" method="POST">
                <input type="hidden" name="song_id" id="approveSongId">
                <input type="hidden" name="action" value="approve">
                
                <div class="form-group">
                    <label for="approveReason">Ghi chú (tùy chọn):</label>
                    <textarea name="reason" id="approveReason" placeholder="Nhập ghi chú khi duyệt bài hát..."></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal()">Hủy</button>
                    <button type="submit" class="btn btn-approve">Duyệt</button>
                </div>
            </form>
        </div>
    </div>
    
    <?php include("footer.php"); ?>
    
    <script>
        function approveSong(songId, songName) {
            if (confirm(`Bạn có chắc chắn muốn duyệt bài hát "${songName}"?`)) {
                document.getElementById('approveSongId').value = songId;
                document.getElementById('approveModal').style.display = 'block';
            }
        }
        
        function rejectSong(songId, songName) {
            if (confirm(`Bạn có chắc chắn muốn từ chối bài hát "${songName}"?`)) {
                document.getElementById('rejectSongId').value = songId;
                document.getElementById('rejectModal').style.display = 'block';
            }
        }
        
        function closeModal() {
            document.getElementById('approveModal').style.display = 'none';
            document.getElementById('rejectModal').style.display = 'none';
        }
        
        // Đóng modal khi click bên ngoài
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                closeModal();
            }
        }
        
        // Tự động ẩn thông báo sau 5 giây
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>
