<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoCompleteOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-complete-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động chuyển trạng thái đơn hàng: từ "Đã giao hàng" (status_id = 4) sang "Đã nhận hàng" (status_id = 7) sau 3 ngày, rồi từ "Đã nhận hàng" sang "Đã hoàn thành" (status_id = 8) sau 7 ngày nữa';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Lấy tất cả các đơn hàng có trạng thái "Đã giao hàng" (status_id = 4) và đã quá 3 ngày
        $ordersToReceive = Order::where('status_id', 4) // "Đã giao hàng"
            ->where('updated_at', '<=', now()->subDays(3)) // Đã quá 3 ngày
            ->get();
        
        foreach ($ordersToReceive as $order) {
            // Cập nhật trạng thái đơn hàng thành "Đã nhận hàng" (status_id = 7)
            $order->status_id = 7;
            $order->save();
            
            // Ghi log cho mỗi đơn hàng được cập nhật
            Log::info("Đơn hàng #{$order->order_code} đã chuyển sang trạng thái 'Đã nhận hàng'.");
        }

        // Lấy tất cả các đơn hàng có trạng thái "Đã nhận hàng" (status_id = 7) và đã quá 7 ngày
        $ordersToComplete = Order::where('status_id', 7) // "Đã nhận hàng"
            ->where('updated_at', '<=', now()->subDays(7)) // Đã quá 7 ngày
            ->get();

        foreach ($ordersToComplete as $order) {
            // Cập nhật trạng thái đơn hàng thành "Đã hoàn thành" (status_id = 8)
            $order->status_id = 8;
            $order->save();

            // Ghi log cho mỗi đơn hàng được cập nhật
            Log::info("Đơn hàng #{$order->order_code} đã chuyển sang trạng thái 'Đã hoàn thành'.");
        }

        // Thông báo hoàn thành việc cập nhật
        $this->info('Đã hoàn tất việc cập nhật trạng thái các đơn hàng.');
    }
}
