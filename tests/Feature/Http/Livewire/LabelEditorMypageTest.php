<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\LabelEditorMypage;
use App\Models\Group;
use App\Models\Label;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class LabelEditorMypageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }
}
