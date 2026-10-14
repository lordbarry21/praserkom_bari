<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-800 leading-tight">
                    {{ __('Master Data Event & Konser') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Kelola data konser musik, festival, harga tiket, dan poster event (CRUD)</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-xs font-bold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Dashboard Pesanan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg shadow-sm text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex items-center justify-between">
                <a href="{{ route('events.create') }}" class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white px-4 py-2.5 rounded-xl font-bold text-sm shadow-md transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Event Konser Baru
                </a>
                <span class="text-xs text-gray-500 font-semibold">Total: {{ $events->total() ?? $events->count() }} Event</span>
            </div>

            <!-- Tabel Data Konser -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-bold tracking-wider">
                            <tr class="border-b border-gray-200">
                                <th class="p-4 text-center">Poster</th>
                                <th class="p-4">Nama Konser / Event</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Harga per Tiket</th>
                                <th class="p-4">Deskripsi</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($events as $event)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="p-4 text-center w-24">
                                        @if($event->image)
                                            <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" class="w-16 h-16 object-cover rounded-xl shadow-sm mx-auto border border-gray-200">
                                        @else
                                            <div class="w-16 h-16 rounded-xl bg-gray-100 text-gray-400 text-xs flex flex-col items-center justify-center mx-auto border border-dashed border-gray-300">
                                                <span>No Img</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-4 font-bold text-gray-900">
                                        {{ $event->title }}
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-gray-700 font-medium">
                                        {{ $event->category }}
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-gray-800 font-semibold">
                                        Rp {{ number_format($event->ticket_price, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 text-xs text-gray-500 max-w-xs">
                                        <p class="line-clamp-2">{{ $event->description }}</p>
                                    </td>
                                    <td class="p-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('events.edit', $event->id) }}" class="inline-flex items-center px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg shadow-sm transition">
                                                Edit
                                            </a>
                                            <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus konser {{ addslashes($event->title) }}?')"
                                                        class="inline-flex items-center px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-10 text-center text-gray-400 font-medium">
                                        Belum ada data event konser. Silakan klik tombol "Tambah Event Konser Baru".
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($events->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
