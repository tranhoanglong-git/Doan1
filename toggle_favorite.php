<?php
session_start();
include("connect.php");

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $song_id = $_POST['song_id'] ?? null;
    $action = $_POST['action'] ?? null; // 'add' hoặc 'remove'
    
    if (!$song_id) {
        echo json_encode(['success' => false, 'message' => 'Thiếu ID bài hát']);
        exit();
    }
    
    if ($action === 'add') {
        // Kiểm tra xem bài hát đã có trong danh sách yêu thích chưa
        $check_sql = "SELECT id FROM favorites WHERE user_id = ? AND song_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $user_id, $song_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Bài hát đã có trong danh sách yêu thích']);
            exit();
        }
        
        // Thêm vào danh sách yêu thích
        $sql = "INSERT INTO favorites (user_id, song_id, created_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $song_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Đã thêm vào danh sách yêu thích']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi thêm vào danh sách yêu thích']);
        }
        
    } elseif ($action === 'remove') {
        // Xóa khỏi danh sách yêu thích
        $sql = "DELETE FROM favorites WHERE user_id = ? AND song_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $song_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Đã xóa khỏi danh sách yêu thích']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa khỏi danh sách yêu thích']);
        }
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
}
?> 