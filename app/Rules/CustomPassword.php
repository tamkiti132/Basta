<?php

namespace App\Rules;

use Laravel\Fortify\Rules\Password as FortifyPassword;

class CustomPassword extends FortifyPassword
{
  /**
   * Get the validation error message.
   * 
   * これは、パスワード新規登録時のLaravelデフォルトのバリデーションルールの
   * messageメソッドをオーバーライドするための記述です。
   * 
   * 【理由】
   * パスワードを8文字未満で入力した際のエラーメッセージが、
   * 「The パスワード must be at least 8 characters.」
   * と正しく日本語で表示されないため。
   * 
   *
   * @return string
   */
  public function message()
  {
    if ($this->message) {
      return $this->message;
    }

    switch (true) {
      case $this->requireUppercase
        && !$this->requireNumeric
        && !$this->requireSpecialCharacter:
        return __(':attributeは、:length 文字以上で、少なくとも1つの大文字を含む必要があります。', [
          'length' => $this->length,
        ]);

      case $this->requireNumeric
        && !$this->requireUppercase
        && !$this->requireSpecialCharacter:
        return __(':attributeは、:length 文字以上で、少なくとも1つの数字を含む必要があります。', [
          'length' => $this->length,
        ]);

      case $this->requireSpecialCharacter
        && !$this->requireUppercase
        && !$this->requireNumeric:
        return __(':attributeは、:length 文字以上で、少なくとも1つの特殊文字を含む必要があります。', [
          'length' => $this->length,
        ]);

      case $this->requireUppercase
        && $this->requireNumeric
        && !$this->requireSpecialCharacter:
        return __(':attributeは、:length 文字以上で、少なくとも1つの大文字と1つの数字を含む必要があります。', [
          'length' => $this->length,
        ]);

      case $this->requireUppercase
        && $this->requireSpecialCharacter
        && !$this->requireNumeric:
        return __(':attributeは、:length 文字以上で、少なくとも1つの大文字と1つの特殊文字を含む必要があります。', [
          'length' => $this->length,
        ]);

      case $this->requireUppercase
        && $this->requireNumeric
        && $this->requireSpecialCharacter:
        return __(':attributeは、:length 文字以上で、少なくとも1つの大文字、1つの数字、および1つの特殊文字を含む必要があります。', [
          'length' => $this->length,
        ]);

      case $this->requireNumeric
        && $this->requireSpecialCharacter
        && !$this->requireUppercase:
        return __(':attributeは、:length 文字以上で、少なくとも1つの特殊文字と1つの数字を含む必要があります。', [
          'length' => $this->length,
        ]);

      default:
        return __(':attributeは、:length 文字以上で指定してください。', [
          'length' => $this->length,
        ]);
    }
  }
}
