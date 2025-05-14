<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\ProductVariant;
  use App\Models\OrderDetail;
use App\Models\Product;
use Carbon\Carbon;
class DashboardController extends Controller
{


public function index()
{
    // Lấy tổng doanh thu, số sản phẩm còn, giá trị tồn kho, đơn hàng hoàn tất và bị huỷ
    $totalRevenue = Order::whereHas('status', function ($query) {
        $query->whereIn('name', ['Đã nhận hàng', 'Đã giao hàng']);
    })->sum('total_price');

    $totalStock = ProductVariant::sum('stock');
    $totalInventoryValue = ProductVariant::sum(DB::raw('stock * price_sale')); // Tính giá trị tồn kho
    $completedOrders = Order::whereHas('status', function ($query) {
        $query->where('name', 'Đã nhận hàng', 'Đã giao hàng');
    })->count();
    $canceledOrders = Order::whereHas('status', function ($query) {
        $query->where('name', 'Đã huỷ');
    })->count();

    // Lấy sản phẩm bán chạy
   $topProducts = Product::withCount('orderDetails')
    ->withSum('orderDetails', 'price') // assuming `price` is in order_details
    ->orderByDesc('order_details_count')
    ->take(5)
    ->get()
    ->map(function ($product) {
        $product->total_revenue = $product->order_details_sum_price;
        return $product;
    });


    // Lấy số sản phẩm bán ra theo từng ngày
    $salesData = OrderDetail::selectRaw('DATE(created_at) as date, SUM(quantity) as total_sold')
        ->groupBy(DB::raw('DATE(created_at)'))
        ->whereHas('order', function ($query) {
            $query->whereHas('status', function ($query) {
                $query->whereIn('name', ['Đã nhận hàng', 'Đã giao hàng']);
            });
        })
        ->orderBy('date', 'asc')
        ->get();

    // Truyền dữ liệu vào view
    return view('admin.dashboard.index', compact(
        'totalRevenue', 
        'totalStock', 
        'totalInventoryValue', 
        'completedOrders', 
        'canceledOrders', 
        'topProducts',
        'salesData'
    ));
}

}
