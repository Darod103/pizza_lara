<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\ProductStoreRequest;
use App\Http\Requests\Api\Product\ProductUpdateRequest;
use App\Models\Product;
use App\Services\Api\ProductServices;
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
        return ProductServices::storeProduct($product);
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
        return ProductServices::updateProduct($product, $updatedProduct);
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
