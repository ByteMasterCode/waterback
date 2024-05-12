<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index($language)
    {


        // Если языковой код не был передан, возвращаем все категории
        if (!$language) {
            return Category::with('type', 'icon')->get();
        }

        // Иначе фильтруем категории по языковому коду
        return Category::whereHas('language', function ($query) use ($language) {
            $query->where('code', $language);
        })->with('type', 'icon','products')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type_id' => 'required|exists:category_types,id',
            'language_id' => 'required|exists:languages,id',
            'icon_id' => 'required|exists:icons,id',
            'brief_description' => 'nullable',
        ]);

        return Category::create($request->all());
    }

    public function show($id)
    {
        return Category::with('type', 'icon','products')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $request->validate([
            'name' => 'required',
            'type_id' => 'required|exists:category_types,id',
            'language_id' => 'required|exists:languages,id',
            'icon_id' => 'required|exists:icons,id',
            'brief_description' => 'nullable',
        ]);

        $category->update($request->all());

        return $category;
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(null, 204);
    }
}
