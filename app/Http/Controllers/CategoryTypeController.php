<?php

namespace App\Http\Controllers;

use App\Models\CategoryType;
use Illuminate\Http\Request;

class CategoryTypeController extends Controller
{
    public function index()
    {
        return CategoryType::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
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
