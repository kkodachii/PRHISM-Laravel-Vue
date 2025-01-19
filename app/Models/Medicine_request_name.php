<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine_request_name extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'generic_name_id',
        'quantity',
    ];

    public function genericName()
    {
        return $this->belongsTo(Generic_names::class, 'generic_name_id');
    }
}
