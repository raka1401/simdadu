<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DmPerangkatDaerah extends Model
{
    use HasFactory;

    protected $table = 'dm_perangkat_daerah';
    protected $fillable = 
    [
        'nama'
    ];
}
