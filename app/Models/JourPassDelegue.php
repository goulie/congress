<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class JourPassDelegue extends Model
{
    protected $table = 'jour_pass_delegues';
    protected $fillable = ['date','congres_id'];
    
    public function congres()
    {
        return $this->belongsTo(Congress::class, 'congres_id');
    }
}
