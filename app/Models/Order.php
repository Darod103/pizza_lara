<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Order extends Model
{

    public function cart():BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function items():HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getTotalAttribute(): float
    {
        return $this->items->sum('subtotal');
    }


}
