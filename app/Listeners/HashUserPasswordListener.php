<?php

namespace App\Listeners;

use App\Events\SaveUserEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class HashUserPasswordListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SaveUserEvent  $event
     * @return void
     */
    public function handle(SaveUserEvent $event)
    {
        //
    }
}
