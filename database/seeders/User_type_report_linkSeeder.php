<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class User_type_report_linkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_type_report_links')->insert([
            [
                'report_id' => 1,
                'user_id' => 1,
            ],
            [
                'report_id' => 5,
                'user_id' => 2,
            ],
            [
                'report_id' => 21,
                'user_id' => 2,
            ],

        ]);
    }
}
