<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Book_type_featureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('book_type_features')->insert([
            [
                'memo_id' => 1,
                'book_photo_path' => ,
            ],
        ]);
    }
}
