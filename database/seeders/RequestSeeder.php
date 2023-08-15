<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('requests')->insert([
            [
                'type' => 0,
                'email' => 'test1@test.com',
                'subject' => 'グループトップページでボタンが押せなくて困っています。',
                'detail' => 'これは詳細内容です。これは詳細内容です。これは詳細内容です。これは詳細内容です。これは詳細内容です。これは詳細内容です。これは詳細内容です。これは詳細内容です。これは詳細内容です。これは詳細内容です。これは詳細内容です。これは詳細内容です。これは詳細内容です。これは詳細内容です。これは詳細内容です。これは詳細内容です。',
                'environment' => 0,
                'reference_url' => 'https://basta/group/24',
            ],
            [
                'type' => 1,
                'email' => 'test2@test.com',
                'subject' => '◯◯◯◯の機能を追加希望です!!。',
                'detail' => 'これは詳細内容です2。これは詳細内容です2。これは詳細内容です2。これは詳細内容です2。これは詳細内容です2。これは詳細内容です2。これは詳細内容です2。これは詳細内容です2。これは詳細内容です2。これは詳細内容です2。これは詳細内容です2。これは詳細内容です2。これは詳細内容です2。これは詳細内容です2。',
                'environment' => 3,
                'reference_url' => 'https://basta/2/56',
            ],
            [
                'type' => 2,
                'email' => 'test3@test.com',
                'subject' => '入力欄にXSS脆弱性があります!!。',
                'detail' => 'これは詳細内容です3。これは詳細内容です3。これは詳細内容です3。これは詳細内容です3。これは詳細内容です3。これは詳細内容です3。これは詳細内容です3。これは詳細内容です3。これは詳細内容です3。これは詳細内容です3。これは詳細内容です3。これは詳細内容です3。これは詳細内容です3。これは詳細内容です3。これは詳細内容です3。',
                'environment' => 5,
                'reference_url' => 'https://basta/create_group',
            ],
            [
                'type' => 3,
                'email' => 'test4@test.com',
                'subject' => 'その他のお問い合わせです!!。',
                'detail' => 'これは詳細内容です4。これは詳細内容です4。これは詳細内容です4。これは詳細内容です4。これは詳細内容です4。これは詳細内容です4。これは詳細内容です4。これは詳細内容です4。これは詳細内容です4。これは詳細内容です4。これは詳細内容です4。',
                'environment' => 2,
                'reference_url' => '',
            ],
        ]);
    }
}
