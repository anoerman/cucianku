<?php

namespace App\Models;

use Faker\Provider\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_code',
        'order_date',
        'customer_id',
        'status',
        'discount_type',
        'discount_value',
        'total_discount',
        'subtotal_price',
        'total_price',
        'remarks',
        'worker_id',
    ];

    /**
     * Get the customer that owns the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the worker that owns the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    /**
     * Get all of the details for the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }


    public static function createOrder(array $data)
    {
        try {
            DB::beginTransaction();
            // Create order
            $order = Order::create([
                'order_code'     => Uuid::uuid(),
                'order_date'     => $data['order_date'],
                'customer_id'    => $data['customer_id'],
                'status'         => $data['status'],
                'discount_type'  => $data['discount_type'],
                'discount_value' => $data['discount_value'],
                'total_discount' => 0,
                'subtotal_price' => 0,
                'total_price'    => 0,
                'remarks'        => $data['remarks'],
                'worker_id'      => $data['worker_id'],
            ]);
            if (!$order) {
                throw new \Exception("No order found!", 404);
            }

            $subtotal_price   = 0;
            $product_inserted = 0;
            if ($order) {
                for ($i = 0; $i < count($data['products']); $i++) {
                    $pid     = $data['products'][$i];
                    $product = Product::find($pid['product_id']);
                    if (!$product) {
                        throw new \Exception("No product found with id " . $pid['product_id'] . "!", 404);
                    }
                    // Create order detail
                    $order_detail = OrderDetail::create([
                        'order_id'   => $order->id,
                        'worker_id'  => $data['worker_id'],
                        'product_id' => $pid['product_id'],
                        'qty'        => $pid['qty'],
                        'price'      => $product->price * $pid['qty'] ?? 0,
                    ]);

                    if ($order_detail) {
                        $product_inserted++;
                        $subtotal_price = $subtotal_price + $order_detail->price;
                    }
                }
            }

            if (count($data['products']) != $product_inserted) {
                throw new \Exception("Failed to process some of the products!", 404);
            }

            // Update order
            $discount = $order->discount_value;
            if ($order->discount_type == 'percentage') {
                $discount = $subtotal_price * ($order->discount_value / 100);
            }
            $order->total_discount = $discount;
            $order->subtotal_price = $subtotal_price;
            $order->total_price    = $subtotal_price - $discount;
            $order->save();

            DB::commit();
            return $order;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage() . "#" . $th->getLine();
        }
    }


    public static function getLastNDaysIncome(int $num_days = 7): float
    {
        $sd = '';
        $ed = '';
        for ($i = 0; $i < $num_days; $i++) {
            $current_date = date('Y-m-d', strtotime($i . ' days ago'));
            if ($i == 0) {
                $ed = $current_date;
            }
            if ($i == ($num_days-1)) {
                $sd = $current_date;
            }
        }
        $data = Order::where([
            ['order_date', '>=', $sd . ' 00:00:00'],
            ['order_date', '<=', $ed . ' 23:59:59']
        ])->sum('total_price');
        return $data;
    }

    /** 
     * Get last n days summary
     * 
     * @param       int     $num_days
     * @return      array
     * 
     */
    public static function getLastNDaysSummary(int $num_days = 7): array
    {
        $data   = [];
        $income = [];
        $labels = [];
        for ($i = 0; $i < $num_days; $i++) {
            $current_date = date('Y-m-d', strtotime($i . ' days ago'));
            $labels[]     = date('d M Y', strtotime($i . ' days ago'));
            $data[]       = Order::where([
                ['order_date', '>=', $current_date . ' 00:00:00'],
                ['order_date', '<=', $current_date . ' 23:59:59']
            ])->count();
            $income[] = Order::where([
                ['order_date', '>=', $current_date . ' 00:00:00'],
                ['order_date', '<=', $current_date . ' 23:59:59']
            ])->sum('total_price');
        }
        return [
            'data'   => array_reverse($data),
            'labels' => array_reverse($labels)
        ];
    }
}
