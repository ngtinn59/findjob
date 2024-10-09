<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class experiences extends Model
{
    use HasFactory;


    protected $table = 'experiences';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function profiles()
    {
        return $this->belongsTo(profiles::class,'profiles_id', 'id');
    }

    public function companies()
    {
        return $this->belongsTo(companies::class,'companies_id','id');
    }

    public function jobs()
    {
        return $this->hasOne(jobs::class,'jobs_id','id');
    }

    public function skills()
    {
        return $this->hasOne(skills::class,'skills_id','id');
    }

}
