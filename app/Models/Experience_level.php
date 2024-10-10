<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience_level extends Model
{
    use HasFactory;
    protected $table = 'experience_level';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function recruitments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->belongsTo(recruitments::class);
    }
}

