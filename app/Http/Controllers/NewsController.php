<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $languageCode = $request->input('language');

        if (!$languageCode) {
            return News::with('language')->get();
        }
        return News::whereHas('language', function ($query) use ($languageCode) {
            $query->where('code', $languageCode);
        })->with('language')->get();
    }


    public function store(Request $request)
    {
        $request->validate([
            'cover' => 'nullable|string',
            'description' => 'nullable|string',
            'language_id' => 'required|exists:languages,id',
            'brief_description' => 'nullable|string',
            'type' => 'nullable|string',
        ]);

        return News::create($request->all());
    }

    public function show($id)
    {
        return News::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);
        $news->update($request->all());

        return $news;
    }
    public function destroy($id)
    {
        $news = News::findOrFail($id);
        $news->delete();

        return response()->json(null, 204);
    }

}
