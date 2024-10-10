<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class skills_required extends Model
{
    use HasFactory;

    protected $table = 'skills_required';
    protected $primaryKey = 'id';
    protected $guarded = [];

}
