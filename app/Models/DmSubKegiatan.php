<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DmSubKegiatan extends Model
{
    use HasFactory;

    protected $table = 'dm_sub_kegiatan';
    protected $fillable =
    [
        'kode',
        'nama',
    ];

    public function dmkegiatan()
    {
        return $this->belongsTo(DmKegiatan::class);
    }

    public function duKak()
    {
        return $this->hasMany(DuKak::class);
    }
}
