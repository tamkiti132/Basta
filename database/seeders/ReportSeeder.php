<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('reports')->insert([
            [
                'contribute_user_id' => 2,
                'type' => 0,
                'reason' => 0,
                'detail' => 'このユーザーは、◯◯◯の法律に違反した行為をさまざまなグループ内で行なっています。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 3,
                'type' => 1,
                'reason' => 1,
                'detail' => '手順１の、〜〜〜という記載が、〜〜〜という理由でよくないと思います。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 3,
                'type' => 2,
                'reason' => 2,
                'detail' => 'このコメントに記載してあるURLがフィッシングサイトにつながります!',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 2,
                'type' => 3,
                'reason' => 3,
                'detail' => 'これは詳細文ですこれは詳細文ですこれは詳細文です',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 2,
                'type' => 0,
                'reason' => 3,
                'detail' => 'bbbbbbbbb',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 3,
                'type' => 1,
                'reason' => 2,
                'detail' => 'user2のメモに対するレポートです6',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],

        ]);
    }
}
