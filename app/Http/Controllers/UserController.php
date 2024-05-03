<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index()
    {
        // Получаем всех пользователей вместе с их ролями
        $usersWithRoles = User::with('roles')->get();

        // Возвращаем JSON с пользователями и их ролями
        return response()->json($usersWithRoles);
    }

    public function update(Request $request, $id)
    {

        // Находим пользователя по его ID
        $user = User::findOrFail($id);

        // Обновляем профиль пользователя
        $user->update($request->only(['fullname', 'phoneNumber', 'address', 'location', 'telegram_id']));

        // Если пароль был отправлен в запросе, обновляем его
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Возвращаем успешный ответ
        return response()->json(['message' => 'User profile updated successfully'], 200);
    }


    public function updateProfile(Request $request)
    {
        // Получаем текущего аутентифицированного пользователя
        $user = Auth::user();

        // Проверяем, были ли отправлены новые данные для обновления
        if ($request->has('fullname')) {
            $user->fullname = $request->fullname;
        }

        if ($request->has('phoneNumber')) {
            $user->phoneNumber = $request->phoneNumber;
        }

        if ($request->has('address')) {
            $user->address = $request->address;
        }

        if ($request->has('location')) {
            $user->location = $request->location;
        }

        if ($request->has('telegram_id')) {
            $user->telegram_id = $request->telegram_id;
        }

        // Сохраняем обновленные данные
        $user->save();

        // Возвращаем успешный ответ
        return response()->json(['message' => 'User profile updated successfully'], 200);
    }


    public function changePassword(Request $request)
    {
        // Валидация входящих данных
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        // Если валидация не прошла, возвращаем сообщение об ошибке
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Получаем текущего аутентифицированного пользователя
        $user = Auth::user();

        // Проверяем, что старый пароль совпадает с текущим паролем пользователя
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['error' => 'Old password is incorrect'], 400);
        }

        // Обновляем пароль пользователя
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Возвращаем успешный ответ
        return response()->json(['message' => 'Password changed successfully'], 200);
    }

}
