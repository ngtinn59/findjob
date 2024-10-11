<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $table = 'cities';

    protected $primaryKey = 'id';
    protected $guarded = [];

    public  function company()
    {
        return $this->hasOne('App\Models\Company', 'country_id', 'id');
    }


    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function districts()
    {
        return $this->hasMany(District::class);
    }
}
