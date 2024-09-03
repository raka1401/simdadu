<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuEvalin extends Model
{
    use HasFactory;

    protected $table = 'du_evalin';
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
}
