<?php

namespace Tests\Feature\vendor\laravel\jetstream\src\Http\Livewire;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm;
use Livewire\Livewire;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateProfileInformationFormTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_現在のプロフィール情報が取得できる(): void
    {
        // Arrange（準備）
        $this->actingAs($user = User::factory()->create());

        $component = Livewire::test(UpdateProfileInformationForm::class);

        // Assert（検証）
        $this->assertEquals($user->nickname, $component->state['nickname']);
        $this->assertEquals($user->email, $component->state['email']);
    }

    public function test_プロフィール情報を更新できる(): void
    {
        // プロフィール画像の更新処理は、Laravelでデフォルトの機能のため、
        // それに関するテストコードは省略する

        // Arrange（準備）        
        $this->actingAs($user = User::factory()->create());

        // Act（実行）
        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['nickname' => 'Test Name', 'email' => 'test@example.com'])
            ->call('updateProfileInformation');

        // Assert（検証）
        $this->assertEquals('Test Name', $user->fresh()->nickname);
        $this->assertEquals('test@example.com', $user->fresh()->email);
    }

    public function test_バリデーション_失敗_プロフィール情報更新(): void
    {
        // バリデーションルールは、
        // app/Actions/Fortify/UpdateUserProfileInformation.php
        // に記述されている

        // Arrange（準備）
        $this->actingAs($user = User::factory()->create());

        // Act（実行）  &  Assert（検証）
        // nicknameのバリデーション
        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['nickname' => '', 'email' => 'test@example.com'])
            ->call('updateProfileInformation')
            ->assertHasErrors(['nickname' => 'required']);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['nickname' => str_repeat('a', 14), 'email' => 'test@example.com'])
            ->call('updateProfileInformation')
            ->assertHasErrors(['nickname' => 'max']);

        // emailのバリデーション
        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['nickname' => 'ValidName', 'email' => 'invalid-email'])
            ->call('updateProfileInformation')
            ->assertHasErrors(['email' => 'email']);

        // photoのバリデーション
        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['nickname' => 'ValidName', 'email' => 'test@example.com'])
            ->set('photo', UploadedFile::fake()->image('photo.gif'))
            ->call('updateProfileInformation')
            ->assertHasErrors(['photo' => 'mimes']);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['nickname' => 'ValidName', 'email' => 'test@example.com'])
            ->set('photo', UploadedFile::fake()->image('photo.jpg')->size(3000))
            ->call('updateProfileInformation')
            ->assertHasErrors(['photo' => 'max']);
    }
}
