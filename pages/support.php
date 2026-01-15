<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hỗ Trợ</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<style>
.support-container {
    max-width: 600px;
    margin: 40px auto;
    background: rgba(30, 30, 30, 0.95);
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.4);
    padding: 32px 24px 24px 24px;
    color: #fff;
}

.support-container h1 {
    text-align: center;
    margin-bottom: 18px;
    font-size: 2.2rem;
    letter-spacing: 1px;
    color: #ff6b6b;
}

.support-container h2 {
    margin-top: 32px;
    margin-bottom: 12px;
    font-size: 1.3rem;
    color: #ffd166;
}

.support-content h3 {
    margin-top: 18px;
    color: #4ecdc4;
    font-size: 1.1rem;
}

.support-content p {
    margin-bottom: 10px;
    color: #eee;
    font-size: 1rem;
}

.support-form {
    margin-top: 10px;
}

.support-form form {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.support-form input[type="text"],
.support-form input[type="email"],
.support-form textarea {
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid #444;
    background: #181818;
    color: #fff;
    font-size: 1rem;
    transition: border 0.2s;
}

.support-form input[type="text"]:focus,
.support-form input[type="email"]:focus,
.support-form textarea:focus {
    border: 1.5px solid #ff6b6b;
    outline: none;
}

.support-form textarea {
    min-height: 90px;
    resize: vertical;
}

.support-form input[type="submit"] {
    background: linear-gradient(90deg, #ff6b6b, #ffd166);
    color: #222;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    padding: 12px 0;
    font-size: 1.1rem;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
    margin-top: 6px;
}

.support-form input[type="submit"]:hover {
    background: linear-gradient(90deg, #ffd166, #ff6b6b);
    color: #111;
}

@media (max-width: 700px) {
    .support-container {
        padding: 18px 6vw 18px 6vw;
        margin: 18px 0;
    }
} 
</style>
<body>
    <?php
        include("../includes/header.php");
        $success = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['message'])) {
            // Ở đây bạn có thể xử lý lưu thông tin hoặc gửi email nếu muốn
            $success = true;
        }
    ?>
    <div class="support-container">
        <h1>Hỗ Trợ</h1>
        <p>Nếu bạn có bất kỳ câu hỏi hoặc cần hỗ trợ, vui lòng liên hệ với chúng tôi.</p>
        <div class="support-content">
            <h3>Bài hát yêu thích của tôi ở đâu?</h3>
            <p>Bạn có thể xem lại trong mục "Yêu thích" ở thanh menu trên cùng (nếu đã đăng nhập).</p>

            <h3>Lỗi không phát được nhạc?</h3>
            <p>Hãy kiểm tra kết nối mạng, thử tải lại trang hoặc chuyển sang trình duyệt khác.</p>
        </div>
        <h2>Gửi yêu cầu hỗ trợ</h2>
        <div class="support-form">

            <form action="support.php" method="post">
                <input type="text" name="name" placeholder="Tên của bạn">
                <input type="email" name="email" placeholder="Email của bạn">
                <textarea name="message" placeholder="Mô tả vấn đề mà bạn gặp phải..."></textarea>
                <input type="submit" value="Gửi hỗ trợ">
                <?php if ($success): ?>
                <div style="background:#4ecdc4;color:#222;padding:12px 18px;border-radius:8px;margin-bottom:14px;text-align:center;font-weight:bold;">Gửi thành công! Bạn sẽ được hỗ trợ sớm nhất.</div>
            <?php endif; ?>
            </form>
        </div>
    </div>
    <?php
        include("../includes/footer.php");
    ?>
</body>

</html>
