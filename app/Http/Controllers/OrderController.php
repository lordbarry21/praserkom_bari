<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Menampilkan katalog tiket konser untuk pelanggan (Halaman Utama Customer UI).
     * CONTOH: Mengambil seluruh event konser aktif untuk ditampilkan beserta filter kategori.
     */
    public function index()
    {
        $events = Event::latest()->get();
        $foods = $events; // Kompatibilitas jika view memanggil $foods

        return view('customer.index', compact('events', 'foods'));
    }

    /**
     * Memproses checkout pesanan tiket pelanggan dan menyimpannya ke 2 tabel (orders & order_details).
     * Menerapkan Database Transaction (DB::beginTransaction) demi integritas dan konsistensi data.
     */
    public function store(Request $request)
    {
        // 1. Validasi Keamanan Input (Merujuk Unit Kompetensi JWP)
        // CONTOH: Memastikan nama pemesan dan email terisi dengan format valid, serta array kuantitas tiket valid.
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'table_number' => 'nullable|string|max:50',
            'items' => 'required|array',
            'items.*' => 'nullable|integer|min:0',
        ]);

        $customerEmail = $request->customer_email;
        if (empty($customerEmail)) {
            $customerEmail = $request->filled('table_number')
                ? 'meja'.$request->table_number.'@customer.com'
                : 'pemesan@customer.com';
        }

        // 2. Filter hanya tiket yang kuantitasnya > 0
        // CONTOH: Dari daftar tiket yang ada, ambil hanya tiket yang dipesan pemesan.
        $orderedItems = array_filter($request->items, fn ($qty) => (int) $qty > 0);

        if (empty($orderedItems)) {
            return back()->with('error', 'Pilih minimal satu tiket konser/event dengan jumlah lebih dari 0!');
        }

        // 3. Memulai Sesi Database Transaction
        // CONTOH: Menjaga integritas data multi-tabel (orders dan order_details).
        // Jika penyimpanan tabel 'orders' sukses namun terjadi error saat simpan 'order_details',
        // maka rollBack() akan membatalkan (meng-UNDO) data order sehingga tidak ada data transaksi yatim piatu.
        DB::beginTransaction();
        try {
            // A. Simpan Header Pesanan ke tabel 'orders'
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $customerEmail,
                'total_price' => 0, // Nilai awal 0, dihitung dari akumulasi loop tiket di bawah
                'status' => 'Pending',
            ]);

            $totalPrice = 0;

            // B. Looping setiap tiket konser yang dipesan untuk disimpan ke tabel 'order_details'
            foreach ($orderedItems as $eventId => $quantity) {
                $qty = (int) $quantity;
                $event = Event::findOrFail($eventId);

                // Menghitung subtotal tiket: harga per tiket dikali jumlah tiket yang dibeli
                $ticketPrice = $event->ticket_price;
                $subtotal = $ticketPrice * $qty;
                $totalPrice += $subtotal; // Akumulasikan ke total harga keseluruhan

                // Simpan baris detail transaksi ke tabel 'order_details'
                OrderDetail::create([
                    'order_id' => $order->id,        // Foreign Key ke tabel orders
                    'event_id' => $event->id,        // Foreign Key ke tabel events
                    'quantity' => $qty,              // Jumlah tiket yang dibeli
                    'subtotal' => $subtotal,         // Harga dikali jumlah tiket
                ]);
            }

            // C. Update total_price di tabel header 'orders' setelah semua tiket selesai dihitung
            $order->update(['total_price' => $totalPrice]);

            // D. Jika seluruh baris transaksi berhasil tanpa kesalahan, simpan permanen ke MySQL
            DB::commit();

            return redirect()->route('customer.index')->with(
                'success',
                'Pesanan tiket #'.$order->id.' berhasil dibuat untuk '.$order->customer_name.' ('.$order->customer_email.') dengan total Rp '.number_format($totalPrice, 0, ',', '.').'!'
            );
        } catch (\Exception $e) {
            // E. Jika terjadi kendala/error, batalkan semua perubahan (UNDO) agar database tetap bersih & aman
            DB::rollBack();

            return back()->with('error', 'Gagal memproses pesanan tiket: '.$e->getMessage());
        }
    }

    /**
     * Menampilkan rekap seluruh pesanan di Dashboard Admin beserta ringkasan statistik.
     */
    public function adminDashboard()
    {
        // Eager Loading (with):
        // CONTOH: Mengambil data order sekaligus orderDetails dan event dalam 1 query SQL efisien.
        // Mencegah N+1 Problem (query database berulang-ulang yang memperlambat website).
        $orders = Order::with(['orderDetails.event'])->latest()->get();

        // Ringkasan statistik untuk informasi cepat admin
        $totalOrders = $orders->count();
        $totalRevenue = $orders->where('status', 'Confirmed')->sum('total_price');
        $pendingCount = $orders->where('status', 'Pending')->count();
        $confirmedCount = $orders->where('status', 'Confirmed')->count();
        $cancelledCount = $orders->where('status', 'Cancelled')->count();

        return view('dashboard', compact(
            'orders',
            'totalOrders',
            'totalRevenue',
            'pendingCount',
            'confirmedCount',
            'cancelledCount'
        ));
    }

    /**
     * Update status pesanan oleh Admin (Pending, Confirmed, Cancelled).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Confirmed,Cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan #'.$order->id.' berhasil diperbarui menjadi '.$order->status.'!');
    }
}
