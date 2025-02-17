<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuRisiko extends Model
{
    use HasFactory;

    protected $table = 'du_risiko';
    protected $fillable = [
        'user_id',
        'sub_bidang_id',
        'tahun_id',
        'kategori',
        'pdf',
        'excel',
        'status',
        'keterangan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
