<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Invoice extends Model
{
    protected $table = 'invoices';
    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'status',
        'user_id',
        'total_amount',
        'amount_paid',
        'participant_id',
        'payment_method',
        'congres_id',
        'payment_date',
        'created_at',
        'updated_at',
        'currency'
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    public function congres()
    {
        return $this->belongsTo(Congress::class, 'congres_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
