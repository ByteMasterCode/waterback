<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Валидация входящих данных
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|unique:users',
            'fullname' => 'required|string',
            'phoneNumber' => 'required|unique:users',
            'password' => 'required|string|min:6',
            'address' => 'nullable|string',
            'location' => 'nullable|json',
            'telegram_id' => 'required|unique:users',
        ]);

        // Если валидация не прошла, возвращаем сообщение об ошибке
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Создаем нового пользователя
        $user = User::create([
            'chat_id' => $request->chat_id,
            'fullname' => $request->fullname,
            'phoneNumber' => $request->phoneNumber,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'location' => $request->location,
            'telegram_id' => $request->telegram_id,
        ]);

        // Аутентифицируем пользователя и генерируем токен
        $token = JWTAuth::fromUser($user);

        // Возвращаем успешный ответ с токеном и данными пользователя
        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        // Проверяем входящие данные
        $credentials = $request->only('phoneNumber', 'password');

        // Проверяем, существует ли пользователь с таким номером телефона
        $user = User::where('phoneNumber', $credentials['phoneNumber'])->first();

        // Если пользователь не найден или пароль неверен, возвращаем сообщение об ошибке
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'Invalid phone number or password'], 401);
        }

        // Аутентифицируем пользователя
        $token = Auth::login($user);

        // Возвращаем токен и информацию о пользователе для успешного входа
        return response()->json(['token' => $token, 'user' => $user]);
    }



}
