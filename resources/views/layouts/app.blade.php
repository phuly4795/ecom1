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
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}

    <script>
        Pusher.logToConsole = true;

        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: "{{ env('PUSHER_APP_KEY') }}",
            cluster: 'ap1',
            forceTLS: true
        });

        window.Echo.channel('contact-messages')
            .listen('NewContactMessage', (e) => {
                console.log('Tin nhắn mới:', e.contact);
                alert('Tin nhắn mới từ: ' + e.contact.name);
            });
    </script>
    <script>
        let messageCount = 0;

        window.Echo.channel('contact-messages')
            .listen('NewContactMessage', (e) => {
                const contact = e.contact;

                messageCount++;
                document.getElementById('messages-count').innerText = messageCount;

                const html = `
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="dropdown-list-image mr-3">
                        <img class="rounded-circle" src="https://ui-avatars.com/api/?name=${contact.name}" alt="${contact.name}">
                        <div class="status-indicator bg-success"></div>
                    </div>
                    <div>
                        <div class="text-truncate">${contact.content.substring(0, 50)}...</div>
                        <div class="small text-gray-500">${contact.name} · vừa gửi</div>
                    </div>
                </a>
            `;

                const list = document.getElementById('messages-list');
                if (list.querySelector('p')) list.innerHTML = ''; // Xóa "Không có tin nhắn"
                list.insertAdjacentHTML('afterbegin', html);
            });
    </script>

</body>

</html>
