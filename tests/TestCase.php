<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Console\CliDumper;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * DB のテーブルに入っているデータを出力します
     */
    protected function dumpdb(): void
    {
        if (class_exists(CliDumper::class)) {
            CliDumper::resolveDumpSourceUsing(fn() => null); // ファイル名や行数の出力を消す
        }

        // Laravel Ver.11 以上は、Schema::getTables() として下さい
        // Laravel Ver.11 未満は、Schema::getAllTables() として下さい
        foreach (Schema::getAllTables() as $table) {
            if (isset($table->name)) {
                $name = $table->name;
            } else {
                $table = (array) $table;
                $name = reset($table);
            }

            if (in_array($name, ['migrations'], true)) {
                continue;
            }

            $collection = DB::table($name)->get();

            if ($collection->isEmpty()) {
                continue;
            }

            $data = $collection->map(function ($item) {
                unset($item->created_at, $item->updated_at);

                return $item;
            })->toArray();

            dump(sprintf('■■■■■■■■■■■■■■■■■■■ %s %s件 ■■■■■■■■■■■■■■■■■■■', $name, $collection->count()));
            dump($data);
        }

        $this->assertTrue(true);
    }

    /**
     * 発行したSQLを出力します
     */
    protected function dumpQuery(): void
    {
        $db = $this->app->make('db');

        $db->enableQueryLog();

        $this->beforeApplicationDestroyed(function () use ($db) {
            dump($db->getQueryLog());
        });
    }
}
