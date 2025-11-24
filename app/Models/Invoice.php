<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Invoice extends Model
{
    const PAYMENT_STATUS_PAID = 'Paid';
    const PAYMENT_STATUS_UNPAID = 'Unpaid';

    const PAYMENT_METHOD_ONLINE = 'Online';
    const PAYMENT_METHOD_TRANSFERT = 'Transfert';
    const PAYMENT_METHOD_CASH = 'Cash';

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
        'currency',
        'user_id_validation',

    ];


    public static function getPaymentStatuses()
    {
        return [
            self::PAYMENT_STATUS_PAID => 'Paid',
            self::PAYMENT_STATUS_UNPAID => 'Unpaid',
        ];
    }


    public static function getPaymentMethod()
    {
        return [
            self::PAYMENT_METHOD_ONLINE => 'Online',
            self::PAYMENT_METHOD_TRANSFERT => 'Transfert',
            self::PAYMENT_METHOD_CASH => 'Cash',
        ];
    }
    public function scopeCurrentUser($query)
    {
        if (Auth::user()->role_id == 1) {
            return $query;
        } else {
            return $query->where('user_id', Auth::user()->id)->orWhere('user_id', Auth::user()->user_id);
        }
    }


    public function userValidation()
    {
        return $this->belongsTo(User::class, 'user_id_validation');
    }
    
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
