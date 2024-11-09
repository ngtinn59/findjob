<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySize extends Model
{
    use HasFactory;
    protected $table = 'company_sizes';

    protected $primaryKey = 'id';
    protected $guarded = [];

    public function company()
    {
        return $this->hasOne(Company::class,'company_size_id','id');
    }
}
