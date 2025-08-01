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
            'price' => $this->faker->randomFloat(2, 1000, 100000), 
            'description' => $this->faker->sentence(),
            'image' => fake()->randomElement([
                'https://images.unsplash.com/photo-1568901346375-23c9450c58cd',
                'https://images.unsplash.com/photo-1513104890138-7c749659a591',
                'https://plus.unsplash.com/premium_photo-1664472682525-0c0b50534850',
            ]),
            'is_available' => $this->faker->boolean(),
        ];
    }
}
