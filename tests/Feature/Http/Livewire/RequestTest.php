<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\Request;
use App\Mail\SendRequestMail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class RequestTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    // テスト用のロケールを設定
    app()->setLocale('testing');
    // テスト用のストレージを設定
    Storage::fake('public');
  }

  public function test_sendRequest_type_1()
  {
    // Arrange（準備）
    Mail::fake();

    // Act（実行） & Assert（検証）
    Livewire::test(Request::class)
      ->set('title_1', 'テスト不具合タイトル')
      ->set('detail_1', 'テスト不具合の詳細説明です。')
      ->set('environment_1', '2')
      ->set('additional_information', '追加情報テスト')
      ->set('reference_url_1', 'https://example.com')
      ->call('sendRequest', 'type_1')
      ->assertRedirect(route('request'));

    // メールが送信されたことを確認
    Mail::assertSent(SendRequestMail::class, 1);

    // セッションにフラッシュメッセージが設定されていることを確認
    $this->assertTrue(session()->has('success'));
  }

  public function test_sendRequest_type_2()
  {
    // Arrange（準備）
    Mail::fake();

    // Act（実行） & Assert（検証）
    Livewire::test(Request::class)
      ->set('function_request_type', '新機能追加')
      ->set('title_2', 'テスト機能リクエストタイトル')
      ->set('detail_2', 'テスト機能リクエストの詳細説明です。')
      ->set('environment_2', '3')
      ->set('reference_url_2', 'https://example.com/feature')
      ->call('sendRequest', 'type_2')
      ->assertRedirect(route('request'));

    // メールが送信されたことを確認
    Mail::assertSent(SendRequestMail::class, 1);

    // セッションにフラッシュメッセージが設定されていることを確認
    $this->assertTrue(session()->has('success'));
  }

  public function test_sendRequest_type_3()
  {
    // Arrange（準備）
    Mail::fake();

    // Act（実行） & Assert（検証）
    Livewire::test(Request::class)
      ->set('title_3', 'テストセキュリティ脆弱性タイトル')
      ->set('detail_3', 'テストセキュリティ脆弱性の詳細説明です。')
      ->set('explanation', '説明テスト')
      ->set('steps_to_reproduce', '1. ログインする\n2. プロフィールページにアクセスする\n3. 特定の操作を行う')
      ->set('abuse_method', '悪用方法テスト')
      ->set('workaround', '回避策テスト')
      ->set('environment_3', '0')
      ->set('reference_url_3', 'https://example.com/security')
      ->call('sendRequest', 'type_3')
      ->assertRedirect(route('request'));

    // メールが送信されたことを確認
    Mail::assertSent(SendRequestMail::class, 1);

    // セッションにフラッシュメッセージが設定されていることを確認
    $this->assertTrue(session()->has('success'));
  }

  public function test_sendRequest_type_4()
  {
    // Arrange（準備）
    Mail::fake();

    // Act（実行） & Assert（検証）
    Livewire::test(Request::class)
      ->set('title_4', 'テストお問い合わせタイトル')
      ->set('detail_4', 'テストお問い合わせの詳細説明です。')
      ->set('environment_4', '1')
      ->set('reference_url_4', 'https://example.com/contact')
      ->call('sendRequest', 'type_4')
      ->assertRedirect(route('request'));

    // メールが送信されたことを確認
    Mail::assertSent(SendRequestMail::class, 1);

    // セッションにフラッシュメッセージが設定されていることを確認
    $this->assertTrue(session()->has('success'));
  }

  public function test_validation_失敗_sendRequest_type_1()
  {
    // Arrange（準備）
    Mail::fake();

    // Act（実行） & Assert（検証）
    // 必須項目のバリデーション
    Livewire::test(Request::class)
      ->set('title_1', '')  // 空のタイトル
      ->set('detail_1', 'テスト不具合の詳細説明です。')
      ->set('environment_1', '2')
      ->call('sendRequest', 'type_1')
      ->assertHasErrors(['title_1' => 'required']);

    Livewire::test(Request::class)
      ->set('title_1', 'テスト不具合タイトル')
      ->set('detail_1', '')  // 空の詳細
      ->set('environment_1', '2')
      ->call('sendRequest', 'type_1')
      ->assertHasErrors(['detail_1' => 'required']);

    Livewire::test(Request::class)
      ->set('title_1', 'テスト不具合タイトル')
      ->set('detail_1', 'テスト不具合の詳細説明です。')
      ->set('environment_1', '')  // 空の環境
      ->call('sendRequest', 'type_1')
      ->assertHasErrors(['environment_1' => 'required']);

    // URL形式のバリデーション
    Livewire::test(Request::class)
      ->set('title_1', 'テスト不具合タイトル')
      ->set('detail_1', 'テスト不具合の詳細説明です。')
      ->set('environment_1', '2')
      ->set('reference_url_1', 'invalid-url')  // 不正なURL
      ->call('sendRequest', 'type_1')
      ->assertHasErrors(['reference_url_1' => 'url']);

    // メールが送信されていないことを確認
    Mail::assertNotSent(SendRequestMail::class);
  }

  public function test_validation_失敗_sendRequest_type_2()
  {
    // Arrange（準備）
    Mail::fake();

    // Act（実行） & Assert（検証）
    // 必須項目のバリデーション
    Livewire::test(Request::class)
      ->set('function_request_type', '')  // 空のリクエストタイプ
      ->set('title_2', 'テスト機能リクエストタイトル')
      ->set('detail_2', 'テスト機能リクエストの詳細説明です。')
      ->set('environment_2', '3')
      ->call('sendRequest', 'type_2')
      ->assertHasErrors(['function_request_type' => 'required']);

    // メールが送信されていないことを確認
    Mail::assertNotSent(SendRequestMail::class);
  }

  public function test_validation_失敗_sendRequest_type_3()
  {
    // Arrange（準備）
    Mail::fake();

    // Act（実行） & Assert（検証）
    // steps_to_reproduceフィールドは必須
    Livewire::test(Request::class)
      ->set('title_3', 'テストセキュリティ脆弱性タイトル')
      ->set('detail_3', 'テストセキュリティ脆弱性の詳細説明です。')
      ->set('steps_to_reproduce', '')  // 空の再現手順
      ->set('environment_3', '0')
      ->call('sendRequest', 'type_3')
      ->assertHasErrors(['steps_to_reproduce' => 'required']);

    // メールが送信されていないことを確認
    Mail::assertNotSent(SendRequestMail::class);
  }

  public function test_validation_失敗_sendRequest_type_4()
  {
    // Arrange（準備）
    Mail::fake();

    // Act（実行） & Assert（検証）
    // タイトル文字数制限のバリデーション（max:100）
    $longTitle = str_repeat('あ', 101);  // 101文字のタイトル

    Livewire::test(Request::class)
      ->set('title_4', $longTitle)
      ->set('detail_4', 'テストお問い合わせの詳細説明です。')
      ->set('environment_4', '1')
      ->call('sendRequest', 'type_4')
      ->assertHasErrors(['title_4' => 'max']);

    // メールが送信されていないことを確認
    Mail::assertNotSent(SendRequestMail::class);
  }
}
