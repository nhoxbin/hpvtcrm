<?php

namespace App\Jobs;

use App\Models\OneBssAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OneBssClearAuth implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private OneBssAccount $account)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->account->expires_in = null;
        $this->account->access_token = null;
        $this->account->save();
    }
}
