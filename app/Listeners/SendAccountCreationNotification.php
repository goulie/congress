<?php

namespace App\Listeners;

use App\Services\AccountCreationEmailService;
use Illuminate\Auth\Events\Registered;

class SendAccountCreationNotification
{
    protected $AccountCreationEmailService;

    public function __construct(AccountCreationEmailService $AccountCreationEmailService)
    {
        $this->AccountCreationEmailService = $AccountCreationEmailService;
    }

    public function handle(Registered $event)
    {
        $this->AccountCreationEmailService->sendAccountCreationEmail($event->user);
    }
}
