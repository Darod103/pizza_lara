<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $guarded = false;


    public function cartItems(): hasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): morphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function getImgUrlAttribute(): array
    {
        $imagesArray = $this->images->pluck('path')->toArray();
        return array_map(function ($image) {
            return Storage::disk('public')->url($image);
        }, $imagesArray);
    }
}
