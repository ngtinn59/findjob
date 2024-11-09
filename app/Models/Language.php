<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;
    protected $table = 'languages';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function languageskills()
    {
        return $this->hasMany(LanguageSkill::class,'language_id','id');
    }
}
