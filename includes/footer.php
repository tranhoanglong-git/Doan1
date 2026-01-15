<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HL Music Footer</title>
    <link rel="stylesheet" href="../assets/css/footer.css">
</head>

<body>
    </div> 
    <footer>
        <div class="footer-container">
            <div class="contact">
                <ul>Cộng Tác</ul>
                <li><a href="#">hwanglong2004@gmail.com</a></li>
                <li><a href="#">SĐT: 03979819747</a></li>
                <li><a href="https://www.google.com/maps/place/Ninh+Ki%E1%BB%81u,+C%E1%BA%A7n+Th%C6%A1/@10.0320325,105.7608596,13z/data=!3m1!4b1!4m6!3m5!1s0x31a0883fbc944b83:0x77fc34233e5e1320!8m2!3d10.0280487!4d105.7644596!16s%2Fm%2F03mbjcs?entry=ttu&g_ep=EgoyMDI1MDczMC4wIKXMDSoASAFQAw%3D%3D" target="_blank">Địa chỉ: Ninh Kiều, Cần Thơ</a></li>
            </div>
            <div class="support">
                <ul>Liên Kết Nhanh</ul>
                <li><a href="../index.php">Trang chủ</a></li>
                <li><a href="../songs/song.php">Bài Hát</a></li>
                <li><a href="../pages/about.php">Về Chúng Tôi</a></li>
                <li><a href="../pages/support.php">Hỗ Trợ</a></li>
            </div>
            <div class="social">
                <ul>Kết Nối</ul>
                <li><a href="https://www.facebook.com/hoang.long.600641/" target="_blank">Facebook</a></li>             
                <li><a href="https://www.pinterest.com/hoanglong2004/" target="_blank">Pinterest</a></li>
                <li><a href="https://www.tiktok.com/@hlong52" target="_blank">TikTok</a></li>
                <li><a href="https://www.youtube.com/@longhoangdeptrai" target="_blank">YouTube</a></li>
            </div>

            <div class="infomation">
    <div>Đăng kí để nhận thông tin mới</div>
    <input type="text" id="email-footer" placeholder="Email của bạn...">  
    <input type="submit" id="btn-dang-ky-footer" value="Đăng Ký">
    <div class="thong-bao-dang-ky" style="margin-top:10px;color:#2ed573;font-weight:bold;"></div>
</div>
        </div>
        <div class="conclusion">
            HL Music - Âm nhạc đến từ tương lai
        </div>
    </footer>
</body>

<script>
document.getElementById('btn-dang-ky-footer').onclick = function() {
    var thongBao = document.querySelector('.thong-bao-dang-ky');
    thongBao.textContent = 'Đăng ký thành công!';
    document.getElementById('email-footer').value = '';
    setTimeout(function() {
        thongBao.textContent = '';
    }, 3000);
    return false;
};
</script>

</html>
