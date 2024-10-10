<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;
    protected $table = 'skills';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function experiences()
    {
        return $this->hasOne(experiences::class,'skills_id','id');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class,'profiles_id','id');
    }
}
