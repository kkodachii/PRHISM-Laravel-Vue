<?php

namespace App\Models;

use App\Casts\HumanReadableTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Delivery_medicines extends Model
{
    use HasFactory;

    protected $table = 'delivery_medicines';

    protected $fillable = [
        'delivery_id',
        'medicine_id',
        'generic_name',
        'quantity',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts =[
        'created_at' => HumanReadableTime::class,
        'updated_at' => HumanReadableTime::class,
    ];

    public function genericName()
    {
        return $this->belongsTo(Medicines::class, 'medicine_id');
    }

}
