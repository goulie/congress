<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Invoice extends Model
{
    const PAYMENT_STATUS_PAID = 'Paid';
    const PAYMENT_STATUS_UNPAID = 'Unpaid';
    const PAYMENT_STATUS_EXPIRED = 'Expired';
    const PAYMENT_STATUS_PENDING = 'Pending';

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
        'period_id',
        'deadline',
        'raw_response',
        'token',
        'user_id_pending'

    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', [
            self::PAYMENT_STATUS_PAID,
        ]);
    }

    public static function getPaymentStatuses()
    {
        return [
            self::PAYMENT_STATUS_PAID => 'Paid',
            self::PAYMENT_STATUS_UNPAID => 'Unpaid',
            self::PAYMENT_STATUS_EXPIRED => 'Expired',
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
        if (Auth::user()->role_id == 1 || Auth::user()->role_id == 4) {
            return $query;
        } else {
            return $query->where('user_id', Auth::user()->id)->orWhere('user_id', Auth::user()->user_id);
        }
    }


    public function userValidation()
    {
        return $this->belongsTo(User::class, 'user_id_validation');
    }

    public function userValidationPending()
    {
        return $this->belongsTo(User::class, 'user_id_pending');
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

    public function periode()
    {
        return $this->belongsTo(Periode::class, 'period_id');
    }

    public static function PaidInvoices($congres_id)
    {
        return self::join('participants', 'participants.id', '=', 'invoices.participant_id')->whereNotNull('email')
            ->where([
                'invoices.status' => Invoice::PAYMENT_STATUS_PAID,
                'participants.congres_id' => $congres_id
            ]);
    }

    public static function UnpaidInvoices($congres_id)
    {

        return self::join('participants', 'participants.id', '=', 'invoices.participant_id')->whereNotNull('email')->where([
            'invoices.status' => Invoice::PAYMENT_STATUS_UNPAID,
            'participants.congres_id' => $congres_id
        ]);
    }

    public static function ExpiredInvoices($congres_id)
    {
        return self::join('participants', 'participants.id', '=', 'invoices.participant_id')->whereNotNull('email')->where([
            'invoices.status' => Invoice::PAYMENT_STATUS_EXPIRED,
            'participants.congres_id' => $congres_id
        ]);
    }

    //All Invoices
    public static function AllInvoices($congres_id)
    {

        return self::select(
            'invoices.*',
            'participants.fname',
            'participants.lname',
            'participants.email',
            'participants.organisation',
        )
            ->join('participants', 'participants.id', '=', 'invoices.participant_id')
            ->whereNotNull('participants.email')
            ->where('participants.congres_id', $congres_id);
    }
}
