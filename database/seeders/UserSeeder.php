<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء مستخدم Super Admin
        $superAdmin = User::create([
            'full_name' => 'Super Admin',
            'username' => 'admin',
            'email' => 'superadmin@app.com',
            'password' => bcrypt('12345678'), // استخدم كلمة مرور قوية في التطبيق الفعلي
        ]);

        $superAdmin->assignRole('Admin');

        // إنشاء مستخدم عادي (مثال)
        $user = User::create([
            'full_name' => 'Normal User',
            'username' => 'user',
            'email' => 'user@app.com',
            'password' => bcrypt('12345678'),
        ]);

        $user->assignRole('User');
    }
}
