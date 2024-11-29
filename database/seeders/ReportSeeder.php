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
                'type' => 1,
                'reason' => 1,
                'detail' => 'このユーザーは、◯◯◯の法律に違反した行為をさまざまなグループ内で行なっています。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 3,
                'type' => 2,
                'reason' => 2,
                'detail' => '手順１の、〜〜〜という記載が、〜〜〜という理由でよくないと思います。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 3,
                'type' => 3,
                'reason' => 3,
                'detail' => 'このコメントに記載してあるURLがフィッシングサイトにつながります!',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 2,
                'type' => 4,
                'reason' => 4,
                'detail' => 'これは詳細文ですこれは詳細文ですこれは詳細文です',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 1,
                'type' => 1,
                'reason' => 4,
                'detail' => 'bbbbbbbbb',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 3,
                'type' => 2,
                'reason' => 3,
                'detail' => 'たろうさんのメモに対するレポートです',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 2,
                'type' => 2,
                'reason' => 3,
                'detail' => 'これはテストレポート1です。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 2,
                'type' => 2,
                'reason' => 3,
                'detail' => 'これはテストレポート2です。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 2,
                'type' => 2,
                'reason' => 3,
                'detail' => 'これはテストレポート3です。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 2,
                'type' => 2,
                'reason' => 3,
                'detail' => 'これはテストレポート4です。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 2,
                'type' => 2,
                'reason' => 3,
                'detail' => 'これはテストレポート5です。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 2,
                'type' => 2,
                'reason' => 3,
                'detail' => 'これはテストレポート6です。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 2,
                'type' => 2,
                'reason' => 3,
                'detail' => 'これはテストレポート7です。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 2,
                'type' => 2,
                'reason' => 3,
                'detail' => 'これはテストレポート8です。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 2,
                'type' => 2,
                'reason' => 3,
                'detail' => 'これはテストレポート9です。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 2,
                'type' => 2,
                'reason' => 3,
                'detail' => 'これはテストレポート10です。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 2,
                'type' => 2,
                'reason' => 3,
                'detail' => 'これはテストレポート11です。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 2,
                'type' => 2,
                'reason' => 3,
                'detail' => 'これはテストレポート12です。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 2,
                'type' => 3,
                'reason' => 2,
                'detail' => 'これはテストレポート13です。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 3,
                'type' => 3,
                'reason' => 3,
                'detail' => 'これはテストレポート14です。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 1,
                'type' => 1,
                'reason' => 4,
                'detail' => 'これはテストレポート15です。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'contribute_user_id' => 2,
                'type' => 3,
                'reason' => 4,
                'detail' => 'これはテストレポート16です。',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],

        ]);
    }
}
