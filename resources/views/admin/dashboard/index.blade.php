@extends('admin.layout')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Thống kê hệ thống</h1>

    {{-- Thống kê nằm ngang --}}
   <div class="flex justify-between gap-4 flex-wrap md:flex-nowrap">
    <div class="p-4 bg-white shadow rounded w-full md:w-1/5">
        <p class="text-gray-600">Tổng doanh thu</p>
        <p class="text-xl font-bold text-green-600">{{ number_format($totalRevenue, 0, ',', '.') }} đ</p>
    </div>

    <div class="p-4 bg-white shadow rounded w-full md:w-1/5">
        <p class="text-gray-600">Sản phẩm còn trong kho</p>
        <p class="text-xl font-bold">{{ $totalStock }} sản phẩm</p>
    </div>

    <div class="p-4 bg-white shadow rounded w-full md:w-1/5">
        <p class="text-gray-600">Giá trị tồn kho</p>
        <p class="text-xl font-bold text-blue-600">{{ number_format($totalInventoryValue, 0, ',', '.') }} đ</p>
    </div>

    <div class="p-4 bg-white shadow rounded w-full md:w-1/5">
        <p class="text-gray-600">Đơn hàng hoàn tất</p>
        <p class="text-xl font-bold">{{ $completedOrders }}</p>
    </div>

    <div class="p-4 bg-white shadow rounded w-full md:w-1/5">
        <p class="text-gray-600">Đơn hàng bị huỷ</p>
        <p class="text-xl font-bold text-red-600">{{ $canceledOrders }}</p>
    </div>
</div>


{{-- Sản phẩm bán chạy và biểu đồ --}}
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Sản phẩm bán chạy --}}
    <div class="bg-white rounded shadow p-4 overflow-x-auto">
        <h2 class="text-xl font-semibold mb-2">Sản phẩm bán chạy</h2>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="py-2 px-4">#</th>
                    <th class="py-2 px-4">Tên sản phẩm</th>
                    <th class="py-2 px-4 text-center">Số lượng đã bán</th>
                    <th class="py-2 px-4 text-right">Tổng doanh thu</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($topProducts as $index => $product)
                    <tr class="border-t">
                        <td class="py-2 px-4">{{ $index + 1 }}</td>
                        <td class="py-2 px-4">{{ $product->name }}</td>
                        <td class="py-2 px-4 text-center">{{ $product->order_details_count }}</td>
                        <td class="py-2 px-4 text-right text-green-600">
                            {{ number_format($product->total_revenue ?? 0, 0, ',', '.') }} đ
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Biểu đồ sản phẩm bán ra --}}
    <div class="bg-white rounded shadow p-4">
        <h2 class="text-xl font-semibold mb-2">Biểu đồ sản phẩm bán ra theo ngày</h2>
        <canvas id="salesChart" height="220"></canvas>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const salesData = @json($salesData);
        const dates = salesData.map(item => item.date);
        const totalSold = salesData.map(item => item.total_sold);

        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Sản phẩm bán ra',
                    data: totalSold,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true },
                    title: {
                        display: true,
                        text: 'Biểu đồ sản phẩm bán ra theo ngày'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
@endsection
