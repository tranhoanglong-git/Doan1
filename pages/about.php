<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Về Chúng Tôi</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <style>
    .about-container {
        max-width: 700px;
        margin: 60px auto 0 auto;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 107, 107, 0.15);
        border-radius: 18px;
        box-shadow: 0 4px 24px 0 rgba(255, 107, 107, 0.08);
        padding: 40px 32px 32px 32px;
        color: #fff;
        font-size: 1.1rem;
        line-height: 1.7;
        position: relative;
    }
    .about-container h1 {
        font-size: 2.2rem;
        color: #ff6b6b;
        margin-bottom: 18px;
        text-align: center;
        letter-spacing: 1px;
    }
    .about-container h2 {
        color: #ffa502;
        margin-top: 28px;
        margin-bottom: 10px;
        font-size: 1.3rem;
        letter-spacing: 0.5px;
    }
    .about-container p {
        margin-bottom: 10px;
    }
    .about-container a {
        color: #1e90ff;
        text-decoration: none;
        transition: color 0.2s;
    }
    .about-container a:hover {
        color: #ff6b6b;
        text-decoration: underline;
    }
    @media (max-width: 768px) {
        .about-container {
            padding: 24px 10px 18px 10px;
        }
        .about-container h1 {
            font-size: 1.5rem;
        }
    }
    </style>
</head>
<?php
        include("../includes/header.php");
    ?>
<body>
    <div class="about-container">
    <h1>Về Chúng Tôi</h1>
    <p>Website nghe nhạc của chúng tôi được thành lập với mục tiêu tạo ra một không gian giải trí hiện đại, thân thiện, 
    và đậm chất cá nhân dành cho người yêu nhạc Việt. </p>
    <h2>Người Phát Triển</h2>
    <p>Trần Hoàng Long</p>
    <p>Sinh năm: 2004</p>
    <p>Quê quán: Ninh Kiều, Cần Thơ</p>
    <p>Số điện thoại: 03979819747</p>
    <p>Email: hwanglong2004@gmail.com</p>
    <p>Facebook: <a href="https://www.facebook.com/hoang.long.600641/" target="_blank">Trần Hoàng Long</a></p>
    </div>
    <?php
        include("../includes/footer.php");
    ?>
</body>
</html>
