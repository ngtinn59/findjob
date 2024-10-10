<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class responsibilities extends Model
{
    use HasFactory;

    protected $table = 'responsibilities';
    protected $primaryKey = 'id';
    protected $guarded = [];


    public function experiences(): BelongsTo
    {
        return $this->belongsTo(experiences::class);
    }

}
