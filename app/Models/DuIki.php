<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuIki extends Model
{
    use HasFactory;

    protected $table = 'du_iki';
    protected $fillable = [
        'user_id',
        'sub_bidang_id',
        'tahun_id',
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
}
