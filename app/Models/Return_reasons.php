<?php

namespace App\Models;

use App\Casts\HumanReadableTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Return_reasons extends Model
{
    use HasFactory;

    protected $table = 'return_reasons';

    protected $fillable = [
        'request_id',
        'reason',
        'created_at',
        'updated_at'
    ];

    public function request()
    {
        return $this->belongsTo(Requests::class, 'request_id');
    }

}
