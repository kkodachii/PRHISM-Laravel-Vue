<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangays extends Model
{
    use HasFactory;

    protected $fillable = [
        'barangay_name',
        'rhu_id'
    ];
    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(User::class, 'barangay_user', 'barangay_id', 'user_id');
    }

    public function rhu()
    {
        return $this->belongsTo(Rhu::class, 'rhu_id')->select('id', 'rhu_name');
    }
    public function midwifeInventories()
{
    return $this->hasMany(Midwife_inventories::class, 'barangay_id');
}


}
