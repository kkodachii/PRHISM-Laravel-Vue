<?php

namespace App\Models;

use App\Casts\HumanReadableTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispense_inventory extends Model
{
    use HasFactory;

    protected $table = 'dispense_inventory';

    protected $fillable = [
        'dispense_id',
        'midwife_inventory_id',
        'type',
        'name',
        'quantity',
            ];

    protected $casts = [
        'created_at' => HumanReadableTime::class,
        'updated_at' => HumanReadableTime::class,
    ];

    public function midwifeInventory()
    {
        return $this->belongsTo(Midwife_Inventories::class, 'midwife_inventory_id');
    }

    public function dispensations()
    {
        return $this->belongsTo(Dispensations::class, 'dispense_id');
    }
}
