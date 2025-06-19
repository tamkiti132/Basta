<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Later_readSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('later_reads')->insert([
            [
                'user_id' => 1,
                'memo_id' => 2,
            ],
        ]);
    }
}
