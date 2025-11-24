<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class BadgeColor extends Model
{
    protected $table = 'badge_colors';
    protected $fillable = ['color', 'libelle'];

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }
}
