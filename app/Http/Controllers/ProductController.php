<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $languageCode = $request->input('language');

        if (!$languageCode) {
            return Product::with('language')->get();
        }
        return Product::whereHas('language', function ($query) use ($languageCode) {
            $query->where('code', $languageCode);
        })->with('language','brands','language','categories')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'isSale' => 'boolean',
            'topicons' => 'array',
            'brands_id' => 'required|exists:brands,id',
            'categories_id' => 'required|exists:categories,id',
            'language_id' => 'required|exists:languages,id',
            'isCashback' => 'boolean',
            'cashback_price' => 'required|numeric',
            'cover' => 'array',
            'description' => 'nullable',
            'brief_description' => 'nullable',
        ]);

        return Product::create($request->all());
    }

    public function show($id)
    {
        return Product::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $request->validate([
            'price' => 'numeric',
            'isSale' => 'boolean',
            'topicons' => 'array',
            'cashback_price' => 'required|numeric',
            'brands_id' => 'exists:brands,id',
            'language_id' => 'exists:languages,id',
            'isCashback' => 'boolean',
            'cover' => 'array',
        ]);

        $product->update($request->all());

        return $product;
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(null, 204);
    }
}
