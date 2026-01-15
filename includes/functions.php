<?php
// File này để chứa các hàm helper dùng chung
// Hiện tại để trống, sẽ dùng trong tương lai cho API/AI

function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url) {
    header("Location: $url");
    exit();
}
?>
