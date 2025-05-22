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
    protected $description = 'Tự động chuyển trạng thái đơn hàng:  từ "Đã nhận hàng" sang "Đã hoàn thành" (status_id = 6) sau 3 ngày nữa';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ordersToComplete = Order::where('status_id', 4) // "Đã nhận hàng"
            ->where('delivered_at', '<=', now()->subDays(3))
            ->get();

        foreach ($ordersToComplete as $order) {
            $order->status_id = 6;
            $order->save();

            // Ghi log cho mỗi đơn hàng được cập nhật
            Log::info("Đơn hàng #{$order->order_code} đã chuyển sang trạng thái 'Đã hoàn thành'.");
        }

        // Thông báo hoàn thành việc cập nhật
        $this->info('Đã hoàn tất việc cập nhật trạng thái các đơn hàng.');
    }
}
