<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Memo_labelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('memo_label')->insert([
            [
                'memo_id' => 1,
                'label_id' => 1,
            ],
            [
                'memo_id' => 2,
                'label_id' => 3,
            ],
            [
                'memo_id' => 3,
                'label_id' => 5,
            ],
            [
                'memo_id' => 3,
                'label_id' => 6,
            ],
        ]);
    }
}
