<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

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
        if ($this->request_type === 'type_1') {
            return new Envelope(
                subject: 'サービスの不具合の報告',
            );
        } elseif ($this->request_type === 'type_2') {
            return new Envelope(
                subject: 'サービス機能の追加・改善リクエスト',
            );
        } elseif ($this->request_type === 'type_3') {
            return new Envelope(
                subject: 'セキュリティ脆弱性の報告',
            );
        } elseif ($this->request_type === 'type_4') {
            return new Envelope(
                subject: 'その他お問い合わせ',
            );
        }
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        if ($this->request_type === 'type_1') {
            return new Content(
                html: 'emails.request-mail-type1',
                text: 'emails.request-mail-text-type1',
                with: [
                    'report_data' => $this->report_data,
                ],
            );
        } elseif ($this->request_type === 'type_2') {
            return new Content(
                html: 'emails.request-mail-type2',
                text: 'emails.request-mail-text-type2',
                with: [
                    'report_data' => $this->report_data,
                ],
            );
        } elseif ($this->request_type === 'type_3') {
            return new Content(
                html: 'emails.request-mail-type3',
                text: 'emails.request-mail-text-type3',
                with: [
                    'report_data' => $this->report_data,
                ],
            );
        } elseif ($this->request_type === 'type_4') {
            return new Content(
                html: 'emails.request-mail-type4',
                text: 'emails.request-mail-text-type4',
                with: [
                    'report_data' => $this->report_data,
                ],
            );
        }
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        $attachments = [];

        if ($this->request_type === 'type_1') {
            if (isset($this->report_data['uploaded_photo_1']) && is_object($this->report_data['uploaded_photo_1'])) {
                $attachments[] = $this->report_data['uploaded_photo_1']->getRealPath();
            }
        } elseif ($this->request_type === 'type_2') {
            if (isset($this->report_data['uploaded_photo_2']) && is_object($this->report_data['uploaded_photo_2'])) {
                $attachments[] = $this->report_data['uploaded_photo_2']->getRealPath();
            }
        } elseif ($this->request_type === 'type_3') {
            if (isset($this->report_data['uploaded_photo_3']) && is_object($this->report_data['uploaded_photo_3'])) {
                $attachments[] = $this->report_data['uploaded_photo_3']->getRealPath();
            }
        } elseif ($this->request_type === 'type_4') {
            if (isset($this->report_data['uploaded_photo_4']) && is_object($this->report_data['uploaded_photo_4'])) {
                $attachments[] = $this->report_data['uploaded_photo_4']->getRealPath();
            }
        }

        return $attachments;
    }
}
