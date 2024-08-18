<?php

namespace App\Listeners;

use App\Models\Notif;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\PurchaseDetails;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;

class SendAgeingNotifications
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        $threshold = Carbon::now()->subMonths(6); // Batas umur barang, misalnya 6 bulan

        // Ambil purchase_detail yang terhubung dengan purchase yang statusnya 1 dan dibeli lebih dari 6 bulan yang lalu
        $purchaseDetails = PurchaseDetails::whereHas('purchase', function ($query) use ($threshold) {
            $query->where('status', 1)
                ->where('date', '<', $threshold);
        })->get();

        // Kelompokkan purchase details berdasarkan product_id
        $groupedPurchaseDetails = $purchaseDetails->groupBy('product_id');

        foreach ($groupedPurchaseDetails as $product_id => $details) {
            $product = Product::find($product_id);

            if ($product) {
                // Hitung total barang yang dibeli untuk produk ini
                $totalPurchased = $details->sum('quantity');

                // Ambil tanggal pembelian terlama untuk produk ini
                $earliestPurchaseDate = $details->min(function ($detail) {
                    return $detail->purchase->date;
                });

                // Hitung total barang yang terjual (dipesan) dimana order_status = 1 untuk produk ini
                $totalSold = OrderDetails::where('product_id', $product->id)
                    ->whereHas('order', function ($query) use ($earliestPurchaseDate) {
                        $query->where('order_status', 1)
                            ->where('order_date', '>=', $earliestPurchaseDate);
                    })
                    ->sum('quantity');

                // Hitung sisa barang yang belum terjual
                $remainingStock = $totalPurchased - $totalSold;

                if ($remainingStock > 0) {
                    Notif::create([
                        'title' => "Notification",
                        'description' => "Item {$product->name} is ageing and may soon expire. Remaining stock: {$remainingStock}",
                        'date' => date('Y-m-d')
                    ]);
                }
            }
        }
    }

}
