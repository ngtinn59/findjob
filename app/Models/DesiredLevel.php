<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesiredLevel extends Model
{
    use HasFactory;
    protected $table = 'desired_levels';

    protected $primaryKey = 'id';
    protected $guarded = [];
}
