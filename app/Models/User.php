<?php

namespace App\Models;

use App\Models\Rol;
use App\Models\UserType;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $guarded = ['id'];

    public function setPasswordAttribute($password) {
        $this->attributes['password'] = app('hash')->make($password);
    }

    /**
     * @return BelongsTo
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class);
    }
    
    /**
     * @return BelongsTo
     */
    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class);
    }

    /**
     * @return BelongsToMany
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'user_item')->withPivot('equipped')->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function attacksMade(): HasMany
    {
        return $this->hasMany(Attack::class, 'attacking_user_id');
    }
    
    /**
     * @return HasMany
     */
    public function receivedAttacks(): HasMany
    {
        return $this->hasMany(Attack::class, 'defending_user_id');
    }

    /**
     * @return HasOne
     */
    public function currentAttack(): HasOne
    {
        return $this->hasOne(Attack::class, 'attacking_user_id')->latest();
    }
}
