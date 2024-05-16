<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createAdmin();
        $this->createUser();
    }

    private function createAdmin(): void
    {
        $user = User::factory()->make([
           'type' => User::TYPES_ADMIN,
           'name' => 'مدیر اصلی',
           'email' => 'admin@admin.com',
           'password' => bcrypt('admin@123'),
           'mobile' => '+989111111111',
        ]);
        $user->save();
        $this->command->info('کاربر ادمین ایجاد شد');
    }

    private function createUser(): void
    {
        $user = User::factory()->make([
            'type' => User::TYPES_USER,
            'name' => 'کاربر عادی',
            'email' => 'user@user.com',
            'password' => bcrypt('user@123'),
            'mobile' => '+989222222222',
        ]);
        $user->save();
        $this->command->info('کاربر پیش فرض ایجاد شد');
    }
}
