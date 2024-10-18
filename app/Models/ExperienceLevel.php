<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExperienceLevel extends Model
{
    use HasFactory;
    protected $table = 'experience_levels';
    protected $primaryKey = 'id';
    protected $guarded = [];


}
