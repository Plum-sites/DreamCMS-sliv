<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailConfirm extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    private $user;
    private $email;
    private $token;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param $email
     * @param $token
     */
    public function __construct(User $user, $email, $token)
    {
        $this->user = $user;
        $this->email = $email;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Подтверждение почтового адреса')->markdown('email.confirm')->with([
            'user' => $this->user,
            'email' => $this->email,
            'token' => $this->token
        ]);
    }
}
