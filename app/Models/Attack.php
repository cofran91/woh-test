<?php

namespace App\Models;

use App\Models\AttackType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attack extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function attackType(): BelongsTo
    {
        return $this->belongsTo(AttackType::class);
    }

    /**
     * @return BelongsTo
     */
    public function attackingUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attacking_user_id');
    }
    
    /**
     * @return BelongsTo
     */
    public function defendingUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'defending_user_id');
    }
}