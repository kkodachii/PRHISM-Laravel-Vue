<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Database_backups extends Model
{
    use HasFactory;

    protected $table = 'database_backups';

    protected $fillable = ['path', 'user_id'];



}
