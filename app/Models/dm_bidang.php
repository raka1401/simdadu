<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dm_bidang extends Model
{
    use HasFactory;

    protected $table = 'dm_bidang';
    protected $fillable = [
        'nama',
        'keterangan',
    ];

    public function dm_sub_bidang()
    {
        return $this->hasMany(dm_sub_bidang::class, 'dm_bidang_id');
    }
}
