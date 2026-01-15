<?php
session_start();
require_once '../includes/config.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Kiểm tra quyền admin
if ($_SESSION['username'] !== 'admin') {
    header("Location: ../index.php?error=access_denied");
    exit();
}

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['song_id'])) {
    $song_id = intval($_POST['song_id']);
    
    try {
        // Lấy thông tin bài hát trước khi xóa để xóa file
        $sql = "SELECT avt_song, file_song FROM songs WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $song_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $song = $result->fetch_assoc();
            
            // Xóa bài hát khỏi database
            $delete_sql = "DELETE FROM songs WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $song_id);
            
            if ($delete_stmt->execute()) {
                // Xóa file ảnh nếu tồn tại
                if (!empty($song['avt_song']) && file_exists($song['avt_song'])) {
                    unlink($song['avt_song']);
                }
                
                // Xóa file nhạc nếu tồn tại
                if (!empty($song['file_song']) && file_exists($song['file_song'])) {
                    unlink($song['file_song']);
                }
                
                // Xóa khỏi bảng favorites nếu có
                $delete_fav_sql = "DELETE FROM favorites WHERE song_id = ?";
                $delete_fav_stmt = $conn->prepare($delete_fav_sql);
                $delete_fav_stmt->bind_param("i", $song_id);
                $delete_fav_stmt->execute();
                
                $response['success'] = true;
                $response['message'] = 'Xóa bài hát thành công!';
            } else {
                $response['message'] = 'Lỗi khi xóa bài hát khỏi database!';
            }
        } else {
            $response['message'] = 'Bài hát không tồn tại!';
        }
    } catch (Exception $e) {
        $response['message'] = 'Lỗi: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Dữ liệu không hợp lệ!';
}

// Trả về JSON response cho AJAX hoặc redirect cho form thường
if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Redirect với thông báo
    if ($response['success']) {
        header("Location: song.php?success=" . urlencode($response['message']));
    } else {
        header("Location: song.php?error=" . urlencode($response['message']));
    }
}
exit();
?>
