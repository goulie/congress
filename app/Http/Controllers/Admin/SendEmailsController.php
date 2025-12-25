<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SendEmailsController extends Controller
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function resendInvoice($id)
    {
        $participant = Participant::find($id);
        $this->emailService->sendInvoiceEmail($participant->invoices()->orderBy('created_at', 'desc')->first());

        return redirect()->back();
    }

    public function resendInvitation($id)
    {
        $participant = Participant::find($id);
        $this->emailService->sendInvitationEmail($participant);

        return redirect()->back();
    }

    public function resendConfirmation($id)
    {
        $participant = Participant::find($id);

        $this->emailService->sendRegistrationConfirmation($participant);

        return redirect()->back();
    }

    
}
