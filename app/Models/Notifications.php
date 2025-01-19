<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'string',
    ];

    public function receiver()
    {
        return $this->belongsTo(User::class, 'notifiable_id')
            ->select('id', 'name', 'email', 'rhu_id', 'barangay_id', 'profile_picture', 'role_id')
            ->with(['rhus:id,rhu_name', 'barangay:id,barangay_name']);
    }

}
