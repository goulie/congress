<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Banniere extends Model
{
    protected $table = 'bannieres';
    protected $fillable = ['left', 'right', 'top'];
}
