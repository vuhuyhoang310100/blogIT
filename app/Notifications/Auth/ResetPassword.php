<?php

namespace App\Notifications\Auth;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPassword extends ResetPasswordNotification implements shouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $token)
    {
        // Specifying queue is optional but recommended
        $this->queue = 'auth';
    }
}
