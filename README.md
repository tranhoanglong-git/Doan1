# 🎵 HoangLong Music - Dự án Web Nghe Nhạc

## 📁 Cấu trúc dự án

```
Doan1/
├── assets/           # CSS, JS, hình ảnh tĩnh
│   ├── css/
│   ├── js/
│   └── images/
├── uploads/          # File upload từ người dùng
│   ├── images/      # Ảnh bìa bài hát
│   └── music/       # File nhạc
├── includes/         # File dùng chung
│   ├── config.php   # Kết nối database
│   ├── header.php   # Header chung
│   ├── footer.php   # Footer chung
│   └── functions.php # Các hàm helper
├── pages/            # Các trang thông tin
│   ├── about.php
│   ├── support.php
│   └── search.php
├── auth/             # Xác thực người dùng
│   ├── login.php
│   ├── register.php
│   └── logout.php
├── songs/            # Quản lý bài hát
│   ├── song.php
│   ├── upload.php
│   ├── my_uploads.php
│   ├── delete_song.php
│   ├── favorites.php
│   └── toggle_favorite.php
├── admin/            # Quản trị viên
│   └── approval.php
├── api/              # API (chuẩn bị cho tương lai)
└── index.php         # Trang chủ
```

## 🚀 Cài đặt

1. Clone dự án
2. Import database: `music_web`
3. Cấu hình database trong `includes/config.php`
4. Chạy trên localhost

## 🔐 Tài khoản mặc định

- Admin: username `admin`

## 📝 Tính năng

- ✅ Đăng ký / Đăng nhập
- ✅ Upload bài hát (chờ admin duyệt)
- ✅ Nghe nhạc online
- ✅ Yêu thích bài hát
- ✅ Tìm kiếm bài hát
- ✅ Quản lý bài hát của tôi
- ✅ Admin duyệt bài hát

## 🎯 Kế hoạch phát triển

- 🔜 Tích hợp API Spotify / YouTube Music
- 🔜 AI gợi ý nhạc thông minh
- 🔜 Chatbot hỗ trợ người dùng
- 🔜 Nhận diện giọng nói

## 👨‍💻 Tác giả

- HoangLong
- Email: hwanglong2004@gmail.com
