<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index($language)
    {
        if (!$language) {
            return Product::with('type', 'icon')->get();
        }

        // Иначе фильтруем категории по языковому коду
        return Product::whereHas('language', function ($query) use ($language) {
            $query->where('code', $language);
        })->with('brand', 'language')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'isSale' => 'boolean',
            'topicons' => 'array',
            'brand_id' => 'required|exists:brands,id',
            'language_id' => 'required|exists:languages,id',
            'isCashback' => 'boolean',
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
            'brand_id' => 'exists:brands,id',
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
