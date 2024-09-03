<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuPendukungDakin extends Model
{
    use HasFactory;

    protected $table = 'du_pendukung_dakin';
    protected $fillable = [
        'user_id',
        'sub_bidang_id',
        'tahun_id',
        'jenis_dokumen_id',
        'perangkat_daerah_id',
        'pdf',
        'status',
        'keterangan',
    ];

    public function perangkat_daerah()
    {
        return $this->belongsTo(DmPerangkatDaerah::class, 'perangkat_daerah_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jenis_dokumen()
    {
        return $this->belongsTo(DmJenisDokumen::class, 'jenis_dokumen_id');
    }

    public function tahun()
    {
        return $this->belongsTo(Tahun::class, 'tahun_id');
    }
}
