<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Group_type_report_linkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('group_type_report_links')->insert([
            [
                'report_id' => 4,
                'group_id' => 2,
            ],

        ]);
    }
}
