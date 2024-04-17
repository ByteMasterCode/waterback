<?php

namespace App\Http\Controllers;

use App\Models\ImagesManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImagesManagerController extends Controller
{
    public function index()
    {
        return ImagesManager::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Добавляем валидацию для изображения
            'name' => 'required',
            'img_category' => 'required'
        ]);

        $imagePath = $request->file('image')->store('images'); // Сохраняем изображение в storage

        return ImagesManager::create([
            'path' => $imagePath, // Сохраняем путь к изображению
            'name' => $request->name,
            'img_category' => $request->img_category,
        ]);
    }

    public function show($id)
    {
        return ImagesManager::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Добавляем валидацию для изображения
            'name' => 'required',
            'img_category' => 'required',
        ]);

        $image = ImagesManager::findOrFail($id);

        if ($request->hasFile('image')) {
            // Удаляем старое изображение из storage
            Storage::delete($image->path);
            // Сохраняем новое изображение в storage
            $imagePath = $request->file('image')->store('images');
            $image->path = $imagePath; // Обновляем путь к изображению
        }

        $image->name = $request->name; // Обновляем имя изображения
        $image->img_category = $request->img_category; // Обновляем категорию изображения
        $image->save();

        return $image;
    }

    public function destroy($id)
    {
        $image = ImagesManager::findOrFail($id);
        $image->delete();

        return 204;
    }
}
