<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'email' => 'test1@test.com',
                'password' => Hash::make('password1'),
                'nickname' => 'たろう',
                'suspension_state' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'email' => 'test2@test.com',
                'password' => Hash::make('password2'),
                'nickname' => 'はなこ',
                'suspension_state' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'email' => 'test3@test.com',
                'password' => Hash::make('password3'),
                'nickname' => '二郎さん',
                'suspension_state' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'email' => 'admin0@admin.com',
                'password' => Hash::make('password_admin0'),
                'nickname' => '運営トップ太郎',
                'suspension_state' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'email' => 'admin1@admin.com',
                'password' => Hash::make('password_admin1'),
                'nickname' => '運営太郎',
                'suspension_state' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'email' => 'admin2@admin.com',
                'password' => Hash::make('password_admin2'),
                'nickname' => 'うんえい子',
                'suspension_state' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'email' => 'admin88@admin.com',
                'password' => Hash::make('password_admin88'),
                'nickname' => '運営悪男',
                'suspension_state' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'email' => 'admin321@admin.com',
                'password' => Hash::make('password_admin321'),
                'nickname' => 'うんえい悪子',
                'suspension_state' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'email' => 'test333@test.com',
                'password' => Hash::make('password333'),
                'nickname' => 'テスト333',
                'suspension_state' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert([
                'email' => $user['email'],
                'password' => $user['password'],
                'nickname' => $user['nickname'],
                'suspension_state' => $user['suspension_state'],
                'username' => '@' . (string) Str::ulid(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
