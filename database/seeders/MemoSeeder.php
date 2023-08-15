<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class MemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('memos')->insert([
            [
                'user_id' => 1,
                'group_id' => 1,
                'title' => 'マイグレーションについて',
                'shortMemo' => 'マイグレーションについてすごくわかりやすくまとめられてます!',
                'type' => 0,
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'user_id' => 2,
                'group_id' => 2,
                'title' => 'リュウジ式至高のレシピ',
                'shortMemo' => '初めての料理本におすすめです!!',
                'type' => 1,
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'user_id' => 3,
                'group_id' => 3,
                'title' => 'プログラミングの教材として最もおすすめのサイト',
                'shortMemo' => 'プログラミングのことは、ほぼこのサイトで学びました!!',
                'type' => 0,
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
        ]);
    }
}
