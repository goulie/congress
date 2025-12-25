<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class InvoicesReminder extends Model
{
    protected $table = 'invoices_reminders';
    protected $fillable = [
        'invoice_id',
        'reminder_sent_at',
        'reminder_type',
    ];
}
