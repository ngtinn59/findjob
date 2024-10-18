<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class job_user extends Model
{
    use HasFactory;

    protected $table = 'job_user';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
