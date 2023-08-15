<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Web_type_featureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('web_type_features')->insert([
            [
                'memo_id' => 1,
                'url' => 'https://readouble.com/laravel/9.x/ja/migrations.html#column-method-integer',
            ],
            [
                'memo_id' => 3,
                'url' => 'https://www.udemy.com/'
            ],
        ]);
    }
}
