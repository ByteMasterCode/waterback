<?php

namespace App\Http\Controllers;

use App\Models\HistoryManager;
use Illuminate\Http\Request;

class HistoryManagerController extends Controller
{
    public function index()
    {
        return HistoryManager::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'actions' => 'required',
            'description' => 'nullable',
        ]);

        return HistoryManager::create($request->all());
    }

    public function show($id)
    {
        return HistoryManager::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $history = HistoryManager::findOrFail($id);
        $history->update($request->all());

        return $history;
    }

    public function destroy($id)
    {
        $history = HistoryManager::findOrFail($id);
        $history->delete();

        return 204;
    }
}
