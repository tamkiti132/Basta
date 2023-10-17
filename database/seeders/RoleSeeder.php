<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'user_id' => '1',
                'group_id' => '1',
                'role' => '10',
            ],
            [
                'user_id' => '2',
                'group_id' => '2',
                'role' => '10',
            ],
            [
                'user_id' => '3',
                'group_id' => '3',
                'role' => '10',
            ],
            [
                'user_id' => '1',
                'group_id' => '2',
                'role' => '100',
            ],
            [
                'user_id' => '3',
                'group_id' => '1',
                'role' => '50',
            ],
            [
                'user_id' => '4',
                'group_id' => null,
                'role' => '3',
            ],
            [
                'user_id' => '5',
                'group_id' => null,
                'role' => '5',
            ],
            [
                'user_id' => '6',
                'group_id' => null,
                'role' => '5',
            ],
            [
                'user_id' => '7',
                'group_id' => null,
                'role' => '5',
            ],
            [
                'user_id' => '8',
                'group_id' => null,
                'role' => '5',
            ],
        ]);
    }
}
