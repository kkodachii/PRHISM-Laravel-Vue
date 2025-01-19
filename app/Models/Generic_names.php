<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generic_names extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'generic_name',
    ];
    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(Medical_categories::class, 'category_id')->select('id', 'category');
    }
}
