<x-app-layout>
    <div id="content d-flex flex-row align-items-center justify-content-between">
        <div class="container-fluid ">
            <div class="text-center">
                <div class="error mx-auto" data-text="404">404</div>
                <p class="lead text-gray-800 mb-3">Không tìm thấy trang</p>
                <p class="text-gray-500 mb-0">Có vẻ như bạn đã tìm một trang không tồn tại...</p>
                <a href="{{route('admin.dashboard')}}">&larr; Trở về trang chủ</a>
            </div>
        </div>
    </div>
</x-app-layout>