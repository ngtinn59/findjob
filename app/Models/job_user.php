<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class job_user extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'job_user';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
