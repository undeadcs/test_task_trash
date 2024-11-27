<?php

use App\Http\Requests\FindByPriceRequest;
use App\Http\Requests\FindByTitleRequest;
use App\Http\Resources\Product as ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('products', function () {
    return ProductResource::collection(Product::paginate());
});

Route::get('products/find-by-title', function (FindByTitleRequest $request) {
    return ProductResource::collection(Product::where('title', 'LIKE', '%'.$request->input('title').'%')->paginate());
});

Route::get('products/find-by-price', function (FindByPriceRequest $request) {
    return ProductResource::collection(
        Product::where('price', '>=', $request->input('price_from'))
            ->where('price', '<=', $request->input('price_to'))
            ->paginate()
    );
});

Route::get('products/{id}', function (string $id) {
    return new ProductResource(Product::findOrFail($id));
});
