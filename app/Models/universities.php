<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class universities extends Model
{
    use HasFactory;


    public function educations()
    {
        return $this->belongsTo(educations::class,'universities_id','id');
    }
}
