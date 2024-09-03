<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dm_sub_bidang extends Model
{
    use HasFactory;

    protected $table = 'dm_sub_bidang';
    protected $fillable = [
        'dm_bidang_id',
        'nama',
        'keterangan',
    ];

    public function dm_bidang()
    {
        return $this->belongsTo(dm_bidang::class, 'dm_bidang_id');
    }

    public function du_renaksi()
    {
        return $this->hasMany(DuRenaksi::class, 'sub_bidang_id');
    }

    public function user()
    {
        return $this->hasMany(User::class, 'sub_bidang_id');
    }

    public function dm_jenis_dokumen()
    {
        return $this->hasMany(DmJenisDokumen::class, 'sub_bidang_id');
    }
}
