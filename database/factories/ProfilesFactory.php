<?php

namespace Database\Factories;

use App\Models\Profiles;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProfilesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'users_id' => function () {
                // Return a randomly selected user ID from your users table
                return \App\Models\User::inRandomOrder()->first()->id;
            },
            'slug_title' => $this->faker->slug,
            'title' => $this->faker->sentence,
            'image_url' => $this->faker->imageUrl(),
            'email' => $this->faker->unique()->safeEmail,
            'phone_number' => $this->faker->phoneNumber,
            'gender' => $this->faker->numberBetween(0, 1), // Assuming 0 for male and 1 for female
            'date_of_birth' => $this->faker->date,
            'address' => $this->faker->address,
            'introduction' => $this->faker->paragraph,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now')
        ];
    }
}
