<?php
session_start();

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Cho phép tất cả user đăng nhập upload bài hát

// Xử lý upload khi form được submit
$thong_bao = '';
$loai_thong_bao = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "music_web";

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Lấy thông tin từ form
        $ten_bai_hat = trim($_POST['ten_bai_hat']);
        $ten_ca_si = trim($_POST['ten_ca_si']);

        // Kiểm tra dữ liệu đầu vào
        if (empty($ten_bai_hat) || empty($ten_ca_si)) {
            throw new Exception("Vui lòng điền đầy đủ tên bài hát và tên ca sĩ!");
        }

        $duong_dan_anh = '';
        $duong_dan_nhac = '';

        // Xử lý upload ảnh
        if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
            $file_anh = $_FILES['hinh_anh'];
            $ten_file_anh = $file_anh['name'];
            $duong_dan_tam_anh = $file_anh['tmp_name'];
            $kich_thuoc_anh = $file_anh['size'];

            // Kiểm tra định dạng ảnh
            $dinh_dang_anh = strtolower(pathinfo($ten_file_anh, PATHINFO_EXTENSION));
            $dinh_dang_cho_phep_anh = array('jpg', 'jpeg', 'png', 'gif', 'webp');

            if (!in_array($dinh_dang_anh, $dinh_dang_cho_phep_anh)) {
                throw new Exception("Chỉ chấp nhận file ảnh định dạng: " . implode(', ', $dinh_dang_cho_phep_anh));
            }

            // Kiểm tra kích thước ảnh (5MB)
            if ($kich_thuoc_anh > 5 * 1024 * 1024) {
                throw new Exception("Kích thước ảnh không được vượt quá 5MB!");
            }

            // Tạo tên file mới để tránh trùng lặp
            $ten_file_moi_anh = uniqid() . '_' . time() . '.' . $dinh_dang_anh;
            $duong_dan_anh = 'upload/images/' . $ten_file_moi_anh;

            // Tạo thư mục nếu chưa tồn tại
            if (!file_exists('upload/images/')) {
                mkdir('upload/images/', 0777, true);
            }

            // Di chuyển file ảnh
            if (!move_uploaded_file($duong_dan_tam_anh, $duong_dan_anh)) {
                throw new Exception("Không thể upload ảnh!");
            }
        }

        // Xử lý upload nhạc
        if (isset($_FILES['file_nhac']) && $_FILES['file_nhac']['error'] == 0) {
            $file_nhac = $_FILES['file_nhac'];
            $ten_file_nhac = $file_nhac['name'];
            $duong_dan_tam_nhac = $file_nhac['tmp_name'];
            $kich_thuoc_nhac = $file_nhac['size'];

            // Kiểm tra định dạng nhạc
            $dinh_dang_nhac = strtolower(pathinfo($ten_file_nhac, PATHINFO_EXTENSION));
            $dinh_dang_cho_phep_nhac = array('mp3', 'wav', 'ogg', 'm4a');

            if (!in_array($dinh_dang_nhac, $dinh_dang_cho_phep_nhac)) {
                throw new Exception("Chỉ chấp nhận file nhạc định dạng: " . implode(', ', $dinh_dang_cho_phep_nhac));
            }

            // Kiểm tra kích thước nhạc (50MB)
            if ($kich_thuoc_nhac > 50 * 1024 * 1024) {
                throw new Exception("Kích thước file nhạc không được vượt quá 50MB!");
            }

            // Tạo tên file mới
            $ten_file_moi_nhac = uniqid() . '_' . time() . '.' . $dinh_dang_nhac;
            $duong_dan_nhac = 'upload/audio/' . $ten_file_moi_nhac;

            // Tạo thư mục nếu chưa tồn tại
            if (!file_exists('upload/audio/')) {
                mkdir('upload/audio/', 0777, true);
            }

            // Di chuyển file nhạc
            if (!move_uploaded_file($duong_dan_tam_nhac, $duong_dan_nhac)) {
                throw new Exception("Không thể upload file nhạc!");
            }
        } else {
            throw new Exception("Vui lòng chọn file nhạc để upload!");
        }

        // Lưu vào database với thông tin user upload
        $sql = "INSERT INTO songs (name_song, artist, avt_song, file_song, user_id, status) VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = $pdo->prepare($sql);

        // Lưu bài hát với trạng thái chờ duyệt
        $stmt->execute([$ten_bai_hat, $ten_ca_si, $duong_dan_anh, $duong_dan_nhac, $_SESSION['user_id']]);

        $thong_bao = "Upload bài hát thành công! Bài hát đang chờ admin duyệt.";
        $loai_thong_bao = "thanh_cong";

        // Reset form
        $_POST = array();
    } catch (Exception $e) {
        $thong_bao = "Lỗi: " . $e->getMessage();
        $loai_thong_bao = "loi";

        // Xóa file đã upload nếu có lỗi
        if (!empty($duong_dan_anh) && file_exists($duong_dan_anh)) {
            unlink($duong_dan_anh);
        }
        if (!empty($duong_dan_nhac) && file_exists($duong_dan_nhac)) {
            unlink($duong_dan_nhac);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bài Hát</title>
    <link rel="stylesheet" href="./Css/upload.css">
</head>

<body>
    <?php include("header.php"); ?>

    <!-- Nội dung chính -->
    <div class="trang-upload">
        <h1 class="tieu-de">UPLOAD BÀI HÁT</h1>
        


        <!-- Thông báo -->
        <?php if (!empty($thong_bao)): ?>
            <div class="thong-bao <?php echo $loai_thong_bao; ?>">
                <svg class="icon-thong-bao" viewBox="0 0 24 24" fill="currentColor">
                    <?php if ($loai_thong_bao == 'thanh_cong'): ?>
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                    <?php else: ?>
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                    <?php endif; ?>
                </svg>
                <?php echo $thong_bao; ?>
            </div>
        <?php endif; ?>

        <div class="container-upload">
            <!-- Form upload -->
            <div class="form-upload">
                <h2 class="tieu-de-form">Thêm Bài Hát Mới</h2>

                <form method="POST" enctype="multipart/form-data" class="form">
                    <div class="nhom-input">
                        <label for="ten_bai_hat" class="label">Tên Bài Hát</label>
                        <input type="text" id="ten_bai_hat" name="ten_bai_hat"
                            value="<?php echo isset($_POST['ten_bai_hat']) ? htmlspecialchars($_POST['ten_bai_hat']) : ''; ?>"
                            class="input" placeholder="Nhập tên bài hát..." required>
                    </div>

                    <div class="nhom-input">
                        <label for="ten_ca_si" class="label">Tên Ca Sĩ</label>
                        <input type="text" id="ten_ca_si" name="ten_ca_si"
                            value="<?php echo isset($_POST['ten_ca_si']) ? htmlspecialchars($_POST['ten_ca_si']) : ''; ?>"
                            class="input" placeholder="Nhập tên ca sĩ..." required>
                    </div>



                    <div class="nhom-input">
                        <label for="hinh_anh" class="label">Ảnh Bìa (Tùy chọn)</label>
                        <div class="hop-file">
                            <input type="file" id="hinh_anh" name="hinh_anh"
                                accept="image/*" class="input-file">
                            <label for="hinh_anh" class="label-file">
                                <svg class="icon-upload" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
                                </svg>
                                Chọn ảnh bìa
                            </label>
                            <span class="ten-file" id="tenFileAnh">Chưa chọn file</span>
                        </div>
                        <small class="ghi-chu">Định dạng: JPG, PNG, GIF, WEBP. Tối đa 5MB</small>
                    </div>

                    <div class="nhom-input">
                        <label for="file_nhac" class="label">File Nhạc *</label>
                        <div class="hop-file">
                            <input type="file" id="file_nhac" name="file_nhac"
                                accept="audio/*" class="input-file" required>
                            <label for="file_nhac" class="label-file">
                                <svg class="icon-upload" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z" />
                                </svg>
                                Chọn file nhạc
                            </label>
                            <span class="ten-file" id="tenFileNhac">Chưa chọn file</span>
                        </div>
                        <small class="ghi-chu">Định dạng: MP3, WAV, OGG, M4A. Tối đa 50MB</small>
                    </div>

                    <div class="nhom-nut">
                        <button type="submit" class="nut nut-upload">
                            <svg class="icon-nut" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z" />
                            </svg>
                            Upload Bài Hát
                        </button>

                        <button type="reset" class="nut nut-reset">
                            <svg class="icon-nut" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zM7 13c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm5 0c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm5 0c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1z" />
                            </svg>
                            Đặt Lại
                        </button>
                    </div>
                </form>
            </div>

            <!-- Preview -->
            <div class="preview">
                <h3 class="tieu-de-preview">Xem Trước</h3>
                <div class="hop-preview">
                    <div class="anh-preview" id="anhPreview">
                        🎵
                    </div>
                    <div class="thong-tin-preview">
                        <div class="ten-bai-preview" id="tenBaiPreview">Tên bài hát</div>
                        <div class="ten-ca-si-preview" id="tenCaSiPreview">Tên ca sĩ</div>
                    </div>
                </div>
                <div class="audio-preview" id="audioPreview" style="display: none;">
                    <audio controls class="player-preview">
                        <source id="sourcePreview" src="" type="audio/mpeg">
                        Trình duyệt của bạn không hỗ trợ phát nhạc.
                    </audio>
                </div>
            </div>
        </div>
    </div>

    <?php include("footer.php"); ?>
    <script src="./js/upload.js"></script>
</body>

</html>