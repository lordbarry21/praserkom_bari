<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];    
    
    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
}

