<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Manuscript>
 */
class ManuscriptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'temporal' => $this->faker->word,
            'nakala_url' => $this->faker->url,
            'dasch_url' => $this->faker->url,
            'authors' => $this->faker->word,
            'published' => $this->faker->boolean,
        ];
    }
}
