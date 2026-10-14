<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'title',
        'category',
        'ticket_price',
        'description',
        'image',
    ];

    /**
     * Relasi ke detail pesanan tiket.
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'event_id');
    }

    /**
     * Accessor & Mutator alias (price -> ticket_price) untuk backward-compatibility.
     */
    public function getPriceAttribute()
    {
        return $this->attributes['ticket_price'] ?? 0;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['ticket_price'] = $value;
    }

    /**
     * Accessor & Mutator alias (name -> title).
     */
    public function getNameAttribute()
    {
        return $this->attributes['title'] ?? '';
    }

    public function setNameAttribute($value)
    {
        $this->attributes['title'] = $value;
    }
}
