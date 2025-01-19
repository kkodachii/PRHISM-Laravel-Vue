<?php

namespace App\Models;

use App\Casts\HumanReadableTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supply_request_name extends Model
{
    use HasFactory;
    protected $fillable = [
        'request_id',
        'medical_supply_name',
        'quantity',
        'updated_at',
        'created_at'
    ];

    protected $casts =[
        'created_at' => HumanReadableTime::class,
        'updated_at' => HumanReadableTime::class,
    ];
}
