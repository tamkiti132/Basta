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

    public function test_バリデーション_成功_プロフィール情報更新(): void
    {
        // Arrange（準備）
        $this->actingAs($user = User::factory()->create());

        // Act（実行）  &  Assert（検証）
        // nicknameのバリデーション
        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', [
                'nickname' => str_repeat('a', 13),
                'email' => 'test@example.com'
            ])
            ->call('updateProfileInformation')
            ->assertHasNoErrors(['nickname' => 'required'])
            ->assertHasNoErrors(['nickname' => 'string'])
            ->assertHasNoErrors(['nickname' => 'max']);

        // emailのバリデーション
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', [
                'nickname' => 'ValidName',
                'email' => 'test@example.com'
            ])
            ->call('updateProfileInformation')
            ->assertHasNoErrors(['email' => 'required'])
            ->assertHasNoErrors(['email' => 'string'])
            ->assertHasNoErrors(['email' => 'email'])
            ->assertHasNoErrors(['email' => 'unique']);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', [
                'nickname' => 'ValidName',
                'email' => str_repeat('a', 255)
            ])
            ->call('updateProfileInformation')
            ->assertHasNoErrors(['email' => 'max']);

        // photoのバリデーション
        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', [
                'nickname' => 'ValidName',
                'email' => 'test@example.com'
            ])
            ->set('photo', null)
            ->call('updateProfileInformation')
            ->assertHasNoErrors(['photo' => 'nullable']);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', [
                'nickname' => 'ValidName',
                'email' => 'test@example.com'
            ])
            ->set('photo', UploadedFile::fake()->image('photo.jpg')->size(2048))
            ->call('updateProfileInformation')
            ->assertHasNoErrors(['photo' => 'max']);
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
            ->set('state', ['nickname' => ['a', 'b', 'c'], 'email' => 'test@example.com'])
            ->call('updateProfileInformation')
            ->assertHasErrors(['nickname' => 'string']);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['nickname' => str_repeat('a', 14), 'email' => 'test@example.com'])
            ->call('updateProfileInformation')
            ->assertHasErrors(['nickname' => 'max']);

        // emailのバリデーション
        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['nickname' => 'ValidName', 'email' => ''])
            ->call('updateProfileInformation')
            ->assertHasErrors(['email' => 'required']);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['nickname' => 'ValidName', 'email' => ['a', 'b', 'c']])
            ->call('updateProfileInformation')
            ->assertHasErrors(['email' => 'string']);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['nickname' => 'ValidName', 'email' => 'invalid-email'])
            ->call('updateProfileInformation')
            ->assertHasErrors(['email' => 'email']);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['nickname' => 'ValidName', 'email' => str_repeat('a', 256)])
            ->call('updateProfileInformation')
            ->assertHasErrors(['email' => 'max']);

        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', [
                'nickname' => 'ValidName',
                'email' => $existingUser->email
            ])
            ->call('updateProfileInformation')
            ->assertHasErrors(['email' => 'unique']);

        // photoのバリデーション
        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['nickname' => 'ValidName', 'email' => 'test@example.com'])
            ->set('photo', UploadedFile::fake()->image('photo.gif'))
            ->call('updateProfileInformation')
            ->assertHasErrors(['photo' => 'mimes']);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['nickname' => 'ValidName', 'email' => 'test@example.com'])
            ->set('photo', UploadedFile::fake()->image('photo.jpg')->size(2049))
            ->call('updateProfileInformation')
            ->assertHasErrors(['photo' => 'max']);
    }
}
