<?php

namespace Database\Seeders;

use Carbon\Carbon;
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
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'このサイトと合わせて、Udemyの教材を活用するといいと思います。',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => '↑アドバイスありがとうございます！',
                'user_id' => 1,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント１',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント２',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント３',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント４',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント５',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント６',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント７',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント８',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント９',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント１０',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント１１',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント１２',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント１３',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント１４',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント１５',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント１６',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント１７',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント１８',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント１９',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント２０',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'comment' => 'テストコメント２１',
                'user_id' => 2,
                'memo_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
