<x-guest-layout>
    @section('title', 'Cảm ơn')
    <div class="container text-center mt-5">
        <h1>Cảm ơn bạn đã đặt hàng!</h1>
        <p>Chúng tôi đã nhận được đơn hàng của bạn và sẽ xử lý sớm nhất.</p>
        <a href="{{ route('home') }}" class="btn btn-primary mt-3">Tiếp tục mua sắm</a>
    </div>

</x-guest-layout>
