<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class aboutme extends Model
{
    use HasFactory;
    protected $table = 'aboutme';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
