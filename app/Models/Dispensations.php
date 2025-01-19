<?php

namespace App\Models;

use App\Casts\HumanReadableTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dispensations extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'barangay_id',
        'provider_name',
        'position',
        'recipients_name',
        'address',
        'reason',
        'birthday',
        'age',
        'dispense_date',
    ];

    protected $casts =[
        'updated_at' => HumanReadableTime::class,
        'created_at' => HumanReadableTime::class,
    ];


    public function medicines()
    {
        return $this->belongsToMany(Medicines::class, 'delivery_medicines');
    }

    public function equipment()
    {
        return $this->belongsToMany(Equipments::class, 'delivery_equipments');
    }

    public function medicalSupplies()
    {
        return $this->belongsToMany(Medical_supplies::class, 'delivery_med_supplies');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangays::class);
    }


    public function dispenseItems()
    {
        return $this->hasMany(Dispense_inventory::class, 'dispense_id');
    }


}
