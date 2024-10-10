<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class company_reviews extends Model
{
    use HasFactory;

    protected $table = 'company_reviews';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function users()
    {
        return $this->belongsTo(User::class,'users_id', 'id');
    }

    public function companies()
    {
        return $this->belongsTo(companies::class, '');
    }
}
