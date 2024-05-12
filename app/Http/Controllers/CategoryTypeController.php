<?php

namespace App\Http\Controllers;

use App\Models\CategoryType;
use Illuminate\Http\Request;

class CategoryTypeController extends Controller
{
    public function index(Request $request)
    {
        $languageCode = $request->input('language');

        if (!$languageCode) {
            return CategoryType::with('language')->get();
        }
        return CategoryType::whereHas('language', function ($query) use ($languageCode) {
            $query->where('code', $languageCode);
        })->with('language','categories.icon')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'language_id' => 'required|exists:languages,id',
            'icon'=>'required'
        ]);

        return CategoryType::create($request->all());
    }

    public function show($id)
    {
        return CategoryType::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $categoryType = CategoryType::findOrFail($id);
        $categoryType->update($request->all());

        return $categoryType;
    }

    public function destroy($id)
    {
        $categoryType = CategoryType::findOrFail($id);
        $categoryType->delete();

        return 204;
    }
}
