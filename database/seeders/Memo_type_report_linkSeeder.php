<?php

namespace Database\Seeders;

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
            [
                'report_id' => 7,
                'memo_id' => 1,
            ],
            [
                'report_id' => 8,
                'memo_id' => 1,
            ],
            [
                'report_id' => 9,
                'memo_id' => 1,
            ],
            [
                'report_id' => 10,
                'memo_id' => 1,
            ],
            [
                'report_id' => 11,
                'memo_id' => 1,
            ],
            [
                'report_id' => 12,
                'memo_id' => 1,
            ],
            [
                'report_id' => 13,
                'memo_id' => 1,
            ],
            [
                'report_id' => 14,
                'memo_id' => 1,
            ],
            [
                'report_id' => 15,
                'memo_id' => 1,
            ],
            [
                'report_id' => 16,
                'memo_id' => 1,
            ],
            [
                'report_id' => 17,
                'memo_id' => 1,
            ],
            [
                'report_id' => 18,
                'memo_id' => 1,
            ],
        ]);
    }
}
