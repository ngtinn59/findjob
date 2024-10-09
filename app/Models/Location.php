<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $table = 'locations';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function company()  : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->belongsTo(Company::class,'location_id','id');
    }

    public function Location_Comapny()  : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->belongsTo(Location_Comapny::class);
    }
}
