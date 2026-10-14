<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    protected $fillable = [
        'order_id',
        'event_id',
        'quantity',
        'subtotal',
    ];

    /**
     * Relasi ke master data event/konser.
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Relasi ke header transaksi pesanan.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Accessor & Mutator alias (price -> subtotal) untuk backward-compatibility.
     */
    public function getPriceAttribute()
    {
        return $this->attributes['subtotal'] ?? 0;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['subtotal'] = $value;
    }

    /**
     * Alias food() untuk kompatibilitas template lama bila ada.
     */
    public function food()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
