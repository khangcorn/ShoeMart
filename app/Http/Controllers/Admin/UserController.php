<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderReview;
use App\Models\OrderStatus;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('wallet')->paginate(15)->through(function ($user) {
            return [
                'id' => $user->user_id,
                'email' => $user->email,
                'contact' => $user->username.' | '.$user->phone,
                'avatar' => $user->avatar,
                'balance' => $user->wallet?->balance ?? 0,
                'is_blocked' => $user->is_blocked,
            ];
        });

        return view('admin.auth.index', compact('users'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.auth.create');

    }
    public function dashboard(Request $request)
{
    $fromDate = $request->input('from_date') ?? Carbon::now()->subDays(30)->toDateString();
    $toDate = $request->input('to_date') ?? Carbon::now()->toDateString();

    $ordersQuery = \App\Models\Order::where('status_id', 6); // chỉ đơn hoàn thành

    if ($fromDate && $toDate) {
        $ordersQuery->whereBetween('created_at', [
            Carbon::parse($fromDate)->startOfDay(),
            Carbon::parse($toDate)->endOfDay(),
        ]);
    }

    // 1. Tổng doanh thu từ các đơn hoàn thành (id trạng thái = 6)
   $totalRevenue = Order::where('status_id', 6)
    ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
        $query->whereBetween('created_at', [
            Carbon::parse($fromDate)->startOfDay(),
            Carbon::parse($toDate)->endOfDay()
        ]);
    })
    ->sum('total');


  $todayOrders = \App\Models\Order::when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
    $query->whereBetween('created_at', [
        Carbon::parse($fromDate)->startOfDay(),
        Carbon::parse($toDate)->endOfDay()
    ]);
})->count();


    // 3. Số đơn hàng theo từng trạng thái
    $orderStatusCounts = \App\Models\Order::select('status_id', DB::raw('count(*) as count'))
        ->groupBy('status_id')
        ->get();

    // 4. Top 5 sản phẩm bán chạy nhất (theo số lượng bán ra)
 $topProducts = OrderDetail::select('variant_id', DB::raw('SUM(quantity) as total_sold'))
    ->whereNotNull('variant_id')
    ->whereHas('order', function ($query) use ($fromDate, $toDate) {
        $query->whereBetween('created_at', [
            Carbon::parse($fromDate)->startOfDay(),
            Carbon::parse($toDate)->endOfDay()
        ])->whereIn('status_id', [4, 6]); // Chỉ lấy đơn giao thành công
    })
    ->groupBy('variant_id')
    ->orderByDesc('total_sold')
    ->with([
        'variant.product',
        'variant.attributes.variantAttribute'
    ])
    ->limit(5)
    ->get()
    ->filter(fn($item) => $item->variant && $item->variant->product);



   $totalUsers = \App\Models\User::when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
    $query->whereBetween('created_at', [
        Carbon::parse($fromDate)->startOfDay(),
        Carbon::parse($toDate)->endOfDay()
    ]);
})->count();

$totalProducts = \App\Models\Product::when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
    $query->whereBetween('created_at', [
        Carbon::parse($fromDate)->startOfDay(),
        Carbon::parse($toDate)->endOfDay()
    ]);
})->count();


    $last7Days = collect();
for ($i = 6; $i >= 0; $i--) {
    $date = Carbon::today()->subDays($i)->toDateString();
    if ($date < $fromDate || $date > $toDate) continue;

    $revenue = Order::whereDate('created_at', $date)
        ->whereIn('status_id', [4, 6])
        ->sum('total');

    $last7Days->push([
        'date' => $date,
        'revenue' => $revenue,
    ]);
}
      $statuses = OrderStatus::with(['orders' => function ($query) use ($fromDate, $toDate) {
    $query->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
        $q->whereBetween('created_at', [
            Carbon::parse($fromDate)->startOfDay(),
            Carbon::parse($toDate)->endOfDay(),
        ]);
    });
}])->get();

$orderStatusLabels = [];
$orderStatusCountsForChart = [];

foreach ($statuses as $status) {
    $orderStatusLabels[] = $status->name;
    $orderStatusCountsForChart[] = $status->orders->count();
}

    $orderCounts = collect();
for ($i = 29; $i >= 0; $i--) {
    $date = Carbon::today()->subDays($i)->toDateString();
    if ($date < $fromDate || $date > $toDate) continue;

    $count = Order::whereDate('created_at', $date)->count();
    $orderCounts->push([
        'date' => $date,
        'count' => $count,
    ]);
}

      $currentYear = Carbon::now()->year;
   $monthlyRevenue = Order::select(
        DB::raw('MONTH(created_at) as month'),
        DB::raw('SUM(total) as revenue')
    )
    ->whereBetween('created_at', [
        Carbon::parse($fromDate)->startOfDay(),
        Carbon::parse($toDate)->endOfDay()
    ])
    ->whereIn('status_id', [4, 6])
    ->groupBy(DB::raw('MONTH(created_at)'))
    ->orderBy('month')
    ->get();


    // Tạo mảng đủ 12 tháng, chỗ nào không có đơn thì gán 0
    $revenueByMonth = collect(range(1, 12))->map(function ($month) use ($monthlyRevenue) {
        $data = $monthlyRevenue->firstWhere('month', $month);
        return $data ? $data->revenue : 0;
    });
   $ordersByPaymentMethod = DB::table('orders')
    ->select('payment_method', DB::raw('count(*) as count'))
    ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
        $query->whereBetween('created_at', [
            Carbon::parse($fromDate)->startOfDay(),
            Carbon::parse($toDate)->endOfDay(),
        ]);
    })
    ->groupBy('payment_method')
    ->get();

   $couponStats = \App\Models\Coupon::select(
        'coupons.coupon_id',
        'coupons.code',
        DB::raw('COUNT(order_coupons.order_id) as used_count'),
        DB::raw('COALESCE(SUM(orders.discount_amount), 0) as total_discount')
    )
    ->leftJoin('order_coupons', 'order_coupons.coupon_id', '=', 'coupons.coupon_id')
    ->leftJoin('orders', 'orders.order_id', '=', 'order_coupons.order_id')
    ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
        $query->whereBetween('orders.created_at', [
            Carbon::parse($fromDate)->startOfDay(),
            Carbon::parse($toDate)->endOfDay(),
        ]);
    })
    ->groupBy('coupons.coupon_id', 'coupons.code')
    ->get();




    $commentStats = \App\Models\OrderReview::select('rating', DB::raw('count(*) as count'))
        ->groupBy('rating')
        ->orderBy('rating', 'desc')
        ->get();

    $totalComments = OrderReview::count();

    // Trung bình tổng số sao
    $averageRating = OrderReview::avg('rating');


    return view('admin.dashboard.index', compact(
    'totalRevenue', 'todayOrders', 'totalUsers', 'totalProducts',
    'orderStatusCounts', 'topProducts', 'last7Days',
    'orderStatusLabels', 'orderStatusCountsForChart',
    'orderCounts', 'revenueByMonth', 'ordersByPaymentMethod',
    'couponStats','totalComments','averageRating',
    'fromDate', 'toDate'
));

    


   
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

   

   

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function requestAccess()
    {
        $userId = auth()->id();
        $hasPendingRequest = AdminRequest::where('user_id', $userId)
            ->where('status', 'pending')
            ->exists();

        return view('admin.admin_access.no_access', compact('hasPendingRequest'));
    }

    public function sendRequest()
    {
        $userId = auth()->id();

        // Kiểm tra user đã có request đang pending chưa
        $existingRequest = AdminRequest::where('user_id', $userId)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            // Nếu đã có yêu cầu pending, quay lại với thông báo
            return redirect()->back()->with('info', 'Bạn đã gửi yêu cầu, vui lòng chờ admin xét duyệt.');
        }

        // Tạo yêu cầu mới nếu chưa có
        AdminRequest::create([
            'user_id' => $userId,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Yêu cầu truy cập đã được gửi.');
    }

    public function viewRequests()
    {
        $requests = AdminRequest::with('user')->get();

        return view('admin.admin_access.request_access', compact('requests'));
    }

    public function approveRequest($id)
    {
        if (! auth()->user()->hasPermission('access_request.approve')) {
            return response()->json(['message' => 'Bạn không có quyền duyệt truy cập Admin.'], 403);
        }
        $request = AdminRequest::findOrFail($id);

        if ($request->status !== 'pending') {
            return response()->json(['message' => 'Yêu cầu đã được xử lý'], 400);
        }

        $request->status = 'approved';
        $request->save();

        // Gán role staff (role_id = 3) nếu chưa có
        $user = $request->user;
        if ($user && ! $user->roles()->where('user_roles.role_id', 3)->exists()) {
            $user->roles()->attach(3);
        }

        return response()->json(['message' => 'Yêu cầu đã được duyệt'], 200);
    }

    public function rejectRequest($id)
    {
        if (! auth()->user()->hasPermission('access_request.reject')) {
            return response()->json(['message' => 'Bạn không có quyền duyệt truy cập Admin.'], 403);
        }
        $request = AdminRequest::findOrFail($id);
        $request->status = 'rejected';
        $request->save();

        return response()->json(['success' => true]);
    }

    public function revoke($id)
    {

        if (! auth()->user()->hasPermission('access_request.revoke')) {
            return response()->json(['message' => 'Bạn không có quyền hủy truy cập Admin.'], 403);
        }
        $request = AdminRequest::findOrFail($id);

        if ($request->status !== 'approved') {
            return response()->json(['message' => 'Yêu cầu chưa được duyệt nên không thể hủy quyền.'], 400);
        }

        $request->status = 'revoked';
        $request->save();

        $user = $request->user;
        if ($user) {
            // Lấy role staff
            $staffRole = \App\Models\Role::where('name', 'staff')->first();
            if ($staffRole) {
                // Xóa role staff của user này
                $user->roles()->detach($staffRole->id);
            }
        }

        return response()->json(['message' => 'Hủy quyền truy cập thành công.']);
    }
  public function blockUser(User $user)
{
    if (auth()->id() === $user->user_id) {
        return response()->json(['success' => false, 'message' => 'Bạn không thể tự khóa tài khoản của mình.']);
    }

    $user->is_blocked = true;
    $user->save();

    return response()->json(['success' => true, 'message' => 'Tài khoản đã bị khóa.']);
}

public function unblockUser(User $user)
{
    $user->is_blocked = false;
    $user->save();

    return response()->json(['success' => true, 'message' => 'Tài khoản đã được mở khóa.']);
}


}
