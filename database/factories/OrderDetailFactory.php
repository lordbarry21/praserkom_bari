<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderDetail>
 */
class OrderDetailFactory extends Factory
{
    protected $model = OrderDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $event = Event::factory()->create();
        $qty = fake()->numberBetween(1, 5);

        return [
            'order_id' => Order::factory(),
            'event_id' => $event->id,
            'quantity' => $qty,
            'subtotal' => $event->ticket_price * $qty,
        ];
    }
}
