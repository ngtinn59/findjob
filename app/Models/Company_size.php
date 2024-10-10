<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company_size extends Model
{
    use HasFactory;
    protected $table = 'company_sizes';

    protected $primaryKey = 'id';
    protected $guarded = [];



}
