<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><path fill='%234e73df' d='M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM96.8 314.1c-3.8-13.7 7.4-26.1 21.6-26.1H393.6c14.2 0 25.5 12.4 21.6 26.1C396.2 382 332.1 432 256 432s-140.2-50-164.8-117.9zM144.4 192a32 32 0 1 1 64 0 32 32 0 1 1 -64 0zm192-32a32 32 0 1 1 0 64 32 32 0 1 1 0-64z'/></svg>"
        type="image/svg+xml">

    <title>@yield('title')</title>
    @include('layouts.include.link')
    @include('layouts.include.script')
</head>

<body id="page-top">

    <div id="wrapper">
        @include('layouts.include.navigation')
        <div id="content-wrapper" class="d-flex flex-column">
            @include('layouts.include.topBar')
            <main>
                {{ $slot }}
            </main>
            <!-- Modal thông báo -->
            @if (session('status') && session('message'))
                <x-alert-modal :status="session('status')" :content="session('message')" />
            @endif
        </div>
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/plugin/relativeTime.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/locale/vi.js"></script>
    <script>
        dayjs.extend(dayjs_plugin_relativeTime);
        dayjs.locale('vi');
        console.log(dayjs().fromNow()); // "vài giây trước"
    </script>




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Realtime JS loaded');
            // window.Echo.channel("contact-messages").listen("NewContactMessage", (e) => {
            //     alert("Tin nhắn mới từ: " + e.contact.name);
            // });
            if (window.Echo) {
                window.Echo.channel('contact-messages')
                    .listen('NewContactMessage', (e) => {
                        console.log('📨 New message received:', e.contact);

                        const contact = e.contact;

                        const html = `
    <a class="dropdown-item d-flex align-items-center" >
        <div class="mr-3">
            <div class="icon-circle bg-primary">
                <i class="fas fa-envelope text-white"></i>
            </div>
        </div>
        <div>
            <div class="small text-gray-500">${dayjs().fromNow()}</div>
            <span class="font-weight-bold">Liên hệ mới từ ${contact.name}</span>
            <div>${contact.content.substring(0, 50)}...</div>
        </div>
    </a>
`;
                        document.getElementById('messages-list').insertAdjacentHTML('afterbegin', html);


                        const list = document.getElementById('messages-list');
                        if (list.querySelector('p')) list.innerHTML = '';
                        list.insertAdjacentHTML('afterbegin', html);

                        const badge = document.getElementById('messages-count');
                        const count = parseInt(badge.innerText || '0') + 1;
                        badge.innerText = count;
                    });
            } else {
                console.error('window.Echo not defined');
            }
        });



        document.addEventListener('click', function(e) {
            const item = e.target.closest('.message-item');
            if (item) {
                e.preventDefault(); // ngăn chặn chuyển trang nếu cần
                item.remove(); // xóa khỏi giao diện

                // Giảm số badge
                const badge = document.getElementById('messages-count');
                const current = parseInt(badge.innerText || '0');
                if (current > 0) badge.innerText = current - 1;

                // Chuyển trang đến liên hệ
                window.location.href = item.getAttribute('href');
            }
        });
    </script>

</body>

</html>
