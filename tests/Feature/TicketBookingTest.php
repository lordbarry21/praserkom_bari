<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketBookingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test halaman utama customer dapat diakses dan menampilkan daftar event konser.
     */
    public function test_customer_can_view_event_catalog(): void
    {
        $event = Event::factory()->create([
            'title' => 'Coldplay Music of the Spheres',
            'category' => 'Musik',
            'ticket_price' => 850000,
        ]);

        $response = $this->get(route('customer.index'));

        $response->assertStatus(200);
        $response->assertSee('Coldplay Music of the Spheres');
        $response->assertSee('850.000');
    }

    /**
     * Test checkout tiket konser oleh pelanggan dengan validasi multi-tabel dan database transaction.
     */
    public function test_customer_can_checkout_tickets_successfully(): void
    {
        $event1 = Event::factory()->create([
            'title' => 'Festival Java Jazz 2026',
            'ticket_price' => 500000,
        ]);
        $event2 = Event::factory()->create([
            'title' => 'Seminar Sound Engineering',
            'ticket_price' => 200000,
        ]);

        $postData = [
            'customer_name' => 'Bari Testing',
            'customer_email' => 'bari@example.com',
            'items' => [
                $event1->id => 2, // 2 x 500.000 = 1.000.000
                $event2->id => 1, // 1 x 200.000 = 200.000
            ],
        ];

        $response = $this->post(route('customer.checkout'), $postData);

        $response->assertRedirect(route('customer.index'));
        $response->assertSessionHas('success');

        // Pastikan tabel orders terisi
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Bari Testing',
            'customer_email' => 'bari@example.com',
            'total_price' => 1200000,
            'status' => 'Pending',
        ]);

        $order = Order::where('customer_email', 'bari@example.com')->first();
        $this->assertNotNull($order);

        // Pastikan detail pesanan tersimpan di tabel order_details
        $this->assertDatabaseHas('order_details', [
            'order_id' => $order->id,
            'event_id' => $event1->id,
            'quantity' => 2,
            'subtotal' => 1000000,
        ]);

        $this->assertDatabaseHas('order_details', [
            'order_id' => $order->id,
            'event_id' => $event2->id,
            'quantity' => 1,
            'subtotal' => 2000000 ? 200000 : 200000,
        ]);
    }

    /**
     * Test checkout gagal bila tidak ada tiket yang dipilih (qty = 0).
     */
    public function test_customer_cannot_checkout_with_empty_items(): void
    {
        $event = Event::factory()->create();

        $response = $this->post(route('customer.checkout'), [
            'customer_name' => 'Bari Empty',
            'customer_email' => 'bari@example.com',
            'items' => [
                $event->id => 0,
            ],
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('orders', 0);
    }

    /**
     * Test admin dapat melihat dashboard rekap pesanan tiket.
     */
    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Rekap Pesanan Tiket');
    }

    /**
     * Test admin dapat melakukan CRUD event (Tambah event dengan upload gambar).
     */
    public function test_admin_can_create_event_with_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();

        $file = UploadedFile::fake()->image('poster.jpg');

        $response = $this->actingAs($admin)->post(route('events.store'), [
            'title' => 'Neon Lights Music Fest',
            'category' => 'Festival',
            'ticket_price' => 450000,
            'description' => 'Konser spektakuler penuh lampu neon dan musisi top.',
            'image' => $file,
        ]);

        $response->assertRedirect(route('events.index'));
        $this->assertDatabaseHas('events', [
            'title' => 'Neon Lights Music Fest',
            'category' => 'Festival',
            'ticket_price' => 450000,
        ]);

        $event = Event::where('title', 'Neon Lights Music Fest')->first();
        $this->assertNotNull($event->image);
        Storage::disk('public')->assertExists($event->image);
    }

    /**
     * Test admin dapat mengupdate status pesanan tiket.
     */
    public function test_admin_can_update_order_status(): void
    {
        $admin = User::factory()->create();
        $order = Order::factory()->create(['status' => 'Pending']);

        $response = $this->actingAs($admin)->patch(route('admin.orders.updateStatus', $order->id), [
            'status' => 'Confirmed',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Confirmed',
        ]);
    }
}
