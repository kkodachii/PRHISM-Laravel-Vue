<?php

namespace App\Models;

use App\Casts\HumanReadableTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Medical_categories;
use App\Models\User;
use App\Models\Batches;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicines extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'batch_id',
        'barangay_id',
        'category_id',
        'generic_name_id',
        'brand',
        'quantity',
        'reserved',
        'provider',
        'expiration_date',
        'status',
        'date_acquired',
        'created_at',
        'updated_at',
        'archived',
        'deleted_at',
    ];

    

    protected $casts = [
        'date_acquired' => HumanReadableTime::class,
        'archived' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();


        static::retrieved(function ($medicine) {
            $medicine->computeStatus();
        });


        static::saving(function ($medicine) {
            $medicine->computeStatus();
        });
        static::deleting(function ($medicine) {
            $medicine->archived = true;
            $medicine->saveQuietly(); // Save without triggering events again
        });
    }

    public function getRawDateAcquiredAttribute()
    {
        return $this->attributes['date_acquired'];
    }

    public function category()
    {
        return $this->belongsTo(Medical_categories::class);
    }

    public function generic_name()
    {
        return $this->belongsTo(Generic_names::class, 'generic_name_id')->select('id', 'generic_name');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'name');
    }

    public function batch()
    {
        return $this->belongsTo(Batches::class, 'batch_id');
    }

    public function deliveries()
    {
        return $this->belongsToMany(Deliveries::class, 'delivery_medicine');
    }

    public function computeStatus()
    {
        $currentDate = Carbon::now();
        $statuses = [];

        $expiryDate = Carbon::parse($this->expiration_date);
        $threeMonthsFromNow = $currentDate->copy()->addMonths(3);

        // Check for Expired status
        if ($expiryDate < $currentDate) {
            $this->status = "Expired";
            $this->deleted_at = now(); 
            $this->delete();    
            return;
        }

        // Quantity-based status (only applied if not expired)
        if ($this->quantity === 0) {
            $statuses[] = "Out of stock";
        } elseif ($this->quantity <= 20) {
            $statuses[] = "Low on stock";
        } else {
            $statuses[] = "On stock";
        }

        // Check for Expiring status (only applied if not expired)
        if ($expiryDate <= $threeMonthsFromNow) {
            $statuses[] = "Expiring";
        }

        // Set the status based on conditions
        $this->status = implode(', ', $statuses);
    }
}
