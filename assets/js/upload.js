document.addEventListener('DOMContentLoaded', function() {
    // Các element cần thiết
    const inputTenBaiHat = document.getElementById('ten_bai_hat');
    const inputTenCaSi = document.getElementById('ten_ca_si');
    const inputHinhAnh = document.getElementById('hinh_anh');
    const inputFileNhac = document.getElementById('file_nhac');
    
    const tenFileAnh = document.getElementById('tenFileAnh');
    const tenFileNhac = document.getElementById('tenFileNhac');
    
    const tenBaiPreview = document.getElementById('tenBaiPreview');
    const tenCaSiPreview = document.getElementById('tenCaSiPreview');
    const anhPreview = document.getElementById('anhPreview');
    const audioPreview = document.getElementById('audioPreview');
    const sourcePreview = document.getElementById('sourcePreview');
    const playerPreview = document.querySelector('.player-preview');
    
    const form = document.querySelector('.form');
    const nutUpload = document.querySelector('.nut-upload');
    
    // Cập nhật preview khi nhập tên bài hát
    inputTenBaiHat.addEventListener('input', function() {
        const tenBai = this.value.trim();
        tenBaiPreview.textContent = tenBai || 'Tên bài hát';
    });
    
    // Cập nhật preview khi nhập tên ca sĩ
    inputTenCaSi.addEventListener('input', function() {
        const tenCaSi = this.value.trim();
        tenCaSiPreview.textContent = tenCaSi || 'Tên ca sĩ';
    });
    
    // Xử lý chọn file nhạc
    inputFileNhac.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Cập nhật tên file
            tenFileNhac.textContent = file.name;
            
            // Kiểm tra định dạng
            const allowedTypes = ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp4'];
            if (!allowedTypes.includes(file.type)) {
                alert('Chỉ chấp nhận file nhạc định dạng: MP3, WAV, OGG, M4A');
                inputFileNhac.value = '';
                tenFileNhac.textContent = 'Chưa chọn file';
                audioPreview.style.display = 'none';
                return;
            }
            
            // Kiểm tra kích thước (50MB)
            if (file.size > 50 * 1024 * 1024) {
                alert('Kích thước file nhạc không được vượt quá 50MB');
                inputFileNhac.value = '';
                tenFileNhac.textContent = 'Chưa chọn file';
                audioPreview.style.display = 'none';
                return;
            }
            
            // Tạo URL để xem trước
            const url = URL.createObjectURL(file);
            sourcePreview.src = url;
            playerPreview.load();
            audioPreview.style.display = 'block';
        } else {
            tenFileNhac.textContent = 'Chưa chọn file';
            audioPreview.style.display = 'none';
        }
    });
    
    // Xử lý submit form
    form.addEventListener('submit', function(e) {
        const tenBaiHat = inputTenBaiHat.value.trim();
        const tenCaSi = inputTenCaSi.value.trim();
        const fileNhac = inputFileNhac.files[0];
        
        // Kiểm tra dữ liệu bắt buộc
        if (!tenBaiHat) {
            alert('Vui lòng nhập tên bài hát');
            inputTenBaiHat.focus();
            e.preventDefault();
            return;
        }
        
        if (!tenCaSi) {
            alert('Vui lòng nhập tên ca sĩ');
            inputTenCaSi.focus();
            e.preventDefault();
            return;
        }
        
        if (!fileNhac) {
            alert('Vui lòng chọn file nhạc');
            inputFileNhac.focus();
            e.preventDefault();
            return;
        }
        
        // Hiển thị loading
        nutUpload.innerHTML = `
            <svg class="icon-nut loading" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z"/>
            </svg>
            Đang upload...
        `;
        nutUpload.disabled = true;
    });
    
    // Xử lý reset form
    const nutReset = document.querySelector('.nut-reset');
    nutReset.addEventListener('click', function() {
        // Reset các input
        inputTenBaiHat.value = '';
        inputTenCaSi.value = '';
        inputHinhAnh.value = '';
        inputFileNhac.value = '';

        // Reset preview
        tenBaiPreview.textContent = 'Tên bài hát';
        tenCaSiPreview.textContent = 'Tên ca sĩ';
        anhPreview.innerHTML = '🎵';
        audioPreview.style.display = 'none';
        tenFileAnh.textContent = 'Chưa chọn file';
        tenFileNhac.textContent = 'Chưa chọn file';
    });
    
    // Drag and drop cho file ảnh
    const labelFileAnh = document.querySelector('label[for="hinh_anh"]');
    
    labelFileAnh.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = '#ff6b6b';
        this.style.background = 'rgba(255, 107, 107, 0.4)';
    });
    
    labelFileAnh.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.style.borderColor = 'rgba(255, 107, 107, 0.5)';
        this.style.background = 'rgba(255, 107, 107, 0.2)';
    });
    
    labelFileAnh.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = 'rgba(255, 107, 107, 0.5)';
        this.style.background = 'rgba(255, 107, 107, 0.2)';
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            if (file.type.startsWith('image/')) {
                inputHinhAnh.files = files;
                inputHinhAnh.dispatchEvent(new Event('change'));
            } else {
                alert('Vui lòng chỉ kéo thả file ảnh');
            }
        }
    });
    
    // Drag and drop cho file nhạc
    const labelFileNhac = document.querySelector('label[for="file_nhac"]');
    
    labelFileNhac.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = '#ff6b6b';
        this.style.background = 'rgba(255, 107, 107, 0.4)';
    });
    
    labelFileNhac.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.style.borderColor = 'rgba(255, 107, 107, 0.5)';
        this.style.background = 'rgba(255, 107, 107, 0.2)';
    });
    
    labelFileNhac.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = 'rgba(255, 107, 107, 0.5)';
        this.style.background = 'rgba(255, 107, 107, 0.2)';
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            if (file.type.startsWith('audio/') || file.name.toLowerCase().match(/\.(mp3|wav|ogg|m4a)$/)) {
                inputFileNhac.files = files;
                inputFileNhac.dispatchEvent(new Event('change'));
            } else {
                alert('Vui lòng chỉ kéo thả file nhạc');
            }
        }
    });
    
    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Hiển thị thông tin file khi hover
    inputHinhAnh.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            // Cập nhật tên file
            tenFileAnh.textContent = file.name;

            // Kiểm tra định dạng
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Chỉ chấp nhận file ảnh định dạng: JPG, PNG, GIF, WEBP');
                this.value = '';
                tenFileAnh.textContent = 'Chưa chọn file';
                anhPreview.innerHTML = '🎵';
                return;
            }

            // Kiểm tra kích thước (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Kích thước ảnh không được vượt quá 5MB');
                this.value = '';
                tenFileAnh.textContent = 'Chưa chọn file';
                anhPreview.innerHTML = '🎵';
                return;
            }

            // Hiển thị preview ảnh
            const reader = new FileReader();
            reader.onload = function(e) {
                anhPreview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            };
            reader.readAsDataURL(file);
        } else {
            tenFileAnh.textContent = 'Chưa chọn file';
            anhPreview.innerHTML = '🎵';
        }
    });
    
    inputFileNhac.addEventListener('change', function() {
        if (this.files[0]) {
            const file = this.files[0];
            const size = formatFileSize(file.size);
            tenFileNhac.textContent = `${file.name} (${size})`;
        }
    });
    
    // Auto-hide thông báo sau 5 giây
    const thongBao = document.querySelector('.thong-bao');
    if (thongBao) {
        setTimeout(function() {
            thongBao.style.opacity = '0';
            thongBao.style.transform = 'translateY(-20px)';
            setTimeout(function() {
                thongBao.style.display = 'none';
            }, 300);
        }, 5000);
    }
    
    // Validate form real-time
    function validateForm() {
        const tenBaiHat = inputTenBaiHat.value.trim();
        const tenCaSi = inputTenCaSi.value.trim();
        const fileNhac = inputFileNhac.files[0];
        
        const isValid = tenBaiHat && tenCaSi && fileNhac;
        nutUpload.disabled = !isValid;
        
        if (isValid) {
            nutUpload.style.opacity = '1';
            nutUpload.style.cursor = 'pointer';
        } else {
            nutUpload.style.opacity = '0.6';
            nutUpload.style.cursor = 'not-allowed';
        }
    }
    
    // Gắn validate cho các input
    inputTenBaiHat.addEventListener('input', validateForm);
    inputTenCaSi.addEventListener('input', validateForm);
    inputFileNhac.addEventListener('change', validateForm);
    
    // Validate ban đầu
    validateForm();
});