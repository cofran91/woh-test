<?php

namespace App\Models;

use App\Models\Attack;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttackType extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = ['id'];

    /**
     * @return HasMany
     */
    public function attacks(): HasMany
    {
        return $this->hasMany(Attack::class);
    }
}
