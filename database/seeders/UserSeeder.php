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
            'username' => 'superadmin',
            'email' => 'superadmin@app.com',
            'password' => bcrypt('password'), // استخدم كلمة مرور قوية في التطبيق الفعلي
        ]);

        $superAdmin->assignRole('Super Admin');

        // إنشاء مستخدم عادي (مثال)
        $user = User::create([
            'full_name' => 'Normal User',
            'username' => 'user',
            'email' => 'user@app.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('User');
    }
}
