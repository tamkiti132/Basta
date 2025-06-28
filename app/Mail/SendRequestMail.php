<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class SendRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $request_type;
    protected $report_data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($request_type, $report_data)
    {
        $this->request_type = $request_type;
        $this->report_data = $report_data;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $subject = null;

        if ($this->request_type === 'type_1') {
            $subject = 'サービスの不具合の報告';
        } elseif ($this->request_type === 'type_2') {
            $subject = 'サービス機能の追加・改善リクエスト';
        } elseif ($this->request_type === 'type_3') {
            $subject = 'セキュリティ脆弱性の報告';
        } elseif ($this->request_type === 'type_4') {
            $subject = 'その他お問い合わせ';
        }

        return new Envelope(
            subject: $subject,
            from: Auth::user()->email,
            // PHPStan のエラー回避のため、配列で指定しました
            to: ['basta.h.a.132@gmail.com'],
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        $content = null;

        if ($this->request_type === 'type_1') {
            $content = new Content(
                html: 'emails.request-mail-type1',
                text: 'emails.request-mail-text-type1',
                with: [
                    'report_data' => $this->report_data,
                ],
            );
        } elseif ($this->request_type === 'type_2') {
            $content = new Content(
                html: 'emails.request-mail-type2',
                text: 'emails.request-mail-text-type2',
                with: [
                    'report_data' => $this->report_data,
                ],
            );
        } elseif ($this->request_type === 'type_3') {
            $content = new Content(
                html: 'emails.request-mail-type3',
                text: 'emails.request-mail-text-type3',
                with: [
                    'report_data' => $this->report_data,
                ],
            );
        } elseif ($this->request_type === 'type_4') {
            $content = new Content(
                html: 'emails.request-mail-type4',
                text: 'emails.request-mail-text-type4',
                with: [
                    'report_data' => $this->report_data,
                ],
            );
        }

        return $content;
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        if ($this->request_type === 'type_1') {
            if (isset($this->report_data['uploaded_photo_1']) && is_object($this->report_data['uploaded_photo_1'])) {

                $uploaded_photo_1 = $this->report_data['uploaded_photo_1'];

                return [
                    Attachment::fromPath($uploaded_photo_1->getRealPath())
                        ->as($uploaded_photo_1->getClientOriginalName())
                        ->withMime($uploaded_photo_1->getMimeType()),
                ];
            }
        } elseif ($this->request_type === 'type_2') {
            if (isset($this->report_data['uploaded_photo_2']) && is_object($this->report_data['uploaded_photo_2'])) {
                $uploaded_photo_2 = $this->report_data['uploaded_photo_2'];

                return [
                    Attachment::fromPath($uploaded_photo_2->getRealPath())
                        ->as($uploaded_photo_2->getClientOriginalName())
                        ->withMime($uploaded_photo_2->getMimeType()),
                ];
            }
        } elseif ($this->request_type === 'type_3') {
            if (isset($this->report_data['uploaded_photo_3']) && is_object($this->report_data['uploaded_photo_3'])) {
                $uploaded_photo_3 = $this->report_data['uploaded_photo_3'];

                return [
                    Attachment::fromPath($uploaded_photo_3->getRealPath())
                        ->as($uploaded_photo_3->getClientOriginalName())
                        ->withMime($uploaded_photo_3->getMimeType()),
                ];
            }
        } elseif ($this->request_type === 'type_4') {
            if (isset($this->report_data['uploaded_photo_4']) && is_object($this->report_data['uploaded_photo_4'])) {
                $uploaded_photo_4 = $this->report_data['uploaded_photo_4'];

                return [
                    Attachment::fromPath($uploaded_photo_4->getRealPath())
                        ->as($uploaded_photo_4->getClientOriginalName())
                        ->withMime($uploaded_photo_4->getMimeType()),
                ];
            }
        }

        // デフォルトの場合は空の配列を返す
        return [];
    }
}
