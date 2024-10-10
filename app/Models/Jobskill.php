<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobskill extends Model
{
    use HasFactory;

    protected $table = 'job_skills';
    protected $fillable = ['job_id', 'name'];

    protected $primaryKey = 'id';
    public function Job()
    {
        return $this->belongsTo(Job::class,'job_id','id');
    }
}
