<?php

namespace Tests\Feature\Actions\Fortify;

use App\Actions\Fortify\PasswordValidationRules;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PasswordValidationRulesTest extends TestCase
{
    use PasswordValidationRules;

    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
    }

    public function test_password_required(): void
    {
        // Arrange（準備）
        $rules = $this->passwordRules();

        // 正常系
        // Act（実行）
        $validator = Validator::make(
            [
                'password' => 'valid-password-123456',
                'password_confirmation' => 'valid-password-123456',
            ],
            ['password' => $rules]
        );

        // Assert（検証）
        // バリデーションが成功していることを確認
        $this->assertFalse($validator->fails());

        // 異常系
        // Act（実行）
        $validator = Validator::make(['password' => ''], ['password' => $rules]);

        // Assert（検証）
        // バリデーションが失敗していることを確認
        $this->assertTrue($validator->fails());
        // エラーメッセージにrequiredが含まれていることを確認
        $errors = $validator->errors()->toArray();
        $this->assertContains('required', $errors['password']);
    }

    public function test_password_string(): void
    {
        // Arrange（準備）
        $rules = $this->passwordRules();

        // 正常系
        // Act（実行）
        $validator = Validator::make(
            [
                'password' => 'password-string-123456',
                'password_confirmation' => 'password-string-123456',
            ],
            ['password' => $rules]
        );

        // Assert（検証）
        // バリデーションが成功していることを確認
        $this->assertFalse($validator->fails());

        // 異常系
        // Act（実行）
        $validator = Validator::make(['password' => [1, 2, 3]], ['password' => $rules]);

        // Assert（検証）
        // バリデーションが失敗していることを確認
        $this->assertTrue($validator->fails());
        // エラーメッセージにstringが含まれていることを確認
        $errors = $validator->errors()->toArray();
        $this->assertContains('string', $errors['password']);
    }

    public function test_password_min(): void
    {
        // Arrange（準備）
        $rules = $this->passwordRules();

        // 正常系
        // Act（実行）
        $validator = Validator::make(
            [
                'password' => str_repeat('a', 15),
                'password_confirmation' => str_repeat('a', 15),
            ],
            ['password' => $rules]
        );

        // Assert（検証）
        // バリデーションが成功していることを確認
        $this->assertFalse($validator->fails());

        // 異常系
        // Act（実行）
        $validator = Validator::make(
            [
                'password' => str_repeat('a', 14),
                'password_confirmation' => str_repeat('a', 14),
            ],
            ['password' => $rules]
        );

        // Assert（検証）
        // バリデーションが失敗していることを確認
        $this->assertTrue($validator->fails());
        // CustomPasswordルールによるエラーが発生していることを確認
        // 注：CustomPasswordルールは独自のエラーメッセージを使用するため、
        // 'min'というキーワードではなく、エラーが存在することのみを確認
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_password_max(): void
    {
        // Arrange（準備）
        $rules = $this->passwordRules();

        // 正常系
        // Act（実行）
        $validator = Validator::make(
            [
                'password' => str_repeat('a', 64),
                'password_confirmation' => str_repeat('a', 64),
            ],
            ['password' => $rules]
        );

        // Assert（検証）
        // バリデーションが成功していることを確認
        $this->assertFalse($validator->fails());

        // 異常系
        // Act（実行）
        $validator = Validator::make(
            [
                'password' => str_repeat('a', 65),
                'password_confirmation' => str_repeat('a', 65),
            ],
            ['password' => $rules]
        );

        // Assert（検証）
        // バリデーションが失敗していることを確認
        $this->assertTrue($validator->fails());
        // エラーメッセージにmaxが含まれていることを確認
        $errors = $validator->errors()->toArray();
        $this->assertContains('max', $errors['password']);
    }

    public function test_password_confirmed(): void
    {
        // Arrange（準備）
        $rules = $this->passwordRules();

        // 正常系
        // Act（実行）
        $validator = Validator::make(
            [
                'password' => 'valid-password-123456',
                'password_confirmation' => 'valid-password-123456',
            ],
            ['password' => $rules]
        );

        // Assert（検証）
        // バリデーションが成功していることを確認
        $this->assertFalse($validator->fails());

        // 異常系
        // Act（実行）
        $validator = Validator::make(
            [
                'password' => 'valid-password-123456',
                'password_confirmation' => 'different-password-123',
            ],
            ['password' => $rules]
        );

        // Assert（検証）
        // バリデーションが失敗していることを確認
        $this->assertTrue($validator->fails());
        // エラーメッセージにconfirmedが含まれていることを確認
        $errors = $validator->errors()->toArray();
        $this->assertContains('confirmed', $errors['password']);
    }
}
