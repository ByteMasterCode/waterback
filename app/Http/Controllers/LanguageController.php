<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LanguageController extends Controller
{
    public function index()
    {
        return Language::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required',
        ]);

        return Language::create($request->all());
    }

    public function show($id)
    {
        return Language::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $language = Language::findOrFail($id);
        $language->update($request->all());

        return $language;
    }

    public function destroy($id)
    {
        $language = Language::findOrFail($id);
        $language->delete();

        return 204;
    }
}
