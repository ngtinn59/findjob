<?php

namespace App\Models;

use App\Http\Controllers\Api\Admin\CompanysizesController;
use App\Http\Controllers\Api\Admin\CompanytypesController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function locations()  : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Location::class,'company_id','id');
    }

    public function Job()  : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Job::class,'company_id','id');
    }

    public  function companytype()
    {
        return $this->hasOne(Companytype::class,'id','company_type_id');
    }

    public  function companysize()
    {
        return $this->hasOne(Companysize::class, 'id', 'company_size_id');
    }

    public  function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }
    public  function city()
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }
    // In your Company model
    public function jobs()  : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Job::class,'company_id','id');
    }

    public function skills() {
        return  $this->hasMany(Skill::class,'company_id','id');
    }

}
