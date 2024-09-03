<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'sub_bidang_id',
        'nip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function DuRenaksi() 
    {
        return $this->hasMany(DuRenaksi::class);
    }

    public function subBidang()
    {   
        return $this->belongsTo(dm_sub_bidang::class);
    }

    public function duKak()
    {
        return $this->hasMany(DuKak::class);
    }

    // public function dm_sub_bidang(): BelongsToMany
    // {
    //     return $this->belongsToMany(dm_sub_bidang::class);
    // }
 
    // public function getTenants(Panel $panel): 
    // {
    //     return $this->DuRenaksi();
    // }
 
    // public function canAccessTenant(Model $tenant): bool
    // {
    //     return $this->teams()->whereKey($tenant)->exists();
    // }
}
