<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @method map(\Closure $param)
 */
class Job extends Model
{
    use HasFactory;

    protected $table = 'jobs';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function Company()
    {
        return $this->belongsTo(Company::class,'company_id','id');
    }

    public function skill()  : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Jobskill::class,'job_id','id');
    }

    public function jobtype()
    {
        return $this->hasMany(Jobtype::class,'id','jobtype_id');
    }
    public function jobcity()
    {
        return $this->hasMany(City::class,'id','city_id');
    }

//    public function checkSaved(){
//        return DB::table('favorites')->where('user_id', auth()->user()->id)->where('job_id', $this->id)->exists();
//    }
//
//    public function favorites(){
//        return $this->belongsToMany(Job::class, 'favorites', 'job_id', 'user_id')->withTimestamps();
//    }
//    public function checkApplication(){
//        return DB::table('job_users')->where('user_id', auth()->user()->id)->where('job_id', $this->id)->exists();
//    }


    public function users(){
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function jobSkills()  : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Jobskill::class,'job_id','id');
    }

    // Trong mô hình Job
    public function applicants()
    {
        // Sử dụng tên mô hình User và tên bảng trung gian là 'job_user'
        return $this->belongsToMany(User::class, 'job_user');
    }

    // In your Job model

// Update other relationships if necessary.

}
