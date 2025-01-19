<?php

namespace App\Models;

use App\Casts\HumanReadableTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Medical_categories;
use App\Models\User;

class Midwife_inventories extends Model
{
    use HasFactory;

    protected $table = 'midwife_inventory';

    protected $fillable = [
        'user_id',
        'rhu_id',
        'barangay_id',
        'type',
        'name',
        'quantity',
        'status',
        'description',
        'brand',
        'category_id',
        'expiration_date',
        'request_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => HumanReadableTime::class,
    ];

    public static function boot()
    {
        parent::boot();

        // Automatically compute status when the model is retrieved
        static::retrieved(function ($inventory) {
            $inventory->computeStatus();
        });

        // Automatically compute status before saving
        static::saving(function ($inventory) {
            $inventory->computeStatus();
        });
    }

    // Relations
    public function category()
    {
        return $this->belongsTo(Medical_categories::class );
    }

    public function generic_name()
    {
        return $this->belongsTo(Generic_names::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'name');
    }


    public function rhu()
    {
        return $this->belongsTo(Rhu::class, 'rhu_id')->select('id', 'rhu_name');
    }

    public function barangay()
    {
        return $this->belongsTo(Barangays::class, 'barangay_id')->select('id', 'barangay_name');
    }

    public function request()
    {
        return $this->belongsTo(Requests::class, 'request_id')->select('id');
    }

    public function deliveryMedicines()
    {
        return $this->belongsToMany(Deliveries::class, 'delivery_medicines');
    }

    public function deliveryEquipments()
    {
        return $this->belongsToMany(Deliveries::class, 'delivery_equipments');
    }

    public function deliveryMedSupplies()
    {
        return $this->belongsToMany(Deliveries::class, 'delivery_med_supplies');
    }

    public function getRawDateAcquiredAttribute()
    {
        return $this->attributes['created_at'];
    }

    // Custom Functions for Status Computation
    public function computeStatus()
    {
        if ($this->type === 'equipment') {
            return;
        }
        $currentDate = \Carbon\Carbon::now();
        $statuses = [];

        // Compute status based on the type of inventory item
        switch ($this->type) {
            case 'medicine':
                $expiryDate = \Carbon\Carbon::parse($this->expiration_date);
                $threeMonthsFromNow = $currentDate->copy()->addMonths(3);

                // Check for Expired status
                if ($expiryDate < $currentDate) {
                    $statuses[] = "Expired";
                    break;
                }

                // Fall-through to quantity-based status check for medicines as well
                // (e.g., medicine can be Expiring and Out of Stock)
                break;

            case 'medical_supply':
                // Equipment and Medical Supplies don't consider expiration date
                break;

            default:
                // Handle unknown types gracefully
                $statuses[] = "Unknown type";
                break;
        }

        // Quantity-based status (applies to all types)
        if ($this->quantity === 0) {
            $statuses[] = "Out of stock";
        } elseif ($this->quantity <= 20) {
            $statuses[] = "Low on stock";
        } else {
            $statuses[] = "On stock";
        }

        // Additional condition for expiring items for medicine
        if ($this->type === 'medicine') {
            if ($expiryDate >= $currentDate && $expiryDate <= $threeMonthsFromNow) {
                $statuses[] = "Expiring";
            }
        }

        // Set the status based on conditions, ensuring "Expiring" is last
        $this->status = implode(', ', array_filter($statuses));
    }




    // New function to compute status based only on quantity


    public function save(array $options = [])
    {
        $this->computeStatus();
        parent::save($options);
    }
}
