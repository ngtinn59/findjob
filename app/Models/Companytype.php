<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companytype extends Model
{
    use HasFactory;
    protected $table = 'company_types';

    protected $primaryKey = 'id';
    protected $guarded = [];

    public function company()
    {
        return $this->hasOne(Company::class,'company_size_id','id');
    }
}
