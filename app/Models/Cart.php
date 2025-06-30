<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Модель корзины покупок
 *
 * @package App\Models
 * @property int $id ID корзины
 * @property int $user_id ID пользователя
 * @property \Illuminate\Support\Carbon $created_at Дата создания
 * @property \Illuminate\Support\Carbon $updated_at Дата обновления
 * @property-read User $user Пользователь-владелец корзины
 * @property-read \Illuminate\Database\Eloquent\Collection<CartItem> $cartItems Элементы корзины
 * @property-read float $total Общая стоимость корзины
 */
class Cart extends Model
{
    protected $guarded = false;

    /**
     * Получить пользователя, которому принадлежит корзина
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить все элементы корзины
     *
     * @return HasMany
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Вычислить общую стоимость корзины
     *
     * @return float Общая стоимость всех товаров в корзине
     */
    public function getTotalAttribute(): float
    {
        return $this->cartItems->sum('subtotal');
    }
}
