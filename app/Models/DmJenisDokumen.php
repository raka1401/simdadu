<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DmJenisDokumen extends Model
{
    use HasFactory;
    protected $table = 'dm_jenis_dokumen';
    protected $fillable = 
    [
        'nama',
        'sub_bidang_id',
        'perangkat_daerah'
    ];

    public function subBidang()
    {
        return $this->belongsTo(dm_sub_bidang::class, 'sub_bidang_id');
    }
}
