<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuDakin extends Model
{
    use HasFactory;

    protected $table = 'du_dakin';
    protected $fillable = [
        'user_id',
        'sub_bidang_id',
        'tahun_id',
        'triwulan',
        'pdf',
        'excel',
        'status',
        'keterangan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tahun()
    {
        return $this->belongsTo(Tahun::class, 'tahun_id');
    }
}
