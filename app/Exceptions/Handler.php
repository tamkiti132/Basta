<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // 署名付きURLの署名が無効な場合の処理
        $this->renderable(function (InvalidSignatureException $e) {
            $expire = request()->query('expire');
            $currentTimestamp = now()->timestamp;

            // 招待メールのURLの有効期限が切れた場合の処理
            if ($expire && $currentTimestamp > $expire) {
                return to_route('index')->with('error', 'URLの有効期限が切れています。');
            }

            // 招待メールのURLが変更された場合の処理
            return to_route('index')->with('error', 'URLが変更されたため、アクセスに失敗しました。');
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof TokenMismatchException) {
            return redirect('login')->withErrors('セッションが切れました。もう一度ログインしてください。');
        }

        // アクセス権限がない場合の処理
        if ($exception instanceof AuthorizationException) {
            session()->flash('role-access-error', 'アクセス権限がありません。');
            return redirect()->back();
        }

        return parent::render($request, $exception);
    }
}
