<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Menampilkan daftar master data event/konser (admin).
     */
    public function index()
    {
        $events = Event::latest()->paginate(10);

        return view('admin.events.index', compact('events'));
    }

    /**
     * Menampilkan form untuk menambah event baru.
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Menyimpan data event baru yang diinput admin ke database.
     */
    public function store(Request $request)
    {
        // Mendukung input 'name' maupun 'title' agar fleksibel
        if (! $request->has('title') && $request->has('name')) {
            $request->merge(['title' => $request->input('name')]);
        }

        // Mendukung input 'price' agar selaras dengan kolom database 'ticket_price'
        if (! $request->has('ticket_price') && $request->has('price')) {
            $request->merge(['ticket_price' => $request->input('price')]);
        }

        // 1. Validasi Keamanan Input (Merujuk Unit Kompetensi JWP)
        // CONTOH: Validasi judul event, kategori wajib (Musik, Festival, Seminar), harga tiket angka minimal 0, dan deskripsi teks.
        // File poster event dibatasi format gambar jpeg/png/jpg max 2MB (2048 KB) agar penyimpanan server efisien dan aman.
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:Musik,Festival,Seminar',
            'ticket_price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Proses Upload Gambar Poster jika diunggah
        // CONTOH: File poster disimpan ke folder 'storage/app/public/events' dengan nama acak unik
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
        }

        // 3. Simpan data event ke database MySQL (Tabel events)
        Event::create([
            'title' => $request->title,
            'category' => $request->category,
            'ticket_price' => $request->ticket_price,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return redirect()->route('events.index')->with('success', 'Data event berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit event yang dipilih.
     */
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Memperbarui data event yang sudah ada di database.
     */
    public function update(Request $request, Event $event)
    {
        // Mendukung input 'name' maupun 'title' agar fleksibel
        if (! $request->has('title') && $request->has('name')) {
            $request->merge(['title' => $request->input('name')]);
        }

        // Mendukung input 'price' maupun 'ticket_price'
        if (! $request->has('ticket_price') && $request->has('price')) {
            $request->merge(['ticket_price' => $request->input('price')]);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:Musik,Festival,Seminar',
            'ticket_price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = $event->image; // Default: tetap memakai poster yang lama

        // CONTOH: Jika poster diganti dengan file baru, poster lama dihapus dari storage fisik
        if ($request->hasFile('image')) {
            if ($event->image && Storage::disk('public')->exists($event->image)) {
                Storage::disk('public')->delete($event->image);
            }
            $imagePath = $request->file('image')->store('events', 'public');
        }

        $event->update([
            'title' => $request->title,
            'category' => $request->category,
            'ticket_price' => $request->ticket_price,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return redirect()->route('events.index')->with('success', 'Data event berhasil diperbarui!');
    }

    /**
     * Menghapus data event dari database beserta file posternya.
     */
    public function destroy(Event $event)
    {
        // CONTOH: Jika data event dihapus, file gambar poster fisik di storage juga ikut dibersihkan
        if ($event->image && Storage::disk('public')->exists($event->image)) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return redirect()->route('events.index')->with('success', 'Data event berhasil dihapus!');
    }
}
