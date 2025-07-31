<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class ItemFactory extends Factory
{
   
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'category_id' => $this->faker->numberBetween(1, 2), 
            'price' => $this->faker->numberFloat(2, 10, 100), 
            'description' => $this->faker->sentence(),
            'img' => $this->faker->imageUrl(),
            'is_available' => $this->faker->boolean(),
        ];
    }
}
