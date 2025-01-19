<?php

namespace App\Models;

use App\Casts\HumanReadableTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Requests extends Model
{
    use HasFactory;

    protected $fillable = [
        'rhu_id',
        'requester_user_id',
        'barangay_id',
        'approver_user_id',
        'requested_at',
        'status',
    ];

    protected $casts = [
        'requested_at' => HumanReadableTime::class,
    ];

    public function requester_user()
    {
        return $this->belongsTo(User::class, 'requester_user_id')->select('id', 'name', 'rhu_id');
    }

    public function barangay()
    {
        return $this->belongsTo(Barangays::class, 'barangay_id')->select('id', 'barangay_name');
    }

    public function medicineRequests()
    {
        return $this->hasMany(Medicine_request_name::class, 'request_id');
    }

    public function equipmentRequests()
    {
        return $this->hasMany(Equipment_request_name::class, 'request_id');
    }

    public function supplyRequests()
    {
        return $this->hasMany(Supply_request_name::class, 'request_id');
    }

    // New relationship to get the associated delivery by request_id
    public function delivery()
    {
        return $this->hasOne(Deliveries::class, 'request_id');
    }

    // Method to get items associated with the delivery
    public function getDeliveryItems()
    {
        $delivery = $this->delivery;

        if ($delivery) {
            $deliveryEquipments = $delivery->deliveryEquipments->map(function ($equipment) {
                return [
                    'equipment_name' => $equipment->equipment_name,
                    'quantity' => $equipment->quantity,
                ];
            });

            $deliveryMedicines = $delivery->deliveryMedicines->map(function ($medicine) {
                return [
                    'generic_name' => $medicine->generic_name,
                    'quantity' => $medicine->quantity,
                ];
            });

            $deliveryMedSupplies = $delivery->deliveryMedSupplies->map(function ($supply) {
                return [
                    'medical_supply_name' => $supply->medical_supply_name,
                    'quantity' => $supply->quantity,
                ];
            });

            return [
                'delivery_equipments' => $deliveryEquipments,
                'delivery_medicines' => $deliveryMedicines,
                'delivery_med_supplies' => $deliveryMedSupplies,
            ];
        }

        return null;
    }

    // Method to get all requested items
    public function getRequestedItems()
    {
        $medicines = $this->medicineRequests->map(function ($medicineRequest) {
            return [
                'generic_name' => optional($medicineRequest->genericName)->generic_name ?? 'Unknown Generic Name',
                'quantity' => $medicineRequest->quantity ?? 0,
            ];
        });

        $equipment = $this->equipmentRequests->map(function ($equipmentRequest) {
            return [
                'equipment_name' => $equipmentRequest->equipment_name,
                'quantity' => $equipmentRequest->quantity ?? 0,
            ];
        });

        $supplies = $this->supplyRequests->map(function ($supplyRequest) {
            return [
                'medical_supply_name' => $supplyRequest->medical_supply_name,
                'quantity' => $supplyRequest->quantity ?? 0,
            ];
        });

        return [
            'medicines' => $medicines,
            'equipment' => $equipment,
            'supplies' => $supplies,
        ];
    }
}
