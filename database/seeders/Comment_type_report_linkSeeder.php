<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Comment_type_report_linkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('comment_type_report_links')->insert([
            [
                'report_id' => 3,
                'comment_id' => 1,
            ],
            [
                'report_id' => 19,
                'comment_id' => 1,
            ],
            [
                'report_id' => 20,
                'comment_id' => 2,
            ],
            [
                'report_id' => 22,
                'comment_id' => 2,
            ],

        ]);
    }
}
