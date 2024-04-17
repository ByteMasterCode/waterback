<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index(Request $request)
    {
        $language = $request->input('language');
        if (!$language) {
            return Slider::with('language', 'category','news')->get();
        }

        // Иначе фильтруем категории по языковому коду
        return Slider::whereHas('language', function ($query) use ($language) {
            $query->where('code', $language);
        })->with('language', 'category','news')->get();

    }

    public function store(Request $request)
    {
        $request->validate([
            'cover' => 'nullable|string',
            'description' => 'nullable|string',
            'language_id' => 'required|exists:languages,id',
            'brief_description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'news_id' => 'nullable|exists:news,id',
        ]);

        return Slider::create($request->all());
    }

    public function show($id)
    {
        return Slider::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $slider = Slider::findOrFail($id);
        $request->validate([
            'cover' => 'nullable|string',
            'description' => 'nullable|string',
            'language_id' => 'required|exists:languages,id',
            'brief_description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'news_id' => 'nullable|exists:news,id',
        ]);

        $slider->update($request->all());

        return $slider;
    }

    public function destroy($id)
    {
        $slider = Slider::findOrFail($id);
        $slider->delete();

        return response()->json(null, 204);
    }
}
