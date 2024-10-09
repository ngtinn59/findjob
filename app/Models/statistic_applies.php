<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class statistic_applies extends Model
{
    use HasFactory;
    protected $table = 'statistic_applies';
    protected $primaryKey = 'id';
    protected $guarded = [];


    public function statistic_applies()
    {
        return $this->belongsTo(statistic_applies::class,'recruitments_id','id');
    }

    public function companies()
    {
        return $this->hasOne(companies::class,'companies_id','id');
    }

}
