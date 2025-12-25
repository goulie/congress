<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvitationLetterMail;
use App\Models\Participant;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InvitationLetterController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function sendInvitationLetter($participantId)
    {
        $adminEmails = Voyager::setting('admin.admin_finance')
            ? array_map('trim', explode(',', Voyager::setting('admin.admin_finance')))
            : [];
        try {
        $participant = Participant::where('uuid', $participantId)->first();
        
        Mail::to($participant->email)->bcc($adminEmails)->send(new InvitationLetterMail($participant, $participant->langue ?? 'fr'));

        } catch (\Exception $e) {
            Log::error('Erreur envoi email d\'invitation : ',$e->getMessage());
        }
        /* $lang = app()->getLocale();

        $result = $this->emailService->sendInvitationEmail($participant, $lang);

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => $result['message']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400); */
    }
}
