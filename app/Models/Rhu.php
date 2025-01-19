<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rhu extends Model
{
    use HasFactory;

    protected $fillable = ['rhu_name','location'];

}
