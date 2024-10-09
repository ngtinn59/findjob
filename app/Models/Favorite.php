<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    use HasFactory;
    protected $table = 'favorites';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
