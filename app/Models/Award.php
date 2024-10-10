<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    protected $table = 'awards';
    protected $primaryKey = 'id';
    protected $guarded = [];
    public function profile()
    {
        return $this->belongsTo(Profile::class,'profiles_id','id');
    }
}
