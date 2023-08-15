<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('labels')->insert([
            [
                'name' => 'Laravel',
                'group_id' => 1,
            ],
            [
                'name' => '設計',
                'group_id' => 1,
            ],
            [
                'name' => 'レシピ',
                'group_id' => 2,
            ],
            [
                'name' => 'キッチングッズ',
                'group_id' => 2,
            ],
            [
                'name' => '教材',
                'group_id' => 3,
            ],
            [
                'name' => 'プログラミング',
                'group_id' => 3,
            ],
        ]);
    }
}
