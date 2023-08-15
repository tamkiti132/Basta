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
            ],
            [
                'name' => 'はなこの料理教室',
                'introduction' => '日々の料理について学んだことをシェアしたいです!!',
                'isJoinFreeEnabled' => 1,
                'isTipEnabled' => 0,
            ],
            [
                'name' => '二郎の仲間たち',
                'introduction' => 'なんでも自由に書いてくださいー',
                'isJoinFreeEnabled' => 0,
                'isTipEnabled' => 1,
            ],
        ]);
    }
}
