<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companyskill extends Model
{
    use HasFactory;
    protected $table = 'skillscopamies';

    protected $primaryKey = 'id';
    protected $guarded = [];

    public function company()
    {
        return $this->hasOne(Company::class,'company_id','id');
    }
}
