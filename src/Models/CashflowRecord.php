<?php

namespace Alariva\LaravelCashflow\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashflowRecord extends Model
{
    protected $fillable = [
        'type',
        'flow',
        'name',
        'currency',
        'details',
        'user_id',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    public function scopeForUser(Builder $query, int|\Illuminate\Contracts\Auth\Authenticatable $user): Builder
    {
        $userId = is_int($user) ? $user : $user->getAuthIdentifier();

        return $query->where('user_id', $userId);
    }
}
