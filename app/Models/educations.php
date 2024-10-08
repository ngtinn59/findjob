<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class educations extends Model
{
    use HasFactory;

    protected $table = 'educations';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

}
