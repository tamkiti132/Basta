<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('goods')->insert([
            [
                'user_id' => 1,
                'memo_id' => 2,
            ],
            [
                'user_id' => 3,
                'memo_id' => 1,
            ],
        ]);
    }
}
