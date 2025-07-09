<?php

namespace App\Services\Api;

use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Сервис для работы с продуктами (создание, обновление, удаление, получение).
 */
class ProductServices
{
    /**
     * Получить список всех продуктов с пагинацией.
     *
     * @return ResourceCollection
     */
    public static function getAll(): ResourceCollection
    {
        return ProductResource::collection(Product::paginate(20));
    }

    /**
     * Получить данные конкретного продукта.
     *
     * @param Product $product
     * @return array
     */
    public static function showProduct(Product $product): array
    {
        return ProductResource::make($product)->resolve();
    }

    /**
     * Сохранить новый продукт.
     *
     * @param array $data
     * @return array
     */
    public static function storeProduct(array $data): array
    {
        $image = $data['image'] ?? null;
        unset($data['image']);

        $product = Product::create($data);

        if ($image instanceof UploadedFile) {
            $categoryFolder = Str::slug($product->category->name);
            $path = $image->store("images/{$categoryFolder}", 'public');
            $product->images()->create(['path' => $path]);
        }

        return ProductResource::make($product->refresh())->resolve();
    }

    /**
     * Обновить данные продукта.
     *
     * @param Product $product
     * @param array $data
     * @return array
     */
    public static function updateProduct(Product $product, array $data): array
    {

        $image = $data['image'] ?? null;
        unset($data['image']);

        $product->update($data);

        if ($image instanceof UploadedFile) {
            $categoryFolder = Str::slug($product->category->name);
            $path = $image->store("images/{$categoryFolder}", 'public');
            if ($product->image && Storage::disk('public')->exists($product->image->path)) {
                Storage::disk('public')->delete($product->image->path);
                $product->images()->update(['path' => $path]);
            } else {
                $product->images()->create(['path' => $path]);
            }
        }

        return ProductResource::make($product->refresh())->resolve();
    }

    /**
     * Удалить продукт и его изображение, если оно есть.
     *
     * @param Product $product
     * @return void
     */
    public static function destroyProduct(Product $product): void
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
    }
}
