<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        // \App\Models\User_group::factory(10)->create();
        // \App\Models\Group::factory(10)->create();
        // \App\Models\Memo::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            UserSeeder::class,
            GroupSeeder::class,
            Group_userSeeder::class,
            RoleSeeder::class,
            Block_stateSeeder::class,
            MemoSeeder::class,
            Web_type_featureSeeder::class,
            // LabelSeeder::class,
            // Label_memoSeeder::class,
            CommentSeeder::class,
            GoodSeeder::class,
            // Later_readSeeder::class,
            RequestSeeder::class,
            // Attached_file_pathSeeder::class,
            DefectSeeder::class,
            Function_addition_improvementSeeder::class,
            VulnerabilitySeeder::class,
            ReportSeeder::class,
            User_type_report_linkSeeder::class,
            Memo_type_report_linkSeeder::class,
            Comment_type_report_linkSeeder::class,
            Group_type_report_linkSeeder::class,
        ]);
    }
}
