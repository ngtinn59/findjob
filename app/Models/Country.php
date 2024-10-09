<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table = 'countries';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public  function company()
    {
        return $this->hasOne('App\Models\Company', 'country_id', 'id');
    }
}
