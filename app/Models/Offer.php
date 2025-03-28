<?php

namespace App\Models;


use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $casts = [
        'price' => MoneyCast::class,
    ];
}
