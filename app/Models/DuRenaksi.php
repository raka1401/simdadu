<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuRenaksi extends Model
{
    use HasFactory;

    protected $table = 'du_renaksi';
    protected $fillable = 
    [
        'user_id',
        'sub_bidang_id',
        'pdf',
        'excel',
        'tahun_id',
        'judul'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subBidang()
    {
        return $this->belongsTo(dm_sub_bidang::class);
    }

    public function tahun()
    {
        return $this->belongsTo(Tahun::class);
    }

    public function verfRenaksi()
    {
        return $this->hasMany(verf_renaksi::class);
    }
}
