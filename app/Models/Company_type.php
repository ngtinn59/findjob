<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company_type extends Model
{
    use HasFactory;
    protected $table = 'company_types';

    protected $primaryKey = 'id';
    protected $guarded = [];
}
