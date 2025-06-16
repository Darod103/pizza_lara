<?php

namespace App\Services\Api;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductServices
{
    public static function getAll(): ResourceCollection
    {
        return ProductResource::collection(Product::paginate(20));
    }

    public static function showProduct(Product $product): array
    {
        return ProductResource::make($product)->resolve();
    }

    public static function storeProduct(array $data): Product
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('images', 'public');
        }
        return Product::create($data);
    }

    public static function updateProduct(Product $product, array $data): Product
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $data['image']->store('images', 'public');
        }
        $product->update($data);
        return $product;
    }

    public static function destroyProduct(Product $product): void
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
    }
}
