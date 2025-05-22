@extends('admin.layout')

@section('content')
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #1e1e2f;
        color: #f1f1f1;
        margin: 0;
        padding: 0 20px 40px;
    }

  .dashboard {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.dashboard .card {
    flex: 1 1 200px; /* mỗi card ít nhất 200px, co dãn */
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
}

.section-title {
    font-weight: 600;
    font-size: 1.2rem;
    margin: 20px 0 10px 0;
    color: #333;
}

/* Container tổng cho biểu đồ */
.chart-container {
    max-width: 800px; /* giới hạn chiều rộng biểu đồ */
    margin: 0 auto 30px auto; /* căn giữa, cách dưới */
    padding: 0 10px;
}

/* Canvas các biểu đồ */
canvas {
    width: 100% !important; /* luôn rộng bằng container */
    height: 250px !important; /* chiều cao vừa phải */
    max-height: 300px !important;
    object-fit: contain;
}

/* Nếu bạn muốn chia 2 cột cho 2 biểu đồ nhỏ (Phương thức thanh toán và Mã giảm giá) */
.flex-two-columns {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    justify-content: center;
    max-width: 900px;
    margin: 0 auto 30px auto;
}

.flex-two-columns > div {
    flex: 1 1 400px;
    background: #fff;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
}

/* Biểu đồ top sản phẩm rộng hơn 1 chút */
.top-products-chart {
    max-width: 900px;
    margin: 0 auto 30px auto;
    padding: 15px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard {
        flex-direction: column;
    }
    .flex-two-columns {
        flex-direction: column;
    }
    .flex-two-columns > div, 
    .dashboard .card,
    .top-products-chart {
        flex: 1 1 100%;
    }
}


    .card {
        background: #2b2b3d;
        border-radius: 12px;
        padding: 24px 20px;
        box-shadow: 0 6px 14px rgba(0,0,0,0.6);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: default;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.8);
    }

    .card h3 {
        font-size: 30px;
        margin: 0;
        color: #fff;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .card p {
        margin-top: 10px;
        font-size: 16px;
        color: #bbb;
    }

    .section-title {
        font-size: 24px;
        font-weight: 700;
        color: #e0e0e0;
        margin-top: 48px;
        margin-bottom: 24px;
        border-left: 6px solid #3498db;
        padding-left: 14px;
        user-select: none;
    }

   

    ul.list {
        list-style: none;
        padding: 0;
        margin-top: 10px;
        max-width: 700px;
    }

    ul.list li {
        padding: 12px 18px;
        background: #3a3a52;
        margin-bottom: 12px;
        border-radius: 8px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.6);
        color: #ddd;
        font-weight: 500;
        transition: background-color 0.3s ease;
    }

    ul.list li strong {
        color: #fff;
    }

    ul.list li:hover {
        background-color: #4a4a6e;
    }

    @media (max-width: 600px) {
        .dashboard {
            grid-template-columns: 1fr;
        }

        canvas {
            padding: 12px;
        }

        ul.list {
            max-width: 100%;
        }
    }
</style>
<form action="{{ route('admin.dashboard') }}" method="GET" class="filter-form" style="margin-bottom: 20px;">
    <label for="from_date">Từ ngày:</label>
    <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}">
    
    <label for="to_date">Đến ngày:</label>
    <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}">

    <button type="submit">Lọc</button>
</form>
<div class="dashboard">
    <div class="card">
        <h3>{{ number_format($totalRevenue) }} đ</h3>
        <p>Tổng doanh thu</p>
    </div>
    <div class="card">
        <h3>{{ $todayOrders }}</h3>
        <p>Đơn hàng</p>
    </div>
    <div class="card">
        <h3>{{ $totalUsers }}</h3>
        <p>Tổng người dùng</p>
    </div>
    <div class="card">
        <h3>{{ $totalProducts }}</h3>
        <p>Tổng sản phẩm</p>
    </div>
</div>

<div class="chart-container">
    <div class="section-title">Thống kê doanh thu </div>
    <canvas id="revenueChart"></canvas>
</div>

<div class="chart-container">
    <div class="section-title">Tỷ lệ trạng thái đơn hàng</div>
    <canvas id="orderStatusChart"></canvas>
</div>

<div class="chart-container">
    <div class="section-title">Số lượng đơn hàng mỗi ngày</div>
    <canvas id="dailyOrdersChart"></canvas>
</div>

<div class="flex-two-columns">
    <div>
        <div class="section-title">Tổng số đơn hàng theo phương thức thanh toán</div>
        <canvas id="ordersByPaymentMethodChart"></canvas>
    </div>

    <div>
        <div class="section-title">Thống kê mã giảm giá</div>
        <canvas id="couponStatsChart"></canvas>
    </div>
</div>

<div class="top-products-chart">
    <div class="section-title">Top sản phẩm bán chạy</div>
    <canvas id="topSellingProductsChart"></canvas>
</div>


@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const commonOptions = {
            responsive: true,
            plugins: {
                legend: {
                    labels: { color: '#fff' }
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: '#333',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                }
            },
            scales: {
                x: {
                    ticks: { color: '#eee', font: { size: 13 } },
                    title: { color: '#ddd', display: true, font: { size: 14, weight: '600' } },
                    grid: { color: '#444' }
                },
                y: {
                    ticks: { color: '#eee', font: { size: 13 } },
                    title: { display: true, color: '#ddd', font: { size: 14, weight: '600' } },
                    grid: { color: '#444' }
                }
            }
        };

        // Revenue 7 days chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: @json($last7Days->pluck('date')),
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: @json($last7Days->pluck('revenue')),
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    x: {
                        ...commonOptions.scales.x,
                        title: { ...commonOptions.scales.x.title, text: 'Ngày' }
                    },
                    y: {
                        ...commonOptions.scales.y,
                        ticks: {
                            ...commonOptions.scales.y.ticks,
                            callback: value => value.toLocaleString('vi-VN') + ' đ'
                        },
                        title: { ...commonOptions.scales.y.title, text: 'VNĐ' }
                    }
                }
            }
        });

        // Order status pie chart
        const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
        new Chart(orderStatusCtx, {
            type: 'pie',
            data: {
                labels: @json($orderStatusLabels),
                datasets: [{
                    data: @json($orderStatusCountsForChart),
                    backgroundColor: [
                        '#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                    ],
                    borderColor: '#2b2b3d',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#fff', font: { size: 14 } } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percent = ((value / total) * 100).toFixed(1);
                                return `${context.label}: ${value} đơn (${percent}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Daily orders line chart
        const dailyOrdersCtx = document.getElementById('dailyOrdersChart').getContext('2d');
        new Chart(dailyOrdersCtx, {
            type: 'line',
            data: {
                labels: @json($orderCounts->pluck('date')),
                datasets: [{
                    label: 'Số đơn hàng',
                    data: @json($orderCounts->pluck('count')),
                    fill: false,
                    borderColor: 'rgba(255, 99, 132, 0.9)',
                    tension: 0.3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: 'rgba(255, 99, 132, 1)',
                    pointRadius: 5,
                    borderWidth: 3,
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    x: {
                        ...commonOptions.scales.x,
                        title: { ...commonOptions.scales.x.title, text: 'Ngày' }
                    },
                    y: {
                        ...commonOptions.scales.y,
                        beginAtZero: true,
                        ticks: { ...commonOptions.scales.y.ticks, stepSize: 1 },
                        title: { ...commonOptions.scales.y.title, text: 'Số đơn' }
                    }
                }
            }
        });

   // Tổng số đơn hàng theo phương thức thanh toán
const ordersByPaymentMethodCtx = document.getElementById('ordersByPaymentMethodChart').getContext('2d');
new Chart(ordersByPaymentMethodCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($ordersByPaymentMethod->pluck('payment_method')->map(fn($m) => ucfirst($m))) !!},
        datasets: [{
            label: 'Số đơn',
            data: {!! json_encode($ordersByPaymentMethod->pluck('count')) !!},
            backgroundColor: '#FF9F40',
            borderColor: '#FF9F40',
            borderWidth: 1,
            borderRadius: 5,
        }]
    },
    options: {
        ...commonOptions,
        scales: {
            x: {
                ...commonOptions.scales.x,
                title: { ...commonOptions.scales.x.title, text: 'Phương thức thanh toán' }
            },
            y: {
                ...commonOptions.scales.y,
                beginAtZero: true,
                ticks: { ...commonOptions.scales.y.ticks, stepSize: 1 },
                title: { ...commonOptions.scales.y.title, text: 'Số đơn' }
            }
        }
    }
});

// Thống kê mã giảm giá
const couponStatsCtx = document.getElementById('couponStatsChart').getContext('2d');
new Chart(couponStatsCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($couponStats->pluck('code')) !!},
        datasets: [{
            label: 'Số lần sử dụng',
            data: {!! json_encode($couponStats->pluck('used_count')) !!},
            backgroundColor: '#9966FF',
            borderColor: '#9966FF',
            borderWidth: 1,
            borderRadius: 5,
        }]
    },
    options: {
        ...commonOptions,
        scales: {
            x: {
                ...commonOptions.scales.x,
                title: { ...commonOptions.scales.x.title, text: 'Mã giảm giá' }
            },
            y: {
                ...commonOptions.scales.y,
                beginAtZero: true,
                ticks: { ...commonOptions.scales.y.ticks, stepSize: 1 },
                title: { ...commonOptions.scales.y.title, text: 'Số lượt sử dụng' }
            }
        }
    }
});

const fullLabels = {!! json_encode($topProducts->map(function($item) {
    $variant = $item->variant;
    $product = $variant ? $variant->product : null;
    if (!$product) return "Không xác định";

    $attributes = $variant->attributes->map(function($attr) {
        return ($attr->variantAttribute->attribute_name ?? '') . ': ' . ($attr->variantAttribute->attribute_value ?? '');
    })->implode(', ');

    return $product->name . ($attributes ? " ($attributes)" : '');
})) !!};

const shortLabels = {!! json_encode($topProducts->map(function($item) {
    $variant = $item->variant;
    $product = $variant ? $variant->product : null;
    if (!$product) return "Không xác định";

    $attributes = $variant->attributes->map(function($attr) {
        return ($attr->variantAttribute->attribute_name ?? '') . ': ' . ($attr->variantAttribute->attribute_value ?? '');
    })->implode(', ');

    $fullName = $product->name . ($attributes ? " ($attributes)" : '');
    return strlen($fullName) > 15 ? substr($fullName, 0, 15) . '...' : $fullName;
})) !!};
const topSellingProductsCtx = document.getElementById('topSellingProductsChart').getContext('2d');
new Chart(topSellingProductsCtx, {
    type: 'bar',
    data: {
        labels: shortLabels,
        datasets: [{
            label: 'Số lượng đã bán',
            data: {!! json_encode($topProducts->pluck('total_sold')) !!},
            backgroundColor: '#36A2EB',
            borderColor: '#36A2EB',
            borderWidth: 1,
            borderRadius: 5,
        }]
    },
    options: {
        ...commonOptions,
        plugins: {
            ...commonOptions.plugins,
            tooltip: {
                callbacks: {
                    title: function(context) {
                        const index = context[0].dataIndex;
                        return fullLabels[index];  // hiện tên đầy đủ khi hover
                    },
                    label: function(context) {
                        return `${context.dataset.label}: ${context.raw} sản phẩm`;
                    }
                }
            }
        },
        scales: {
            x: {
                ...commonOptions.scales.x,
                ticks: {
                    maxRotation: 60,
                    minRotation: 30,
                    autoSkip: false,
                    font: { size: 10 },
                    color: '#eee'
                },
                title: { ...commonOptions.scales.x.title, text: 'Sản phẩm' }
            },
            y: {
                ...commonOptions.scales.y,
                beginAtZero: true,
                ticks: { ...commonOptions.scales.y.ticks, stepSize: 1, color: '#eee' },
                title: { ...commonOptions.scales.y.title, text: 'Số lượng bán' }
            }
        }
    }
});



  });
</script>
