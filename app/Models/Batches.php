<?php

namespace App\Models;

use App\Casts\HumanReadableTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Batches extends Model
{
    use HasFactory;

    protected $table = 'batches';

    protected $fillable = [
        'batch_number',
        'barangay_id'
    ];

    protected $casts =[
        'created_at' => HumanReadableTime::class,
    ];

    public $timestamps = false;
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($batch) {
            $batch->barangay_id = Auth::user()->barangay_id;
        });
    }


    public function medicines()
    {
        return $this->hasMany(Medicines::class, 'batch_id');
    }

    public function equipment()
    {
        return $this->hasMany(Equipments::class, 'batch_id');
    }

    public function medicalSupplies()
    {
        return $this->hasMany(Medical_supplies::class, 'batch_id');
    }
}
