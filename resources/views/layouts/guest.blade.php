<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><path fill='%234e73df' d='M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM96.8 314.1c-3.8-13.7 7.4-26.1 21.6-26.1H393.6c14.2 0 25.5 12.4 21.6 26.1C396.2 382 332.1 432 256 432s-140.2-50-164.8-117.9zM144.4 192a32 32 0 1 1 64 0 32 32 0 1 1 -64 0zm192-32a32 32 0 1 1 0 64 32 32 0 1 1 0-64z'/></svg>"
        type="image/svg+xml">
    <title>@yield('title')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />


    <!-- Google font -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">

    <!-- Bootstrap -->
    <link type="text/css" rel="stylesheet" href="{{ asset('asset/guest/css/bootstrap.min.css') }}" />
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"> --}}
    <!-- Slick -->
    <link type="text/css" rel="stylesheet" href="{{ asset('asset/guest/css/slick.css') }}" />
    <link type="text/css" rel="stylesheet" href="{{ asset('asset/guest/css/slick-theme.css') }}" />

    <!-- nouislider -->
    <link type="text/css" rel="stylesheet" href="{{ asset('asset/guest/css/nouislider.min.css') }}" />

    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="{{ asset('asset/guest/css/font-awesome.min.c') }}ss">

    <!-- Custom stlylesheet -->
    <link type="text/css" rel="stylesheet" href="{{ asset('asset/guest/css/style.css') }}" />
    <link type="text/css" rel="stylesheet" href="{{ asset('asset/guest/css/custom.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<!-- HEADER -->
@include('layouts.include.guest.header')
<!-- /HEADER -->

<!-- NAVIGATION -->
@include('layouts.include.guest.navigation')
<!-- /NAVIGATION -->
<!-- CONTENT -->
@if (session('status') && session('message'))
    <x-alert-modal-guest :status="session('status')" :content="session('message')" />
@endif
{{ $slot }}
<!-- /CONTENT -->
<!-- box chat -->
<div id="chat-icon" onclick="toggleChatbox()"
    style="position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; background-color: #ffc107; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 9999; animation: bounce 2s infinite;">
    <img src="{{ asset('asset/img/icon_chat_bot.png') }}" alt="Chat AI" style="width: 36px;">
</div>

<!-- Box Chat (ẩn ban đầu) -->
<div id="chatbox"
    style="display:none; position: fixed; bottom: 20px; right: 20px; width: 550px; height: 550px; background: white; border: 1px solid #ccc; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.2); z-index: 10000; flex-direction: column;">
    <div
        style="background: #ffc107; color: #000; padding: 10px; font-weight: bold; border-top-left-radius:10px; border-top-right-radius:10px; display: flex; justify-content: space-between;">
        <span>Trợ lý AI - Giới thiệu sản phẩm</span>
        <button onclick="clearChat()"
            style="background:none; border:none; font-size:14px; color:#fff; margin-left:auto;">
            🗑 Xóa hội thoại
        </button>
        <button onclick="toggleChatbox()" style="background: none; border: none; font-size: 16px;">X</button>
    </div>
    <div id="chat-content" style="height:330px; overflow-y:auto; padding:10px; flex-grow:1;"></div>
    <div
        style="display: flex;
        padding: 10px;
        border-top: 1px solid #ddd;
        flex-direction: row;
        flex-wrap: nowrap;
        align-content: center;
        justify-content: space-around;
        align-items: center;">
        <input type="text" id="chat-input" placeholder="Bạn cần gì?" style="width:80%;" class="form-control">
        <button class="btn btn-danger" onclick="sendMessage()">Gửi</button>
    </div>
</div>

<style>
    @keyframes bounce {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-8px);
        }

        60% {
            transform: translateY(-4px);
        }
    }

    #chatbox {
        background: #fff;
        border: 2px solid #e91e63;
        /* đỏ theo màu chủ đạo */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    #chatbox>div:first-child {
        background: #e91e63;
        /* header đỏ */
        color: #fff;
    }

    .chat-message {
        max-width: 80%;
        padding: 10px;
        border-radius: 10px;
        margin: 8px 0;
        display: inline-block;
        clear: both;
    }

    .chat-user {
        background-color: #f1f1f1;
        float: right;
        text-align: right;
    }

    .chat-ai {
        background-color: #f1f1f1;
        /* color: #fff; */
        float: left;
    }

    .typing-indicator {
        display: inline-block;
        padding: 8px 12px;
        background: #f1f1f1;
        border-radius: 15px 15px 15px 0;
        margin: 8px 0;
        font-style: italic;
        color: #666;
        animation: fadeIn 0.3s ease-in-out;
    }

    .typing-indicator span {
        animation: blink 1.4s infinite;
        font-size: 20px;
        margin: 0 1px;
    }

    .typing-indicator span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-indicator span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes blink {
        0% {
            opacity: 0.2;
        }

        20% {
            opacity: 1;
        }

        100% {
            opacity: 0.2;
        }
    }

    .ai-product-suggestion {
        margin-top: 15px;
        font-family: Arial, sans-serif;
    }

    .ai-product-suggestion a {
        color: #007bff;
        text-decoration: none;
    }

    .ai-product-suggestion a:hover {
        text-decoration: underline;
    }
</style>
<!-- FOOTER -->
@include('layouts.include.guest.footer')
<!-- /FOOTER -->

<script src="{{ asset('asset/guest/js/jquery.min.js') }}"></script>
<script src="{{ asset('asset/guest/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('asset/guest/js/slick.min.js') }}"></script>
<script src="{{ asset('asset/guest/js/nouislider.min.js') }}"></script>
<script src="{{ asset('asset/guest/js/jquery.zoom.min.js') }}"></script>
<script src="{{ asset('asset/guest/js/main.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let hasInitialized = false;

    function toggleChatbox() {
        const chatbox = document.getElementById("chatbox");
        const icon = document.getElementById("chat-icon");

        if (chatbox.style.display === "none") {
            chatbox.style.display = "flex";
            icon.style.display = "none";

            if (!hasInitialized) {
                fetch('/api/chat/history')
                    .then(res => res.json())
                    .then(history => {
                        const content = document.getElementById("chat-content");
                        history.forEach(msg => {
                            const senderClass = msg.sender === 'user' ? 'chat-message chat-user' :
                                'chat-message chat-ai';
                            const sender = msg.sender === 'user' ? 'Bạn' :
                                'Trợ lý AI';
                            content.innerHTML +=
                                `<div class="${senderClass}"><strong>${sender}:</strong> ${msg.message}</div>`;
                        });

                        if (history.length === 0) {
                            const welcome = "Xin chào! Mình là trợ lý bán hàng. Bạn đang cần tìm gì hôm nay?";
                            content.innerHTML +=
                                `<div class='chat-message chat-ai'><strong>Trợ lý AI:</strong> ${welcome}</div>`;
                            fetch('/api/chat-ai', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: JSON.stringify({
                                    message: welcome,
                                    is_system: true
                                })
                            });
                        }

                        content.scrollTop = content.scrollHeight;
                        hasInitialized = true;
                    });
            }
        } else {
            chatbox.style.display = "none";
            icon.style.display = "flex";
        }
    }

    function clearChat() {
        const content = document.getElementById("chat-content");
        const typingId = 'typing-clear-' + Date.now();

        // Hiệu ứng đang xử lý
        content.innerHTML +=
            `<div id="${typingId}" class="chat-message chat-ai typing-indicator"><span>.</span><span>.</span><span>.</span> </div>`;
        content.scrollTop = content.scrollHeight;

        fetch('/api/chat/clear-chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            })
            .then(res => res.json())
            .then(data => {
                // Xóa tất cả nội dung khung chat
                content.innerHTML = '';

                // Tin nhắn chào lại
                const welcome = "Xin chào! Mình là trợ lý bán hàng. Bạn đang cần tìm gì hôm nay?";
                content.innerHTML +=
                    `<div class='chat-message chat-ai'><strong>Trợ lý AI:</strong> ${welcome}</div>`;
                content.scrollTop = content.scrollHeight;

                // Gửi tin nhắn chào để tạo ngữ cảnh AI
                fetch('/api/chat-ai', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        message: welcome,
                        is_system: true
                    })
                });
            })
            .catch(() => {
                const typingEl = document.getElementById(typingId);
                if (typingEl) typingEl.remove();

                content.innerHTML += `<div style="color:red;">Lỗi kết nối. Vui lòng thử lại.</div>`;
            });
    }

    // Xử lý gửi tin nhắn
    function sendMessage() {
        const input = document.getElementById("chat-input");
        const content = document.getElementById("chat-content");
        const message = input.value.trim();

        if (message === "") return;

        // Hiển thị tin nhắn người dùng
        content.innerHTML += `<div class="chat-message chat-user"><strong>Bạn:</strong> ${message}</div>`;
        input.value = "";
        content.scrollTop = content.scrollHeight;

        // Thêm dấu chấm đang gõ
        const typingId = 'typing-' + Date.now();
        content.innerHTML +=
            `<div id="${typingId}" class="chat-message chat-ai typing-indicator"> <span>.</span><span>.</span><span>.</span> </div>`;
        content.scrollTop = content.scrollHeight;

        fetch('/api/chat-ai', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    message
                })
            })
            .then(res => res.json())
            .then(data => {
                const typingEl = document.getElementById(typingId);
                if (typingEl) typingEl.remove();

                // Hiển thị phản hồi AI
                content.innerHTML +=
                    `<div class="chat-message chat-ai"><strong>Trợ lý AI:</strong> ${data.reply}</div>`;
                content.scrollTop = content.scrollHeight;
            })
            .catch(() => {
                const typingEl = document.getElementById(typingId);
                if (typingEl) typingEl.remove();

                content.innerHTML += `<div style="color:red;">Lỗi kết nối. Vui lòng thử lại.</div>`;
            });
    }
</script>

<script>
    /**
     * Hiển thị alert modal cho người dùng
     * @param {string} message - Nội dung thông báo
     * @param {string} status - success | error | warning | info
     * @param {object} options - Các tuỳ chọn: autoClose, closeTimeout, redirectUrl
     */
    function showUserAlertModal(message, status = 'info', options = {}) {
        const icons = {
            success: '<i class="fa fa-check-circle text-success" style="font-size: 40px;"></i>',
            error: '<i class="fa fa-times-circle text-danger" style="font-size: 40px;"></i>',
            warning: '<i class="fa fa-exclamation-circle text-warning" style="font-size: 40px;"></i>',
            info: '<i class="fa fa-info-circle text-info" style="font-size: 40px;"></i>'
        };

        const defaultOptions = {
            autoClose: false,
            closeTimeout: 3000,
            redirectUrl: null
        };

        options = {
            ...defaultOptions,
            ...options
        };

        const modalContent = `
            <div class="mb-2">${icons[status]}</div>
            <p class="text-${status}">${message}</p>
        `;

        $('#userAlertModalContent').html(modalContent);
        $('#userAlertModal').modal('show');

        if (options.autoClose) {
            setTimeout(() => {
                $('#userAlertModal').modal('hide');
                if (options.redirectUrl) {
                    window.location.href = options.redirectUrl;
                }
            }, options.closeTimeout);
        }

        $('#userAlertModal').on('hidden.bs.modal', function() {
            $('#userAlertModalContent').html('');
            if (!options.autoClose && options.redirectUrl) {
                window.location.href = options.redirectUrl;
            }
        });
    }

    function showAlertModal(message, type = 'success') {
        Swal.fire({
            title: type === 'success' ? 'Thành công!' : 'Lỗi!',
            text: message,
            icon: type,
            confirmButtonText: 'OK',
            buttonsStyling: false,
            customClass: {
                confirmButton: `btn btn-${type}`
            }
        });
    }
    // Tự động gọi nếu có session flash từ Laravel
    @if (session('status') && session('message'))
        $(document).ready(function() {
            showUserAlertModal(
                "{{ session('message') }}",
                "{{ session('status') }}", {
                    autoClose: true,
                    closeTimeout: 3000,
                    @if (session('redirect'))
                        redirectUrl: "{{ session('redirect') }}"
                    @endif
                }
            );
        });
    @endif
</script>


</html>
