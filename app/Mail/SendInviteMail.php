<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class SendInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $group_name;
    protected $invite_url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($group_name, $invite_url)
    {
        $this->group_name = $group_name;
        $this->invite_url = $invite_url;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Basta 招待',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            html: 'emails.invite-mail',
            text: 'emails.invite-mail-text',
            with: [
                'group_name' => $this->group_name,
                'invite_url' => $this->invite_url,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
