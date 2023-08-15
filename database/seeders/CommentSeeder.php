<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('Comments')->insert([
            [
                'comment' => '特におすすめのレシピはどれですか？',
                'user_id' => 1,
                'memo_id' => 2,
            ],
            [
                'comment' => 'このサイトと合わせて、Udemyの教材を活用するといいと思います。',
                'user_id' => 2,
                'memo_id' => 1,
            ],
        ]);
    }
}
