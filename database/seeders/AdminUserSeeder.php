<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Генерируем сложный пароль
        $password =  Str::random(12);
        $hashed = bcrypt($password);
        // Создаем пользователя с ролью admin
        User::create([
            'chat_id' => 0,
            'fullname' => 'Admin OsonExpress',
            'phoneNumber' => '88777788',
            'password' => $hashed,
            'address' => 'OsonExpress',
            'location' => null,
            'telegram_id' => 0,
        ])->assignRole('admin');
        $this->command->info("Admin created with password: $password");
    }
}
