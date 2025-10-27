<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class InvoiceItem extends Model
{
    protected $table = 'invoice_items';
    protected $fillable = [
        'invoice_id', 'description_fr', 'price', 'description_en', 'currency'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
