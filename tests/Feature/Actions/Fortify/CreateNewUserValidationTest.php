<?php

namespace Tests\Feature\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateNewUserValidationTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    // テスト用のロケールを設定
    app()->setLocale('testing');
  }

  public function test_nickname_required(): void
  {
    // Arrange（準備）
    $rules = ['required', 'string', 'max:13'];

    // 正常系
    // Act（実行）
    $validator = Validator::make(
      ['nickname' => 'Test User'],
      ['nickname' => $rules]
    );

    // Assert（検証）
    // バリデーションが成功していることを確認
    $this->assertFalse($validator->fails());



    // 異常系
    // Act（実行）
    $validator = Validator::make(['nickname' => ''], ['nickname' => $rules]);

    // Assert（検証）
    // バリデーションが失敗していることを確認
    $this->assertTrue($validator->fails());
    // エラーメッセージにrequiredが含まれていることを確認
    $errors = $validator->errors()->toArray();
    $this->assertContains('required', $errors['nickname']);
  }

  public function test_nickname_string(): void
  {
    // Arrange（準備）
    $rules = ['required', 'string', 'max:13'];

    // 正常系
    // Act（実行）
    $validator = Validator::make(
      ['nickname' => 'Test User'],
      ['nickname' => $rules]
    );

    // Assert（検証）
    // バリデーションが成功していることを確認
    $this->assertFalse($validator->fails());



    // 異常系
    // Act（実行）
    $validator = Validator::make(['nickname' => [1, 2, 3]], ['nickname' => $rules]);

    // Assert（検証）
    // バリデーションが失敗していることを確認
    $this->assertTrue($validator->fails());
    // エラーメッセージにstringが含まれていることを確認
    $errors = $validator->errors()->toArray();
    $this->assertContains('string', $errors['nickname']);
  }

  public function test_nickname_max(): void
  {
    // Arrange（準備）
    $rules = ['required', 'string', 'max:13'];

    // 正常系
    // Act（実行）
    $validator = Validator::make(
      ['nickname' => str_repeat('a', 13)],
      ['nickname' => $rules]
    );

    // Assert（検証）
    // バリデーションが成功していることを確認
    $this->assertFalse($validator->fails());



    // 異常系
    // Act（実行）
    $validator = Validator::make(
      ['nickname' => str_repeat('a', 14)],
      ['nickname' => $rules]
    );

    // Assert（検証）
    // バリデーションが失敗していることを確認
    $this->assertTrue($validator->fails());
    // エラーメッセージにmaxが含まれていることを確認
    $errors = $validator->errors()->toArray();
    $this->assertContains('max', $errors['nickname']);
  }

  public function test_email_required(): void
  {
    // Arrange（準備）
    $rules = ['required', 'string', 'email', 'max:255', 'unique:users'];

    // 正常系
    // Act（実行）
    $validator = Validator::make(
      ['email' => 'test@example.com'],
      ['email' => $rules]
    );

    // Assert（検証）
    // バリデーションが成功していることを確認
    $this->assertFalse($validator->fails());



    // 異常系
    // Act（実行）
    $validator = Validator::make(['email' => ''], ['email' => $rules]);

    // Assert（検証）
    // バリデーションが失敗していることを確認
    $this->assertTrue($validator->fails());
    // エラーメッセージにrequiredが含まれていることを確認
    $errors = $validator->errors()->toArray();
    $this->assertContains('required', $errors['email']);
  }

  public function test_email_string(): void
  {
    // Arrange（準備）
    $rules = ['required', 'string', 'email', 'max:255', 'unique:users'];

    // 正常系
    // Act（実行）
    $validator = Validator::make(
      ['email' => 'test@example.com'],
      ['email' => $rules]
    );

    // Assert（検証）
    // バリデーションが成功していることを確認
    $this->assertFalse($validator->fails());



    // 異常系
    // Act（実行）
    $validator = Validator::make(['email' => [1, 2, 3]], ['email' => $rules]);

    // Assert（検証）
    // バリデーションが失敗していることを確認
    $this->assertTrue($validator->fails());
    // エラーメッセージにstringが含まれていることを確認
    $errors = $validator->errors()->toArray();
    $this->assertContains('string', $errors['email']);
  }

  public function test_email_email(): void
  {
    // Arrange（準備）
    $rules = ['required', 'string', 'email', 'max:255', 'unique:users'];

    // 正常系
    // Act（実行）
    $validator = Validator::make(
      ['email' => 'test@example.com'],
      ['email' => $rules]
    );

    // Assert（検証）
    // バリデーションが成功していることを確認
    $this->assertFalse($validator->fails());



    // 異常系
    // Act（実行）
    $validator = Validator::make(['email' => 'testexample.com'], ['email' => $rules]);

    // Assert（検証）
    // バリデーションが失敗していることを確認
    $this->assertTrue($validator->fails());
    // エラーメッセージにemailが含まれていることを確認
    $errors = $validator->errors()->toArray();
    $this->assertContains('email', $errors['email']);
  }

  public function test_email_max(): void
  {
    // Arrange（準備）
    $rules = ['required', 'string', 'email', 'max:255', 'unique:users'];

    // 正常系
    // Act（実行）
    $validator = Validator::make(
      ['email' => 'test@example.com'],
      ['email' => $rules]
    );

    // Assert（検証）
    // バリデーションが成功していることを確認
    $this->assertFalse($validator->fails());



    // 異常系
    // Act（実行）
    $validator = Validator::make(
      ['email' => str_repeat('a', 256) . '@example.com'],
      ['email' => $rules]
    );

    // Assert（検証）
    // バリデーションが失敗していることを確認
    $this->assertTrue($validator->fails());
    // エラーメッセージにmaxが含まれていることを確認
    $errors = $validator->errors()->toArray();
    $this->assertContains('max', $errors['email']);
  }

  public function test_email_unique(): void
  {
    // Arrange（準備）
    $rules = ['required', 'string', 'email', 'max:255', 'unique:users'];

    // テスト用のユーザーを作成
    $testUser = User::factory()->create([
      'nickname' => 'TestUser',
      'email' => 'test_unique@example.com',
      'password' => Hash::make('password123456789'),
      'username' => '@' . (string) Str::ulid(),
    ]);

    // 正常系
    // Act（実行）
    $validator = Validator::make(
      ['email' => 'different@example.com'],
      ['email' => $rules]
    );

    // Assert（検証）
    // バリデーションが成功していることを確認
    $this->assertFalse($validator->fails());



    // 異常系
    // Act（実行）
    $validator = Validator::make(
      ['email' => 'test_unique@example.com'],
      ['email' => $rules]
    );

    // Assert（検証）
    // バリデーションが失敗していることを確認
    $this->assertTrue($validator->fails());
    // エラーメッセージにuniqueが含まれていることを確認
    $errors = $validator->errors()->toArray();
    $this->assertContains('unique', $errors['email']);
  }
}
