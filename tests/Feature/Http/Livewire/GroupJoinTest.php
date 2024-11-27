<?php

namespace Tests\Feature\Http\Livewire;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use App\Models\Group;
use App\Http\Livewire\GroupJoin;

class GroupJoinTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_Livewireコンポーネントが存在している()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/group_join')
            ->assertSeeLivewire(GroupJoin::class);
    }
}
