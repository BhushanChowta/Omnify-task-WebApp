<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'discountCode',
        'discountType',
        'value',
        'applies',
        'eligibility',
        'autoApply',
        'expiryOn',
        'redemptionType',
        'redemptionLimit',
    ];

    protected $casts = [
        'autoApply' => 'boolean',
        'expiryOn' => 'date',
        'redemptionLimit' => 'array', // Cast JSON column to an array
    ];
}
