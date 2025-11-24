<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CongressBankTransfer extends Model
{
    protected $table = 'congress_bank_transfers';
    protected $fillable = [
        'congres_id',
        'beneficiary_name',
        'banque',
        'bank_address',
        'account_number',
        'iban',
        'swift'
    ];

    public function congres()
    {
        return $this->belongsTo(Congress::class, 'congres_id');
    }
}
