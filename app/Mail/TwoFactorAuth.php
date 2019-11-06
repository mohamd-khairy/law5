<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TwoFactorAuth extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $name;
    public $code;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->name = $user->name;
        $this->email = $user->email;
        $this->code = $user->step2code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('user.login.twoFactorAuth')->with([
            'name'  => $this->name,
            'email' => $this->email,
            'code'  =>$this->code
        ]);
    }
}
