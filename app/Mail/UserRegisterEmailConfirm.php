<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegisterEmailConfirm extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            //->from(env('MAIL_FROM_ADDRESS'))
            ->view('user.register.confirm_email')
            ->text('user.register.confirm_email_plain')
            ->with([
                'user.name' => $this->user->name,
                'user.email' => $this->user->email,
            ])
            ;
    }
}
