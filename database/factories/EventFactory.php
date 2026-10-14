<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'category' => fake()->randomElement(['Musik', 'Festival', 'Seminar']),
            'ticket_price' => fake()->numberBetween(100000, 2000000),
            'description' => fake()->paragraph(),
            'image' => null,
        ];
    }
}
