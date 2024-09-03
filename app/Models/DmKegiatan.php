<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DmKegiatan extends Model
{
    use HasFactory;

    protected $table = 'dm_kegiatan';
    protected $fillable =
    [
        'kode',
        'nama',
    ];

    public function DmSubKegiatan()
    {
        return $this->hasMany(DmSubKegiatan::class);
    }
}
