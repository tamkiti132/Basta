<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class InviteMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $email;
    protected $group_data;
    protected $target_user;
    protected $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $group_data, $target_user)
    {
        $this->email = $email;
        $this->group_data = $group_data;
        $this->target_user = $target_user;
        $this->url = URL::temporarySignedRoute('invite.joinGroup', now()->addHours(24), ['group_id' => $group_data->id, 'target_user_id' => $target_user->id, 'expire' => now()->addHours(24)->timestamp]);
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Basta グループ招待',
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
                'email' => $this->email,
                'group_data' => $this->group_data,
                'target_user' => $this->target_user,
                'url' => $this->url,
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
