<?php

namespace App\Models;

use App\Casts\HumanReadableTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Delivery_med_supplies extends Model
{
    use HasFactory;

    protected $table = 'delivery_med_supplies';
    protected $fillable = [
        'delivery_id',
        'med_sup_id',
        'med_sup_name',
        'quantity',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts =[
        'created_at' => HumanReadableTime::class,
        'updated_at' => HumanReadableTime::class,
    ];

    public function medSupName()
    {
        return $this->belongsTo(Medical_supplies::class, 'med_sup_id');
    }
}
