<?php
session_start();
require_once 'connect.php';

$error = '';
$success = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Vui lòng điền đầy đủ thông tin!";
    } elseif (strlen($username) < 3 || strlen($username) > 20) {
        $error = "Tên đăng nhập phải từ 3-20 ký tự!";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = "Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới!";
    } elseif (strlen($password) < 6) {
        $error = "Mật khẩu phải có ít nhất 6 ký tự!";
    } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $password)) {
        $error = "Mật khẩu chỉ được chứa chữ cái và số!";
    } elseif ($password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        $check_sql = "SELECT * FROM users WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        if ($check_result->num_rows > 0) {
            $error = "Tên đăng nhập đã tồn tại!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (username, password) VALUES (?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ss", $username, $hashed_password);
            if ($insert_stmt->execute()) {
                $success = "Đăng ký thành công! Vui lòng <a href='login.php' style='color:#2ed573;font-weight:bold;'>đăng nhập</a>.";
            } else {
                $error = "Đăng ký thất bại! Vui lòng thử lại.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - HL Music</title>
    <link rel="stylesheet" href="./Css/login_register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="trang-dang-nhap">
        <div class="khung-dang-nhap">
            <div class="tieu-de">
                <h1>HL Music</h1>
                <p>Đăng ký tài khoản mới</p>
            </div>
            <?php if ($error): ?>
                <div class="thong-bao loi">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="thong-bao thanh-cong">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="nhom-input">
                    <label class="label" for="register-username">Tên đăng nhập</label>
                    <input type="text" id="register-username" name="username" class="input" required
                           pattern="[a-zA-Z0-9_]{3,20}" 
                           title="Tên đăng nhập từ 3-20 ký tự, chỉ bao gồm chữ cái, số và dấu gạch dưới"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    <small class="input-hint">Từ 3-20 ký tự, chỉ bao gồm chữ cái, số và dấu gạch dưới</small>
                </div>
                <div class="nhom-input">
                    <label class="label" for="register-password">Mật khẩu</label>
                    <input type="password" id="register-password" name="password" class="input" required
                           pattern="[a-zA-Z0-9]{6,}"
                           title="Mật khẩu phải có ít nhất 6 ký tự, chỉ bao gồm chữ cái và số">
                    <small class="input-hint">Ít nhất 6 ký tự, chỉ bao gồm chữ cái và số</small>
                </div>
                <div class="nhom-input">
                    <label class="label" for="confirm-password">Xác nhận mật khẩu</label>
                    <input type="password" id="confirm-password" name="confirm_password" class="input" required>
                </div>
                <button type="submit" name="register" class="nut">
                    <i class="fas fa-user-plus"></i> Đăng Ký
                </button>
            </form>
            <div class="lien-ket" style="margin-top: 20px;">
                <p>Bạn đã có tài khoản? <a href="login.php" class="nut-chuyen">Đăng nhập ngay <i class="fas fa-arrow-right"></i></a></p>
            </div>
        </div>
    </div>
    <script>
        // Kiểm tra mật khẩu khớp nhau
        document.getElementById('confirm-password').addEventListener('input', function() {
            const password = document.getElementById('register-password').value;
            const confirmPassword = this.value;
            if (password !== confirmPassword) {
                this.setCustomValidity('Mật khẩu không khớp!');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>