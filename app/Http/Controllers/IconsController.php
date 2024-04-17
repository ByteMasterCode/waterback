<?php

namespace App\Http\Controllers;

use App\Models\Icon;
use Illuminate\Http\Request;

class IconsController extends Controller
{
    public function index()
    {
        return Icon::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'icon' => 'required', // Путь к изображению (URL)
            'index' => 'nullable|integer', // Индекс, если необходимо сортировать иконки
        ]);
        $history = new HistoryManagerController();
        $h_request = new Request([
            'actions' => 'created',
            'description' => ' icon-info name / '. $request->name . " url  / " . $request->icon . " index / " . $request->index
        ]);
        $history->store($h_request);
        return Icon::create($request->all());
    }

    public function show($id)
    {
        return Icon::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $icon = Icon::findOrFail($id);
        $icon->update($request->all());

        return $icon;
    }

    public function destroy($id)
    {
        $icon = Icon::findOrFail($id);
        $history = new HistoryManagerController();
        $h_request = new Request([
            'actions' => 'delete',
            'description' => ' icon-info name / '. $icon->name . " url  / " . $icon->icon . " index / " . $icon->index
        ]);
        $history->store($h_request);
        $icon->delete();

        return 204;
    }
}
