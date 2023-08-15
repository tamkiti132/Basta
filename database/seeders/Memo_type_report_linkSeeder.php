<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Memo_type_report_linkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('memo_type_report_links')->insert([
            [
                'report_id' => 2,
                'memo_id' => 3,
            ],
            [
                'report_id' => 6,
                'memo_id' => 1,
            ],

        ]);
    }
}
