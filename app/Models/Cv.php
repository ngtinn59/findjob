<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    use HasFactory;
    protected $table = 'cvs';

    protected $primaryKey = 'id';

    protected $guarded = [];
    protected $fillable = ['users_id', 'file_path'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
