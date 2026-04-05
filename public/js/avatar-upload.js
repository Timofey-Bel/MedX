document.addEventListener('DOMContentLoaded', function() {
    const avatarCameras = document.querySelectorAll('.avatar-camera');
    
    avatarCameras.forEach(camera => {
        camera.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            
            input.onchange = async function(e) {
                const file = e.target.files[0];
                if (!file) return;
                
                if (file.size > 2048 * 1024) {
                    alert('Файл слишком большой. Максимальный размер: 2MB');
                    return;
                }
                
                const formData = new FormData();
                formData.append('avatar', file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                
                try {
                    const response = await fetch('/profile/avatar', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        location.reload();
                    }
                } catch (error) {
                    alert('Ошибка при загрузке аватарки');
                }
            };
            
            input.click();
        });
    });
});
