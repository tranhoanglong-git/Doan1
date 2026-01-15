document.addEventListener('DOMContentLoaded', function() {
    // Các phần tử
    const audioPlayer = document.getElementById('audioPlayer');
    const playBtn = document.getElementById('playBtn');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const thanhTienTrinh = document.getElementById('thanhTienTrinh');
    const tienTrinh = document.getElementById('progress');
    const thanhAmLuong = document.getElementById('thanhAmLuong');
    const currentSongImage = document.getElementById('currentSongImage');
    const currentSongTitle = document.getElementById('currentSongTitle');
    const currentSongArtist = document.getElementById('currentSongArtist');
    const theBaiHat = document.querySelectorAll('.the-bai-hat');
    const trinhPhatNhac = document.getElementById('musicPlayer');
    const togglePlayer = document.getElementById('togglePlayer');
    const toggleMinimize = document.getElementById('toggleMinimize');
    const danhSachBaiHat = document.querySelector('.danh-sach-bai-hat');

    // Biến
    let currentSongIndex = 0;
    let songs = Array.from(theBaiHat);
    let isPlaying = false;
    let isMinimized = false;

    // Khởi tạo âm lượng
    audioPlayer.volume = thanhAmLuong.value / 100;

    // Hiển thị/ẩn trình phát
    function togglePlayerVisibility() {
        trinhPhatNhac.classList.toggle('hien-thi');
        danhSachBaiHat.classList.toggle('co-trinh-phat');
        
        if (trinhPhatNhac.classList.contains('hien-thi')) {
            if (isMinimized) {
                danhSachBaiHat.classList.add('thu-nho');
            }
        } else {
            danhSachBaiHat.classList.remove('co-trinh-phat', 'thu-nho');
        }
    }

    // Thu nhỏ/mở rộng trình phát
    function toggleMinimizePlayer() {
        isMinimized = !isMinimized;
        trinhPhatNhac.classList.toggle('thu-nho');
        danhSachBaiHat.classList.toggle('thu-nho');
        
        // Thay đổi icon
        toggleMinimize.innerHTML = isMinimized ? 
            '<i class="fas fa-expand"></i>' : 
            '<i class="fas fa-minus"></i>';
    }

    // Chức năng phát/tạm dừng
    function togglePlay() {
        if (isPlaying) {
            audioPlayer.pause();
            playBtn.innerHTML = '<i class="fas fa-play"></i>';
        } else {
            audioPlayer.play();
            playBtn.innerHTML = '<i class="fas fa-pause"></i>';
        }
        isPlaying = !isPlaying;
    }

    // Tải và phát bài hát
    function loadSong(index) {
        const song = songs[index];
        const songSrc = song.dataset.song;
        const songImage = song.dataset.image;
        const songTitle = song.dataset.title;
        const songArtist = song.dataset.artist;

        audioPlayer.src = songSrc;
        currentSongImage.src = songImage || '';
        currentSongTitle.textContent = songTitle;
        currentSongArtist.textContent = songArtist;

        // Cập nhật trạng thái active
        songs.forEach(card => card.classList.remove('active'));
        song.classList.add('active');

        // Hiển thị trình phát và phát bài hát
        if (!trinhPhatNhac.classList.contains('hien-thi')) {
            togglePlayerVisibility();
        }
        audioPlayer.play();
        isPlaying = true;
        playBtn.innerHTML = '<i class="fas fa-pause"></i>';
    }

    // Sự kiện
    togglePlayer.addEventListener('click', togglePlayerVisibility);
    toggleMinimize.addEventListener('click', toggleMinimizePlayer);
    playBtn.addEventListener('click', togglePlay);

    prevBtn.addEventListener('click', () => {
        currentSongIndex = (currentSongIndex - 1 + songs.length) % songs.length;
        loadSong(currentSongIndex);
    });

    nextBtn.addEventListener('click', () => {
        currentSongIndex = (currentSongIndex + 1) % songs.length;
        loadSong(currentSongIndex);
    });

    // Cập nhật thanh tiến trình
    audioPlayer.addEventListener('timeupdate', () => {
        const percent = (audioPlayer.currentTime / audioPlayer.duration) * 100;
        thanhTienTrinh.value = percent;
        tienTrinh.style.width = percent + '%';
    });

    // Chức năng tua
    thanhTienTrinh.addEventListener('input', () => {
        const seekTime = (thanhTienTrinh.value / 100) * audioPlayer.duration;
        audioPlayer.currentTime = seekTime;
    });

    // Điều khiển âm lượng
    thanhAmLuong.addEventListener('input', () => {
        audioPlayer.volume = thanhAmLuong.value / 100;
    });

    // Click vào thẻ bài hát
    songs.forEach((card, index) => {
        card.addEventListener('click', () => {
            currentSongIndex = index;
            loadSong(currentSongIndex);
        });
    });

    // Xử lý khi bài hát kết thúc
    audioPlayer.addEventListener('ended', () => {
        currentSongIndex = (currentSongIndex + 1) % songs.length;
        loadSong(currentSongIndex);
    });

    // Điều khiển bằng bàn phím
    document.addEventListener('keydown', (e) => {
        switch(e.code) {
            case 'Space':
                e.preventDefault();
                togglePlay();
                break;
            case 'ArrowLeft':
                currentSongIndex = (currentSongIndex - 1 + songs.length) % songs.length;
                loadSong(currentSongIndex);
                break;
            case 'ArrowRight':
                currentSongIndex = (currentSongIndex + 1) % songs.length;
                loadSong(currentSongIndex);
                break;
        }
    });
});

// Function xóa bài hát (chỉ admin)
function deleteSong(songId, songName) {
    if (!confirm(`Bạn có chắc chắn muốn xóa bài hát "${songName}"?\n\nLưu ý: Hành động này không thể hoàn tác!`)) {
        return;
    }
    
    // Tạo form data
    const formData = new FormData();
    formData.append('song_id', songId);
    formData.append('ajax', '1');
    
    // Gửi request xóa
    fetch('delete_song.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hiển thị thông báo thành công
            showNotification(data.message, 'success');
            
            // Xóa element khỏi DOM
            const songElement = document.querySelector(`[data-song-id="${songId}"]`).closest('.the-bai-hat');
            if (songElement) {
                songElement.style.opacity = '0';
                songElement.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    songElement.remove();
                    
                    // Cập nhật lại danh sách bài hát
                    const songs = document.querySelectorAll('.the-bai-hat');
                    if (songs.length === 0) {
                        document.querySelector('.luoi-bai-hat').innerHTML = 
                            '<p class="khong-co-bai-hat">Chưa có bài hát nào được thêm vào.</p>';
                    }
                }, 300);
            }
        } else {
            // Hiển thị thông báo lỗi
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi xóa bài hát!', 'error');
    });
}

// Function hiển thị thông báo
function showNotification(message, type) {
    const notification = document.getElementById('notification');
    if (notification) {
        notification.textContent = message;
        notification.className = `notification ${type} show`;
        
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }
}
