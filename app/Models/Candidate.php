<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $table = 'favorites_candidates';
    protected $primaryKey = 'id';
    protected $guarded = [];


}
