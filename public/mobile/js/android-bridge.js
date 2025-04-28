var AndroidBridge = {
    openCamera: function (callback) {
        try {
            if (typeof window.Android === "undefined") {
                throw new Error("Android interface not available");
            }
            console.log("Calling Android.openCamera with callback:", callback);
            window.Android.openCamera(callback);
        } catch (e) {
            console.error("Error opening camera:", e.message);
            const errorElement = document.getElementById("errorMessage");
            if (errorElement) {
                errorElement.innerText = "Error: " + e.message;
            }
        }
    },
    openPhotoPicker: function () {
        try {
            if (typeof window.Android === "undefined") {
                throw new Error("Android interface not available");
            }
            console.log("Calling Android.openPhotoPicker");
            window.Android.openPhotoPicker();
        } catch (e) {
            console.error("Error opening photo picker:", e.message);
            const errorElement = document.getElementById("errorMessage");
            if (errorElement) {
                errorElement.innerText = "Error: " + e.message;
            }
        }
    },
    showImagePreview: function (base64Image) {
        try {
            if (typeof window.showImagePreview === "undefined") {
                throw new Error("showImagePreview function not defined");
            }
            console.log(
                "AndroidBridge.showImagePreview called with base64 length:",
                base64Image.length
            );
            window.showImagePreview(base64Image);
        } catch (e) {
            console.error("Error showing image preview:", e.message);
            const errorElement = document.getElementById("errorMessage");
            if (errorElement) {
                errorElement.innerText = "Error: " + e.message;
            }
        }
    },
};
