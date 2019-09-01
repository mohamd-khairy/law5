<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendResetPasswordLink extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $email;
    public $name;
    public $token;
    public $code;
    public $time;
    public function __construct($name, $email, $token, $code ,$time)
    {
        $this->name = $name;
        $this->email = $email;
        $this->token = $token;
        $this->code = $code;
        $this->time = $time;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('user.Login.reset_password')->with("time", $this->time)->with("code", $this->code)->with("token", $this->token)->with("name", $this->name)->with("email", $this->email);
    }
}
