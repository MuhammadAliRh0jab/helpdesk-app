$(document).ready(function () {
    // Handle file input display for all tickets
    $('[id^="custom-button-"]').each(function () {
        var ticketId = this.id.replace('custom-button-', '');
        $(this).on('click', function () {
            $('#images-' + ticketId).trigger('click');
        });
    });

    $('[id^="images-"]').each(function () {
        var ticketId = this.id.replace('images-', '');
        $(this).on('change', function () {
            var files = this.files;
            var fileNameDisplay = $('#file-name-' + ticketId);
            fileNameDisplay.text(files.length > 0 ?
                (files.length > 1 ? files.length + ' file dipilih' : files[0].name) :
                'Tidak ada file dipilih');
        });
    });

    // Handle AJAX form submission for chat forms (used in assigned.blade.php)
    $('[id^="chat-form-"]').on('submit', function (e) {
        e.preventDefault();
        var ticketId = this.id.replace('chat-form-', '');
        var form = this;
        var formData = new FormData(form);
        var chatContainer = $('#chat-container-' + ticketId);

        // Remove any existing error messages
        chatContainer.find('.alert-danger').remove();

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.success) {
                    var user = data.user;
                    var message = formData.get('message');
                    var createdAt = new Date().toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    var isSender = user.id === data.auth_user_id;
                    var bgColor = isSender ? 'rgb(84, 163, 242)' : 'rgb(54, 56, 59)';

                    // Handle uploaded images
                    var imagesHtml = '';
                    if (data.uploads && data.uploads.length > 0) {
                        data.uploads.forEach(function (upload) {
                            imagesHtml += `
                                <a href="/storage/${upload.filename_path}" target="_blank">
                                    <img src="/storage/${upload.filename_path}" class="img-thumbnail mt-1" style="width: 128px; height: 128px; object-fit: cover;">
                                </a>`;
                        });
                    }

                    var messageHtml = `
                        <div class="message-wrapper mb-3" style="display: flex; flex-direction: column; align-items: ${isSender ? 'flex-end' : 'flex-start'};">
                            <p class="text-dark mb-1">
                                <strong>${user.username} (${user.role_id === 4 ? 'Pengadu' : 'PIC'})</strong>
                            </p>
                            <div class="message-box p-2 rounded shadow-sm" style="max-width: 60%; background-color: ${bgColor};">
                                <p class="mb-1">${message}</p>
                                ${imagesHtml}
                                <small class="text-muted d-block text-end">${createdAt}</small>
                            </div>
                        </div>`;

                    chatContainer.append(messageHtml);
                    chatContainer.scrollTop(chatContainer[0].scrollHeight);

                    // Clear the form
                    $(form)[0].reset();
                    $('#file-name-' + ticketId).text('Tidak ada file dipilih');
                } else {
                    chatContainer.prepend(`<div class="alert alert-danger">${data.message || 'Terjadi kesalahan saat mengirim pesan.'}</div>`);
                }
            },
            error: function () {
                chatContainer.prepend(`<div class="alert alert-danger">Terjadi kesalahan saat mengirim pesan.</div>`);
            }
        });
    });

    // Handle AJAX form submission for reply forms (used in index.blade.php)
    $('[id^="reply-form-"]').on('submit', function (e) {
        e.preventDefault();
        var ticketId = this.id.replace('reply-form-', '');
        var form = this;
        var formData = new FormData(form);
        var chatContainer = $('#chat-container-' + ticketId);

        // Remove any existing error messages
        chatContainer.find('.alert-danger').remove();

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.success) {
                    var user = data.user;
                    var message = formData.get('message');
                    var createdAt = new Date().toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    var isSender = user.id === data.auth_user_id;
                    var bgColor = isSender ? 'rgb(84, 163, 242)' : 'rgb(54, 56, 59)';
                    var quotedMessage = data.quoted_message ? `<span class="fst-italic text-muted small d-block mb-1">(Membalas: "${data.quoted_message}")</span>` : '';

                    // Handle uploaded images
                    var imagesHtml = '';
                    if (data.uploads && data.uploads.length > 0) {
                        data.uploads.forEach(function (upload) {
                            imagesHtml += `
                                <a href="/storage/${upload.filename_path}" target="_blank">
                                    <img src="/storage/${upload.filename_path}" class="img-thumbnail mt-1" style="width: 128px; height: 128px; object-fit: cover;">
                                </a>`;
                        });
                    }

                    var messageHtml = `
                        <div class="message-wrapper mb-3" style="display: flex; flex-direction: column; align-items: ${isSender ? 'flex-end' : 'flex-start'};">
                            <p class="text-dark mb-1">
                                <strong>${user.username}</strong>
                            </p>
                            ${quotedMessage}
                            <div class="message-box p-2 rounded shadow-sm" style="max-width: 60%; background-color: ${bgColor};">
                                <p class="mb-1">${message}</p>
                                ${imagesHtml}
                                <small class="text-muted d-block text-end">${createdAt}</small>
                            </div>
                        </div>`;

                    chatContainer.append(messageHtml);
                    chatContainer.scrollTop(chatContainer[0].scrollHeight);

                    // Clear the form
                    $(form)[0].reset();
                    $('#file-name-' + ticketId).text('Tidak ada file dipilih');

                    // Hide the reply form if limit is reached
                    if (data.message_count >= 10 && data.limit_reached) {
                        $(form).closest('.form-container').hide();
                        chatContainer.prepend(`<div class="alert alert-warning">Anda telah mencapai batas 10 pesan. Tunggu balasan dari pegawai untuk mengirim lagi.</div>`);
                    }
                } else {
                    chatContainer.prepend(`<div class="alert alert-danger">${data.message || 'Terjadi kesalahan saat mengirim balasan.'}</div>`);
                }
            },
            error: function () {
                chatContainer.prepend(`<div class="alert alert-danger">Terjadi kesalahan saat mengirim balasan.</div>`);
            }
        });
    });
});