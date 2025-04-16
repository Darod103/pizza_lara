<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\ProductStoreRequest;
use App\Http\Requests\Api\Product\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Api\ProductServices;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProductServices::getAll();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        $product = $request->validated();
        $product = ProductServices::storeProduct($product);
        return ProductServices::showProduct($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): array
    {
        return ProductServices::showProduct($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $updatedProduct = $request->validated();
        $product = ProductServices::updateProduct($product, $updatedProduct);
        return ProductServices::showProduct($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        ProductServices::destroyProduct($product);
        return response()->json([
            "message" => "Product has been deleted"
        ], Response::HTTP_OK);
    }
}
