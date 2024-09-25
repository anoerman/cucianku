<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'worker_id',
        'product_id',
        'qty',
        'discount',
        'price',
    ];

    protected static function booted() {
        // Hook yang digunakan untuk menambahkan data yang akan disimpan
        static::saving(function($order_detail) {
            // Ambil harga dari table products
            $product = Product::find($order_detail->product_id);
            if ($product) {
                // Hitung harga * qty
                $order_detail->price = $product->price * $order_detail->qty;
            }
        });

        // Hook untuk mengubah data order berdasarkan order detail
        static::saved(function ($order_detail) {
            // Ambil order terkait
            $order = $order_detail->order;

            // Hitung ulang subtotal berdasarkan order_details
            $subtotal = 0;
            foreach ($order->details as $detail) {
                $subtotal += $detail->price;
            }

            // Hitung diskon
            $discount = $order->discount_value;
            if ($order->discount_type == 'percentage') {
                $discount = $subtotal * ($order->discount_value / 100);
            }

            // Set subtotal, total diskon, dan total price
            $order->total_discount = $discount;
            $order->subtotal_price = $subtotal;
            $order->total_price    = $subtotal - $discount;

            // Update order dengan nilai yang baru
            $order->updateQuietly([
                'subtotal_price' => $subtotal,
                'total_discount' => $discount,
                'total_price'    => $subtotal - $discount,
            ]);
        });
    }

    /**
     * Get the order that owns the OrderDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Get the product that owns the OrderDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the worker that owns the OrderDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }
}
