<?php

namespace Alariva\LaravelCashflow\Models;

use Illuminate\Database\Eloquent\Model;

class CashflowRecord extends Model
{
    protected $table = 'cashflow_records';

    protected $fillable = [
        'type',
        'flow',
        'name',
        'currency',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];
}
