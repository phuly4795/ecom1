<x-app-layout>
    @section('title', 'Trang chủ')
    <div id="content">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Trang chủ</h1>
                {{-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-download fa-sm text-white-50"></i> Xuất báo cáo
                </a> --}}
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body">Tổng liên hệ: {{ $contactCount }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body">Tổng người dùng: {{ $userCount }}</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Biểu đồ Doanh thu</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="myAreaChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Tỷ lệ dữ liệu</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="myPieChart"></canvas>
                            </div>
                            <div class="mt-4 text-center small">
                                <span class="mr-2">
                                    <i class="fas fa-circle text-primary"></i> Sản phẩm
                                </span>
                                <span class="mr-2">
                                    <i class="fas fa-circle text-success"></i> Người dùng
                                </span>
                                <span class="mr-2">
                                    <i class="fas fa-circle text-info"></i> Liên hệ
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12"
                    style="display: flex;
                    flex-direction: row;
                    align-content: center;
                    flex-wrap: wrap;
                    justify-content: space-between;
                    align-items: flex-start;">
                    <div class="card mb-4 col-md-4">
                        <div class="card-header">Trạng thái đơn hàng</div>
                        <div class="card-body text-center">
                            <canvas id="ordersChart" width="300" height="300"></canvas>
                        </div>
                    </div>

                    <div class="card mb-4 col-md-3">
                        <div class="card-header">Người dùng mới 7 ngày qua</div>
                        <div class="card-body" style="height: 300px;">
                            <canvas id="usersChart" width="300" height="300"></canvas>
                        </div>
                    </div>

                    <div class="card mb-4 col-md-4">
                        <div class="card-header">Sản phẩm bán chạy</div>
                        <div class="card-body" style="height: 300px;">
                            <canvas id="bestsellersChart" width="300" height="300"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Đơn hàng theo trạng thái
        const ordersChart = new Chart(document.getElementById('ordersChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($ordersByStatus->keys()) !!},
                datasets: [{
                    data: {!! json_encode($ordersByStatus->values()) !!},
                    backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b'],
                }]
            }
        });

        // Người dùng mới trong 7 ngày
        const usersChart = new Chart(document.getElementById('usersChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($usersLast7Days->pluck('date')) !!},
                datasets: [{
                    label: 'Người dùng mới',
                    data: {!! json_encode($usersLast7Days->pluck('total')) !!},
                    borderColor: '#36b9cc',
                    fill: false,
                    tension: 0.4,
                }]
            }
        });

        // Sản phẩm bán chạy
        const bestsellersChart = new Chart(document.getElementById('bestsellersChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($bestSellers->pluck('title')) !!},
                datasets: [{
                    label: 'Số lượng bán',
                    data: {!! json_encode($bestSellers->pluck('total_sold')) !!},
                    backgroundColor: '#4e73df',
                }]
            }
        });


        const ctxArea = document.getElementById("myAreaChart").getContext('2d');
        new Chart(ctxArea, {
            type: 'line',
            data: {
                labels: {!! json_encode(
                    $revenueLast7Days->pluck('date')->map(function ($d) {
                        return \Carbon\Carbon::parse($d)->format('d/m');
                    }),
                ) !!},
                datasets: [{
                    label: 'Doanh thu (vnđ)',
                    data: {!! json_encode($revenueLast7Days->pluck('total')) !!},
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    }
                }
            }
        });

        const ctxPie = document.getElementById("myPieChart").getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ['Sản phẩm', 'Người dùng', 'Liên hệ'],
                datasets: [{
                    data: [{{ $productCount }}, {{ $userCount }}, {{ $contactCount }}],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                    hoverOffset: 10,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</x-app-layout>
