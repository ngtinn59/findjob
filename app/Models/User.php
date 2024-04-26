<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'companies_id',
        'account_type',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function company_reviews()
    {
        return $this->hasMany(company_reviews::class,'users_id','id');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class,'users_id','id');
    }

    public function companies()
    {
        return $this->hasOne(Company::class,'users_id','id');
    }

    public function checkApplication(){
        return DB::table('job_users')->where('users_id', auth()->user()->id)->where('job_id', $this->id)->exists();
    }

    public function favorites(){
        return $this->belongsToMany(Job::class, 'favorites', 'job_id', 'users_id')->withTimestamps();
    }

    public function checkSaved(){
        return DB::table('favorites')->where('users_id', auth()->user()->id)->where('job_id', $this->id)->exists();
    }

    public function job(){
        return $this->hasMany(Job::class,'users_id','id');
    }
//    public function jobs()
//    {
//        return $this->belongsToMany(Job::class)->withPivot('status');
//    }

    public function jobs()
    {
        return $this->belongsToMany(Job::class)->withPivot('status');
    }

    public function cvs()
    {
        return $this->hasMany(CV::class,'users_id','id');
    }
}
