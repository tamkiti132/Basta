<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('groups')->insert([
            [
                'name' => 'たろうのへや',
                'introduction' => '主にLaravelについての情報を共有します!!',
                'isJoinFreeEnabled' => 1,
                'isTipEnabled' => 1,
                'suspension_state' => 0,
            ],
            [
                'name' => 'はなこの料理教室',
                'introduction' => '日々の料理について学んだことをシェアしたいです!!',
                'isJoinFreeEnabled' => 1,
                'isTipEnabled' => 0,
                'suspension_state' => 0,
            ],
            [
                'name' => '二郎の仲間たち',
                'introduction' => 'なんでも自由に書いてくださいー',
                'isJoinFreeEnabled' => 0,
                'isTipEnabled' => 1,
                'suspension_state' => 1,
            ],
            [
                'name' => 'eeeee',
                'introduction' => 'eeeeeです。',
                'isJoinFreeEnabled' => 1,
                'isTipEnabled' => 1,
                'suspension_state' => 0,
            ],
            [
                'name' => 'グループ5',
                'introduction' => 'これはグループ5の紹介文です。',
                'isJoinFreeEnabled' => 1,
                'isTipEnabled' => 0,
                'suspension_state' => 0,
            ],
            [
                'name' => 'グループ6',
                'introduction' => 'これはグループ6の紹介文です。',
                'isJoinFreeEnabled' => 0,
                'isTipEnabled' => 1,
                'suspension_state' => 0,
            ],
            [
                'name' => 'グループ7',
                'introduction' => 'これはグループ7の紹介文です。',
                'isJoinFreeEnabled' => 1,
                'isTipEnabled' => 1,
                'suspension_state' => 0,
            ],
            [
                'name' => 'グループ8',
                'introduction' => 'これはグループ8の紹介文です。',
                'isJoinFreeEnabled' => 0,
                'isTipEnabled' => 0,
                'suspension_state' => 1,
            ],
            [
                'name' => 'グループ9',
                'introduction' => 'これはグループ9の紹介文です。',
                'isJoinFreeEnabled' => 1,
                'isTipEnabled' => 1,
                'suspension_state' => 0,
            ],
            [
                'name' => 'グループ10',
                'introduction' => 'これはグループ10の紹介文です。',
                'isJoinFreeEnabled' => 0,
                'isTipEnabled' => 1,
                'suspension_state' => 0,
            ],
            [
                'name' => 'グループ11',
                'introduction' => 'これはグループ11の紹介文です。',
                'isJoinFreeEnabled' => 1,
                'isTipEnabled' => 0,
                'suspension_state' => 0,
            ],
            [
                'name' => 'グループ12',
                'introduction' => 'これはグループ12の紹介文です。',
                'isJoinFreeEnabled' => 1,
                'isTipEnabled' => 1,
                'suspension_state' => 0,
            ],
            [
                'name' => 'グループ13',
                'introduction' => 'これはグループ13の紹介文です。',
                'isJoinFreeEnabled' => 0,
                'isTipEnabled' => 0,
                'suspension_state' => 0,
            ],
            [
                'name' => 'グループ14',
                'introduction' => 'これはグループ14の紹介文です。',
                'isJoinFreeEnabled' => 1,
                'isTipEnabled' => 1,
                'suspension_state' => 0,
            ],
            [
                'name' => 'グループ15',
                'introduction' => 'これはグループ15の紹介文です。',
                'isJoinFreeEnabled' => 0,
                'isTipEnabled' => 1,
                'suspension_state' => 0,
            ],
            [
                'name' => 'グループ16',
                'introduction' => 'これはグループ16の紹介文です。',
                'isJoinFreeEnabled' => 1,
                'isTipEnabled' => 0,
                'suspension_state' => 0,
            ],
            [
                'name' => 'グループ17',
                'introduction' => 'これはグループ17の紹介文です。',
                'isJoinFreeEnabled' => 1,
                'isTipEnabled' => 1,
                'suspension_state' => 0,
            ],
            [
                'name' => 'グループ18',
                'introduction' => 'これはグループ18の紹介文です。',
                'isJoinFreeEnabled' => 0,
                'isTipEnabled' => 0,
                'suspension_state' => 1,
            ],
            [
                'name' => 'グループ19',
                'introduction' => 'これはグループ19の紹介文です。',
                'isJoinFreeEnabled' => 1,
                'isTipEnabled' => 1,
                'suspension_state' => 0,
            ],
            [
                'name' => 'グループ20',
                'introduction' => 'これはグループ20の紹介文です。',
                'isJoinFreeEnabled' => 0,
                'isTipEnabled' => 1,
                'suspension_state' => 0,
            ],
            [
                'name' => 'グループ21',
                'introduction' => 'これはグループ21の紹介文です。',
                'isJoinFreeEnabled' => 1,
                'isTipEnabled' => 0,
                'suspension_state' => 0,
            ],
            [
                'name' => 'グループ22',
                'introduction' => 'これはグループ22の紹介文です。',
                'isJoinFreeEnabled' => 1,
                'isTipEnabled' => 1,
                'suspension_state' => 0,
            ],
            [
                'name' => 'グループ23',
                'introduction' => 'これはグループ23の紹介文です。',
                'isJoinFreeEnabled' => 0,
                'isTipEnabled' => 0,
                'suspension_state' => 1,
            ],
            [
                'name' => 'グループ24',
                'introduction' => 'これはグループ24の紹介文です。',
                'isJoinFreeEnabled' => 1,
                'isTipEnabled' => 1,
                'suspension_state' => 0,
            ],
            [
                'name' => 'グループ25',
                'introduction' => 'これはグループ25の紹介文です。',
                'isJoinFreeEnabled' => 0,
                'isTipEnabled' => 1,
                'suspension_state' => 0,
            ],
        ]);
    }
}
