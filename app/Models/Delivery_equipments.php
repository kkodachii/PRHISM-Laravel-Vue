<?php

namespace App\Models;

use App\Casts\HumanReadableTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Delivery_equipments extends Model
{
    use HasFactory;

    protected $table = 'delivery_equipments';

    protected $fillable = [
        'delivery_id',
        'equipment_id',
        'equipment_name',
        'quantity',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts =[
        'created_at' => HumanReadableTime::class,
        'updated_at' => HumanReadableTime::class,
    ];

    public function equipmentName()
    {
        return $this->belongsTo(Equipments::class, 'equipment_id');
    }

}
