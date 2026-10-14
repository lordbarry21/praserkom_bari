<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'customer_name',
        'customer_email',
        'total_price',
        'status',
    ];

    /**
     * Relasi ke rincian pesanan tiket konser.
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    /**
     * Alias relasi details() untuk kemudahan pemanggilan.
     */
    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
}
