<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objective extends Model
{
    use HasFactory;

    protected $table = 'objectives';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profiles_id');
    }
}
