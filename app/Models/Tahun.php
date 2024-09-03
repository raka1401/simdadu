<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tahun extends Model
{
    use HasFactory;

    protected $table = 'dm_tahun';
    protected $fillable = [
        'nama',
        'status',
    ];

    public function du_renaksi()
    {
        return $this->hasMany(DuRenaksi::class, 'tahun_id');
    }

    public function du_kak()
    {
        return $this->hasMany(DuKak::class, 'tahun_id');
    }
}
