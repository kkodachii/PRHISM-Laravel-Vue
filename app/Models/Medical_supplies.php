<?php

namespace App\Models;

use App\Casts\HumanReadableTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medical_supplies extends Model
{

    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'batch_id',
        'barangay_id',
        'med_sup_name',
        'description',
        'quantity',
        'reserved',
        'provider',
        'status',
        'date_acquired',
        'created_at',
        'updated_at',
        'archived',
        'deleted_at',
    ];

    // protected $casts =[
    //     'date_acquired' => HumanReadableTime::class,
    // ];


    public function getRawDateAcquiredAttribute()
    {
        return $this->attributes['date_acquired'];
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
        return $this->belongsToMany(Deliveries::class, 'delivery_med_supplies');
    }

    public function computeStatus()
    {
        $statuses = [];


        // Quantity-based status (only applied if not expired)
        if ($this->quantity === 0) {
            $statuses[] = "Out of stock";
        } elseif ($this->quantity <= 20) {
            $statuses[] = "Low on stock";
        } else {
            $statuses[] = "On stock";
        }

        $this->status = implode(', ', $statuses);
    }
}
