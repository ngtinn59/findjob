<?php

namespace Database\Factories;

use App\Models\DesiredLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DesiredLevel>
 */
class DesiredLevelFactory extends Factory
{
    protected $model = DesiredLevel::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
