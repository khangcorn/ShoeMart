<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VnPayController extends Controller
{
public function createPayment(Request $request)
{
    $vnp_TmnCode = env('VNPAY_TMN_CODE');
    $vnp_HashSecret = env('VNPAY_HASH_SECRET');
    Log::info('VNPAY Hash Secret:', ['secret' => $vnp_HashSecret]);
    $vnp_Url = env('VNPAY_URL');
    $vnp_Returnurl = "http://tutoan132.com/payment/vnpay/return";

    $order = Order::findOrFail($request->order_id);
    $orderCode = $order->order_code;
    $amount = (int)($order->total * 100);
    $startTime = date("YmdHis");
    $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));
    $orderId = $request->order_id;
    $method = $request->method;
    
    $inputData = [
        "vnp_Version" => "2.1.0",
        "vnp_TmnCode" => $vnp_TmnCode,
        "vnp_Amount" => $amount,
        "vnp_Command" => "pay",
        "vnp_CreateDate" => now()->format('YmdHis'),
        "vnp_CurrCode" => "VND",
        "vnp_IpAddr" => $request->ip(),
        "vnp_Locale" => "vn",
        "vnp_OrderInfo" => "Thanh toan don hang " . $orderCode,
        "vnp_OrderType" => "billpayment",
        "vnp_ReturnUrl" => $vnp_Returnurl,
        "vnp_TxnRef" => $orderCode,
    ];

    ksort($inputData);
    Log::info('Payment inputData:', $inputData);
   $hashData = '';
    foreach ($inputData as $key => $value) {
        $hashData .= ($hashData ? '&' : '') . urlencode($key) . '=' . urlencode($value);
    }

    $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);


    $inputData['vnp_SecureHashType'] = 'SHA512';
    $inputData['vnp_SecureHash'] = $vnp_SecureHash;

    $paymentUrl = $vnp_Url . '?' . http_build_query($inputData, '', '&', PHP_QUERY_RFC3986);

    Log::info('Hash Data:', ['hashData' => $hashData]);
    Log::info('Secure Hash:', ['secureHash' => $vnp_SecureHash]);
    Log::info('Payment URL:', ['url' => $paymentUrl]);

    return redirect($paymentUrl);
}






public function vnpayReturn(Request $request)
{
    $vnp_HashSecret = trim(env('VNPAY_HASH_SECRET'));

    // Lấy dữ liệu query string đầy đủ
    $queryParams = $request->query();

    Log::info('VNPAY Return full query:', $queryParams);

    // Loại bỏ 2 trường không dùng để tạo hash
    $inputData = $queryParams;
    unset($inputData['vnp_SecureHashType']);
    unset($inputData['vnp_SecureHash']);

    // Sắp xếp tham số theo key
     ksort($inputData);
    $hashData = '';
    foreach ($inputData as $key => $value) {
        $hashData .= ($hashData ? '&' : '') . urlencode($key) . '=' . urlencode($value);
    }




    // Tính hash mới
    $calculatedHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

    Log::info('VNPAY Return hashData:', ['hashData' => $hashData]);
    Log::info('VNPAY Return calculatedHash:', ['calculatedHash' => $calculatedHash]);
    Log::info('VNPAY Return received vnp_SecureHash:', ['receivedHash' => $request->query('vnp_SecureHash')]);

    // So sánh chữ ký không phân biệt hoa thường
  if (!hash_equals(strtoupper($calculatedHash), strtoupper($request->query('vnp_SecureHash')))) {
        return view('payment.invalid')->with([
            'message' => 'Sai chữ ký',
            'hashData' => $hashData,
            'calculatedSecureHash' => $calculatedHash,
            'receivedSecureHash' => $request->query('vnp_SecureHash'),
        ]);
    }

    // Xử lý thanh toán thành công
    if ($request->query('vnp_ResponseCode') === '00') {
        DB::beginTransaction();
        try {
            $order = Order::where('order_code', $request->query('vnp_TxnRef'))->firstOrFail();

            $order->update([
                'status_id' => 1,
                'payment_status' => 'success',
            ]);

            DB::commit();

            return view('payment.success', compact('order'));
        } catch (\Exception $e) {
            DB::rollBack();
            return view('payment.failed')->with('error', 'Lỗi cập nhật đơn hàng: ' . $e->getMessage());
        }
    } else {
        return view('payment.failed');
    }
}
public function checkIp(Request $request)
{
    $ip = $request->ip();
    Log::info('Client IP: ' . $ip);
    return response()->json(['ip' => $ip]);
}
}