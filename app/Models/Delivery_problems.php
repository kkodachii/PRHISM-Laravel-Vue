<?php

namespace App\Models;

use App\Casts\HumanReadableTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Delivery_problems extends Model
{
    use HasFactory;

    protected $table = 'delivery_problems';

    protected $fillable = [
        'delivery_id',
        'rhu_id',
        'barangay_id',
        'broken_equipment',
        'incorrect_quantity',
        'wrong_supply_text',
        'other_text',
        'created_at',
        'updated_at',
    ];

    public function delivery()
    {
        return $this->belongsTo(Deliveries::class, 'delivery_id');
    }

    public function rhu()
    {
        return $this->belongsTo(Rhu::class, 'rhu_id')->select('id','rhu_name');
    }

    public function barangay()
    {
        return $this->belongsTo(Barangays::class, 'barangay_id')->select('id','barangay_name');
    }

}
