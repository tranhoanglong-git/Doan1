<?php
session_start();
require_once '../includes/config.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    if (empty($username) || empty($password)) {
        $error = "Vui lòng điền đầy đủ thông tin!";
    } else {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: ../index.php");
                exit();
            } else {
                $error = "Mật khẩu không chính xác!";
            }
        } else {
            $error = "Tên đăng nhập không tồn tại!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - HL Music</title>
    <link rel="stylesheet" href="../assets/css/login_register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="trang-dang-nhap">
        <div class="khung-dang-nhap">
            <div class="tieu-de">
                <h1>HL Music</h1>
                <p>Đăng nhập để tiếp tục</p>
            </div>
            <?php if ($error): ?>
                <div class="thong-bao loi">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="nhom-input">
                    <label class="label" for="login-username">Tên đăng nhập</label>
                    <input type="text" id="login-username" name="username" class="input" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                <div class="nhom-input">
                    <label class="label" for="login-password">Mật khẩu</label>
                    <input type="password" id="login-password" name="password" class="input" required>
                </div>
                <button type="submit" name="login" class="nut">
                    <i class="fas fa-sign-in-alt"></i> Đăng Nhập
                </button>
            </form>
            <div class="lien-ket">
                <p>Bạn chưa có tài khoản? <a href="register.php" class="nut-chuyen">Đăng ký ngay <i class="fas fa-arrow-right"></i></a></p>
            </div>
        </div>
    </div>
</body>
</html>
