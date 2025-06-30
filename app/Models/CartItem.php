<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product;

/**
 * Модель элемента корзины
 *
 * @package App\Models
 * @property int $id ID элемента корзины
 * @property int $cart_id ID корзины
 * @property int $product_id ID товара
 * @property int $quantity Количество товара
 * @property float $price Цена товара на момент добавления в корзину
 * @property \Illuminate\Support\Carbon $created_at Дата создания
 * @property \Illuminate\Support\Carbon $updated_at Дата обновления
 * @property-read Cart $cart Корзина, к которой принадлежит элемент
 * @property-read Product $product Товар
 * @property-read float $subtotal Стоимость элемента (цена × количество)
 */
class CartItem extends Model
{
    protected $guarded = false;

    /**
     * Получить корзину, к которой принадлежит элемент
     *
     * @return BelongsTo
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Получить связанный товар
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Вычислить стоимость элемента корзины
     *
     * @return float Общая стоимость (цена × количество)
     */
    public function getSubtotalAttribute(): float
    {
        return round($this->price * $this->quantity,2);
    }
}
