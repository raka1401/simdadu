<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuKak extends Model
{
    use HasFactory;

    protected $table = 'du_kak';
    protected $fillable = [
        'user_id',
        'sub_bidang_id',
        'tahun_id',
        'sub_kegiatan_id',
        'jenis_kak',
        'pdf',
        'status',
        'keterangan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tahun()
    {
        return $this->belongsTo(Tahun::class);
    }

    public function sub_kegiatan()
    {
        return $this->belongsTo(DmSubKegiatan::class);
    }
}
