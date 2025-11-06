<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => '山田太郎（管理者）',
                'email' => 'taro@yamada.com',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '山田花子（一般ユーザー）',
                'email' => 'hanako@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '佐藤三郎（一般ユーザー）',
                'email' => 'sato@sato.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
