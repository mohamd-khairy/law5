<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class returnToCompany extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $app_name;
    public $emp_name;

    public function __construct($app_name, $emp_name)
    {
        $this->app_name = $app_name;
        $this->emp_name = $emp_name;
    
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('employee.returnToApp')->with("aname", $this->app_name)->with("ename", $this->emp_name);
    }
}
