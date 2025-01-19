<?php

namespace App\Models;

use App\Casts\HumanReadableTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deliveries extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'user_id',
        'rhu_id',
        'for_pickup',
        'pickup_date',
        'destination_id',
        'delivery_name',
        'delivery_number',
        'delivery_address',
        'date_delivery',
        'date_delivered',
        'updated_at',
        'created_at',
    ];

    protected $casts =[
        'date_delivery' => HumanReadableTime::class,
        'date_delivered' => HumanReadableTime::class,
        'updated_at' => HumanReadableTime::class,
        'created_at' => HumanReadableTime::class,
    ];


    public function request()
    {
        return $this->belongsTo(Requests::class, 'request_id');
    }

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

public function barangay()
{
    return $this->belongsTo(Barangays::class, 'destination_id')->select('id','barangay_name');
}

    public function medicineDeliveries()
    {
        return $this->hasMany(Delivery_medicines::class, 'delivery_id');
    }
    public function equipmentDeliveries()
    {
        return $this->hasMany(Delivery_equipments::class, 'delivery_id');
    }
    public function supplyDeliveries()
    {
        return $this->hasMany(Delivery_med_supplies::class, 'delivery_id');
    }








    // Method to get all requested items
public function getDeliveryItems()
    {
        // Fetch the medicine generic names
        $medicines = $this->medicineDeliveries->map(function ($medicineDeliveries) {
            return optional($medicineDeliveries->genericName)->name ?? 'Unknown Generic Name';
        });

        // Fetch equipment names and medical supply names
        $equipment = $this->equipmentDeliveries->map(function ($equipmentDeliveries) {
            return optional($equipmentDeliveries->equipmentName)->name ?? 'Unknown Equipment Name';
        });

        $supplies = $this->supplyDeliveries->map(function ($supplyDeliveries) {
            return optional($supplyDeliveries->medSupName)->name ?? 'Unknown Med Supply Name';
        });

        // Combine all into a single array
        return array_merge($medicines->toArray(), $equipment, $supplies);
    }


}
