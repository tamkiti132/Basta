<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Faker\Provider\DateTime;
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
            ],
            [
                'email' => 'test2@test.com',
                'password' => Hash::make('password2'),
                'nickname' => 'はなこ',
                'suspension_state' => 0,
            ],
            [
                'email' => 'test3@test.com',
                'password' => Hash::make('password3'),
                'nickname' => '二郎さん',
                'suspension_state' => 1,
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert([
                'email' => $user['email'],
                'password' => $user['password'],
                'nickname' => $user['nickname'],
                'suspension_state' => $user['suspension_state'],
                'username' => '@' . (string) Str::ulid(),
                'created_at' => DateTime::dateTimeThisDecade(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
