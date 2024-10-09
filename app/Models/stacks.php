<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class stacks extends Model
{
    use HasFactory;
    protected $table = 'stacks';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function projects(): BelongsTo
    {
        return $this->belongsTo(projects::class);
    }

}

