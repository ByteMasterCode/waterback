<?php

namespace App\Http\Controllers;

use App\Models\HistoryManager;
use App\Models\User;
use App\services\CourierCardController;
use App\services\UserService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UsersHandleController extends Controller
{

    /**
     * Создать нового клиента.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createClient(Request $request)
    {
        // Правила валидации
        $rules = [
            'chat_id' => 'required|unique:users',
            'fullname' => 'required',
            'phoneNumber' => 'required|unique:users',
            'password' => 'required',
            'address' => 'nullable',
            'location' => 'nullable',
            'telegram_id' => 'nullable|unique:users',
        ];

        // Проверяем входные данные
        $validator = Validator::make($request->all(), $rules);

        // Если валидация не пройдена, возвращаем ошибку
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Создаем клиента
        $client = UserService::createClient($request->all());
        if (auth()->check()) {
            // Получаем аутентифицированного пользователя через auth()->user() или JWTAuth::user()
            $user = auth()->user();
            HistoryManager::create([
                'actions' => 'created',
                'description' => 'Foydalanuvchi yartildi | ismi : ' .$client->fullname.' telegram id : '.$client->telegram_id.' tel :  '.$client->phoneNumber.' yaratgan  foydalanuvchi : '.$user->fullname,
            ]);
        } else {
            // Возвращаем сообщение, если пользователь не авторизован
            return response()->json(['message' => 'User is not authenticated'], 403);
        }
        // Возвращаем успешный ответ и данные о созданном клиенте
        return response()->json(['client' => $client], 201);
    }

    /**
     * Создать нового пользователя с ролью "courier".
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCourier(Request $request)
    {
        // Правила валидации
        $rules = [
            'chat_id' => 'required|unique:users',
            'fullname' => 'required',
            'phoneNumber' => 'required|unique:users',
            'password' => 'required',
            'address' => 'nullable',
            'location' => 'nullable',
            'telegram_id' => 'nullable|unique:users',
        ];

        // Проверяем входные данные
        $validator = Validator::make($request->all(), $rules);

        // Если валидация не пройдена, возвращаем ошибку
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Создаем курьера с помощью сервиса UserService
        $courier = UserService::createUserWithRole($request->all(), 'courier');

        if (auth()->check()) {
            // Получаем аутентифицированного пользователя через auth()->user() или JWTAuth::user()
            $user = auth()->user();
            HistoryManager::create([
                'actions' => 'created',
                'description' => 'Kuryer yartildi | ismi : ' .$courier->fullname.' telegram id : '.$courier->telegram_id.' tel :  '.$courier->phoneNumber.' yaratgan  foydalanuvchi : '.$user->fullname,
            ]);
        } else {
            // Возвращаем сообщение, если пользователь не авторизован
            return response()->json(['message' => 'User is not authenticated'], 403);
        }
        // Возвращаем успешный ответ и данные о созданном курьере
        return response()->json(['courier' => $courier], 201);
    }


    /**
     * Создать нового пользователя с ролью "admin".
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAdmin(Request $request)
    {
        // Правила валидации
        $rules = [
            'chat_id' => 'required|unique:users',
            'fullname' => 'required',
            'phoneNumber' => 'required|unique:users',
            'password' => 'required',
            'address' => 'nullable',
            'location' => 'nullable',
            'telegram_id' => 'nullable|unique:users',
        ];

        // Проверяем входные данные
        $validator = Validator::make($request->all(), $rules);

        // Если валидация не пройдена, возвращаем ошибку
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Создаем администратора с помощью сервиса UserService
        $admin = UserService::createUserWithRole($request->all(), 'admin');
        if (auth()->check()) {
            // Получаем аутентифицированного пользователя через auth()->user() или JWTAuth::user()
            $user = auth()->user();
            HistoryManager::create([
                'actions' => 'created',
                'description' => 'Admin yartildi | ismi : ' .$admin->fullname.' telegram id : '.$admin->telegram_id.' tel :  '.$admin->phoneNumber.' yaratgan  foydalanuvchi : '.$user->fullname,
            ]);
        } else {
            // Возвращаем сообщение, если пользователь не авторизован
            return response()->json(['message' => 'User is not authenticated'], 403);
        }
        // Возвращаем успешный ответ и данные о созданном администраторе
        return response()->json(['admin' => $admin], 201);
    }

    /**
     * Получить всех пользователей с ролью "админ" в формате JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAdmins()
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        return response()->json(['admins' => $admins], 200);
    }

    /**
     * Получить всех пользователей с ролью "client" в формате JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClients()
    {
        $clients = User::whereHas('roles', function ($query) {
            $query->where('name', 'client');
        })->get();

        return response()->json(['clients' => $clients], 200);
    }

    /**
     * Получить всех пользователей с ролью "courier" в формате JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCouriers()
    {
        // Получаем всех курьеров
        $couriers = User::whereHas('roles', function ($query) {
            $query->where('name', 'courier');
        })->get();

        $couriersWithCards = [];

        // Для каждого курьера находим его карту курьера
        foreach ($couriers as $courier) {
            // Находим карту курьера по ID пользователя
            $card = CourierCardController::findCourierCardByUserId($courier->id);

            // Если карта курьера найдена, добавляем курьера и его карту курьера в массив
            if ($card) {
                $couriersWithCards[] = [
                    'courier' => $courier,
                    'card' => $card,
                ];
            }
        }

        // Возвращаем JSON с курьерами и их картами курьера
        return response()->json($couriersWithCards);
    }
}
