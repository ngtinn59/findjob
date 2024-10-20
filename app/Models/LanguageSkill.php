<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LanguageSkill extends Model
{
    use HasFactory;
    protected $table = 'language_skills';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function profile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class,'language_id','id');
    }


}
