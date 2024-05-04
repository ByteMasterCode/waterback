<?php

namespace App\services;

use App\Models\CourierCard;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    /**
     * Создать клиента и присвоить ему роль "client".
     *
     * @param array $userData
     * @return User
     */
    public static function createClient(array $userData)
    {

        // Проверяем наличие ключа "location" в массиве данных пользователя
        $location = $userData['location'] ?? null;
        // Создаем клиента
        $client = User::create([
            'chat_id' => $userData['chat_id'],
            'fullname' => $userData['fullname'],
            'phoneNumber' => $userData['phoneNumber'],
            'password' => Hash::make($userData['password']),
            'address' => $userData['address'],
            'location' => $location,
            'telegram_id' => $userData['telegram_id'],
        ]);

        // Присваиваем роль "client"
        $client->assignRole('client');

        return $client;
    }


    /**
     * Создать пользователя (курьера или клиента) и присвоить ему роль.
     *
     * @param array $userData
     * @param string $role
     * @return User
     */
    public static function createUserWithRole(array $userData, string $role)
    {
        // Проверяем наличие ключа "location" в массиве данных пользователя
        $location = $userData['location'] ?? null;
        // Создаем пользователя
        $user = User::create([
            'chat_id' => $userData['chat_id'],
            'fullname' => $userData['fullname'],
            'phoneNumber' => $userData['phoneNumber'],
            'password' => Hash::make($userData['password']),
            'address' => $userData['address'],
            'location' => $location,
            'telegram_id' => $userData['telegram_id'],
        ]);

        // Присваиваем роль
        $user->assignRole($role);

        // Если роль - курьер, создаем для него карту курьера
        if ($role === 'courier') {
            self::createCourierCard($user);
        }

        return $user;
    }

    /**
     * Создать карту курьера для указанного пользователя.
     *
     * @param User $user
     * @return CourierCard
     */
    private static function createCourierCard(User $user)
    {
        return CourierCard::create([
            'user_id' => $user->id,
            'status' => 'active',
            'cash' => 0,
        ]);
    }


    public static function updateUserRole(User $user, $roleName)
    {
        // Находим роль по имени
        $role = Role::where('name', $roleName)->first();

        // Если роль не найдена, возвращаем ошибку
        if (!$role) {
            throw new \Exception("Role '$roleName' not found");
        }

        // Удаляем все текущие роли пользователя
        $user->roles()->detach();

        // Присваиваем новую роль пользователю
        $user->assignRole($role);
    }
}
