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

                    // Handle uploaded images
                    var imagesHtml = '';
                    if (data.uploads && data.uploads.length > 0) {
                        data.uploads.forEach(function (upload) {
                            imagesHtml += `
                                <div class="message-attachment mb-2">
                                    <a href="/storage/${upload.filename_path}" target="_blank">
                                        <img src="/storage/${upload.filename_path}" alt="${upload.filename_ori}" style="width: 128px; height: 128px; object-fit: cover; border-radius: 8px; ${!isSender ? 'border: 1px solid #e5e7eb;' : ''}">
                                    </a>
                                </div>`;
                        });
                    }

                    var messageHtml = `
                        <div class="message-wrapper mb-3" style="display: flex; flex-direction: column; align-items: ${isSender ? 'flex-end' : 'flex-start'};">
                            <div class="message-info d-flex align-items-center gap-2 mb-1">
                                ${!isSender ? `
                                    <div class="avatar" style="width: 28px; height: 28px; border-radius: 50%; background-color: #1e3a8a; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600;">
                                        ${user.username.charAt(0)}
                                    </div>` : ''}
                                <span class="message-sender" style="font-weight: 600; font-size: 0.85rem; color: #374151;">
                                    ${user.username} (${user.role_id === 4 ? 'Pengadu' : 'PIC'})
                                </span>
                            </div>
                            <div class="message-box ${isSender ? 'sent' : 'received'}" style="max-width: 80%; padding: 12px 16px; border-radius: 16px; margin-bottom: 0.5rem; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);">
                                <p class="mb-0" style="line-height: 1.5; font-size: 0.9rem;">${message}</p>
                                ${imagesHtml}
                                <span class="message-time" style="font-size: 0.7rem; ${isSender ? 'color: rgba(255, 255, 255, 0.85);' : 'color: #6b7280;'} display: block; text-align: right; margin-top: 4px;">
                                    ${createdAt}
                                </span>
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
                    var quotedMessage = data.quoted_message ? `<div class="message-quote" style="font-style: italic; ${isSender ? 'color: rgba(255, 255, 255, 0.8);' : 'color: #6b7280;'} font-size: 0.8rem; margin-bottom: 6px; padding-left: 8px; border-left: 2px solid ${isSender ? 'rgba(255, 255, 255, 0.5)' : '#d1d5db'};">"${data.quoted_message}"</div>` : '';

                    // Handle uploaded images
                    var imagesHtml = '';
                    if (data.uploads && data.uploads.length > 0) {
                        data.uploads.forEach(function (upload) {
                            imagesHtml += `
                                <div class="message-attachment mb-2">
                                    <a href="/storage/${upload.filename_path}" target="_blank">
                                        <img src="/storage/${upload.filename_path}" alt="${upload.filename_ori}" style="width: 128px; height: 128px; object-fit: cover; border-radius: 8px; ${!isSender ? 'border: 1px solid #e5e7eb;' : ''}">
                                    </a>
                                </div>`;
                        });
                    }

                    var messageHtml = `
                        <div class="message-wrapper mb-3" style="display: flex; flex-direction: column; align-items: ${isSender ? 'flex-end' : 'flex-start'};">
                            <div class="message-info d-flex align-items-center gap-2 mb-1">
                                ${!isSender ? `
                                    <div class="avatar" style="width: 28px; height: 28px; border-radius: 50%; background-color: #1e3a8a; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600;">
                                        ${user.username.charAt(0)}
                                    </div>` : ''}
                                <span class="message-sender" style="font-weight: 600; font-size: 0.85rem; color: #374151;">
                                    ${user.username}
                                </span>
                            </div>
                            ${quotedMessage}
                            <div class="message-box ${isSender ? 'sent' : 'received'}" style="max-width: 80%; padding: 12px 16px; border-radius: 16px; margin-bottom: 0.5rem; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);">
                                <p class="mb-0" style="line-height: 1.5; font-size: 0.9rem;">${message}</p>
                                ${imagesHtml}
                                <span class="message-time" style="font-size: 0.7rem; ${isSender ? 'color: rgba(255, 255, 255, 0.85);' : 'color: #6b7280;'} display: block; text-align: right; margin-top: 4px;">
                                    ${createdAt}
                                </span>
                            </div>
                        </div>`;

                    chatContainer.append(messageHtml);
                    chatContainer.scrollTop(chatContainer[0].scrollHeight);

                    // Clear the form
                    $(form)[0].reset();
                    $('#file-name-' + ticketId).text('Tidak ada file dipilih');

                    // Show toast notification if limit is reached
                    if (data.message_count >= 10 && data.limit_reached) {
                        $(form).closest('.reply-container').hide();
                        if ($('#limitToast-' + ticketId).length === 0) {
                            chatContainer.prepend(`
                                <div class="toast position-absolute top-0 start-50 translate-middle-x bg-warning text-dark p-3 rounded shadow" id="limitToast-${ticketId}" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 1050; font-size: 12px; max-width: 90%; opacity: 0.9;">
                                    <div class="d-flex">
                                        <div class="toast-body">
                                            Anda telah mencapai batas 10 pesan. Tunggu balasan dari pegawai untuk mengirim lagi.
                                        </div>
                                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                </div>
                            `);
                            var toast = new bootstrap.Toast($('#limitToast-' + ticketId));
                            toast.show();
                            setTimeout(() => {
                                toast.hide();
                            }, 5000); // Auto-hide after 5 seconds
                        }
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