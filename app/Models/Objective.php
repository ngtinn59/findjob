<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objective extends Model
{
    use HasFactory;

    protected $table = 'objectives';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profiles_id');
    }

    // Trong model Objective
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    // Trong model Objective
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class, 'education_level_id');
    }

    public function employmentType()
    {
        return $this->belongsTo(EmploymentType::class, 'employment_type_id');
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class, 'profession_id');
    }



    public function desiredLevel()
    {
        return $this->belongsTo(DesiredLevel::class, 'desired_level_id');
    }

    public function experienceLevel()
    {
        return $this->belongsTo(ExperienceLevel::class, 'experience_level_id');
    }


}
