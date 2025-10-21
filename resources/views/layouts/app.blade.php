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
    </script>
    <script>
        let originalTitle = document.title;
        let notificationCount = 0;

        document.addEventListener('DOMContentLoaded', function() {
            if (window.Echo) {
                // thông báo liên hệ
                window.Echo.channel('contact-messages')
                    .listen('.new-contact', (e) => {
                        console.log('📨 New message received:', e.contact);

                        const contact = e.contact;
                        const html = `
                            <a class="dropdown-item d-flex align-items-center" href="/admin/contacts/${contact.id}">
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


                        const list = document.getElementById('messages-list');
                        if (list.querySelector('p')) list.innerHTML = '';

                        list.insertAdjacentHTML('afterbegin', html);

                        const badge = document.getElementById('messages-count');
                        const count = parseInt(badge.innerText || '0') + 1;
                        badge.innerText = count;
                        notificationCount++;
                        updateDocumentTitle();
                    });


                // thông báo khác
                window.Echo.channel('admin-notifications')
                    .listen('NewNotification', (e) => {
                        console.log('📨 New notifications:', e);

                        const notification = e;
                        switch (notification.type) {
                            case 'new-order':
                                iconHtml = `
            <div class="icon-circle bg-primary">
                <i class="fas fa-shopping-cart text-white"></i>
            </div>`;
                                break;
                            case 'paymented':
                                iconHtml = `
            <div class="icon-circle bg-success">
                <i class="fas fa-credit-card text-white"></i>
            </div>`;
                                break;
                            case 'new-user':
                                iconHtml = `
            <div class="icon-circle bg-info">
                <i class="fas fa-user-plus text-white"></i>
            </div>`;
                                break;
                            case 'promotion-expire':
                                iconHtml = `
            <div class="icon-circle bg-warning">
                <i class="fas fa-clock text-white"></i>
            </div>`;
                                break;
                            case 'low-stock':
                                iconHtml = `
            <div class="icon-circle bg-warning">
                <i class="fas fa-clock text-white"></i>
            </div>`;
                                break;
                            default:
                                iconHtml = `
            <div class="icon-circle bg-secondary">
                <i class="fas fa-bell text-white"></i>
            </div>`;
                        }

                        let href = '/admin/dashboard';
                        if (notification.type === 'new-order') href = `/admin/order/${notification.id}`;
                        else if (notification.type === 'paymented') href = `/admin/orders?status=paid`;
                        else if (notification.type === 'new-user') href = `/admin/users`;
                        else if (notification.type === 'promotion-expire') href =
                            `/admin/coupons?soon_expire=true`;


                        const html = `
                         <a class="dropdown-item d-flex align-items-center" href="${href}">
                                <div class="mr-3">
                                  ${iconHtml}
                                </div>
                                <div>
                                    <div class="small text-gray-500">${dayjs().fromNow()}</div>
                                    <span class="font-weight-bold">${notification.title}</span>
                                    <div>${notification.message.substring(0, 50)}...</div>
                                </div>
                            </a>
                        `;

                        const list = document.getElementById('messages-list-alert');
                        if (list.querySelector('p')) list.innerHTML = '';
                        list.insertAdjacentHTML('afterbegin', html);

                        const badge = document.getElementById('messages-count-alert');
                        const count = parseInt(badge.innerText || '0') + 1;
                        badge.innerText = count;
                        notificationCount++;
                        updateDocumentTitle();
                    });
            } else {
                console.error('window.Echo not defined');
            }
        });

        function updateDocumentTitle() {
            if (notificationCount > 0) {
                document.title = `(${notificationCount}) ${originalTitle}`;
            } else {
                document.title = originalTitle;
            }
        }

        document.getElementById('messagesDropdown')?.addEventListener('click', () => {
            notificationCount = 0;
            updateDocumentTitle();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault(); // chặn chuyển trang ngay lập tức

                    const notificationId = this.dataset.id;
                    const href = this.getAttribute('href');

                    fetch(`/admin/notifications/${notificationId}/mark-read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({})
                    }).then(() => {
                        window.location.href = href; // chuyển trang sau khi mark read
                    }).catch(() => {
                        window.location.href = href; // fallback nếu lỗi vẫn chuyển
                    });
                });
            });
        });
    </script>
</body>

</html>
