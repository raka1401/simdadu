<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class verf_renaksi extends Model
{
    use HasFactory;

    protected $table = 'verf_renaksi';
    protected $fillable = ['status','keterangan'];

    public function duRenaksi()
    {
        return $this->belongsTo(DuRenaksi::class);
    }
}
