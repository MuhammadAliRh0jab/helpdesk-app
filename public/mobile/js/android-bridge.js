var AndroidBridge = {
    openCamera: function(callback) {
        try {
            if (typeof window.Android === 'undefined') {
                throw new Error('Android interface not available');
            }
            window.Android.openCamera(callback);
        } catch (e) {
            console.error(e.message);
            document.getElementById('errorMessage').innerText = "Error: " + e.message;
        }
    },
    showImagePreview: function(base64Image) {
        try {
            // Tampilkan preview
            document.getElementById('imagePreview').src = base64Image;
            document.getElementById('imagePreview').style.display = 'block';
            // Simpan Base64 di input tersembunyi
            document.getElementById('imageBase64Input').value = base64Image.split(',')[1]; // Hanya ambil bagian Base64 tanpa prefix
        } catch (e) {
            console.error(e.message);
            document.getElementById('errorMessage').innerText = "Error: " + e.message;
        }
    }
};
