<?php

namespace App\Listeners;

use App\Events\OneBssUnauth;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OneBssClearAuth
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OneBssUnauth $event): void
    {
        $event->account->access_token = null;
        $event->account->expires_in = null;
        $event->account->save();
    }
}
