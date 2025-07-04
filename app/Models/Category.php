<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public static function findByName(string $name): Category
    {
        return self::where('name', $name)->firstOrFail();
    }
}
