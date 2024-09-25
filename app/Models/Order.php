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

    /** 
     * Get last n days income info
     * 
     * @param       int     $num_days
     * @return      float
     * 
     */
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
