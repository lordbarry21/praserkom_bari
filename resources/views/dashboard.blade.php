<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-800 leading-tight">
                    {{ __('Dashboard Rekap Pesanan Tiket') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Memantau seluruh transaksi dan pesanan tiket konser musik yang masuk</p>
            </div>
            
            <!-- Quick Actions -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('events.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-xs font-bold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Event
                </a>
                <a href="{{ route('events.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-xs font-bold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Kelola Master Konser
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Notifikasi Flash -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg shadow-sm text-sm font-medium flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-lg shadow-sm text-sm font-medium flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Ringkasan Statistik Pesanan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Pesanan -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 block">Total Pesanan</span>
                    <div class="text-2xl font-extrabold text-gray-900 mt-2">
                        {{ $totalOrders ?? $orders->count() }}
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Seluruh tiket yang masuk</p>
                </div>

                <!-- Total Pendapatan -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 block">Total Pendapatan</span>
                    <div class="text-2xl font-extrabold text-gray-900 mt-2">
                        Rp {{ number_format($totalRevenue ?? $orders->where('status', 'Confirmed')->sum('total_price'), 0, ',', '.') }}
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Dari pesanan terkonfirmasi</p>
                </div>

                <!-- Pesanan Pending -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 block">Menunggu (Pending)</span>
                    <div class="text-2xl font-extrabold text-gray-900 mt-2">
                        {{ $pendingCount ?? $orders->where('status', 'Pending')->count() }}
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Perlu tindakan/konfirmasi</p>
                </div>

                <!-- Pesanan Confirmed -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 block">Terkonfirmasi</span>
                    <div class="text-2xl font-extrabold text-gray-900 mt-2">
                        {{ $confirmedCount ?? $orders->where('status', 'Confirmed')->count() }}
                    </div>
                    <p class="text-xs text-grey-400 mt-1">Pesanan tiket sukses</p>
                </div>
            </div>

            <!-- Tabel Daftar Pesanan Masuk (Admin UI) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-800 text-base">Daftar Transaksi Pesanan Tiket</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Pantau data pemesan, rincian tiket, total pembayaran, dan kelola status pesanan</p>
                    </div>
                    <span class="text-xs bg-gray-100 text-gray-600 font-semibold px-2.5 py-1 rounded-md">
                        {{ $orders->count() }} Data
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-bold tracking-wider">
                            <tr>
                                <th class="p-4 border-b">ID Pesanan</th>
                                <th class="p-4 border-b">Waktu</th>
                                <th class="p-4 border-b">Nama Pemesan</th>
                                <th class="p-4 border-b">Email Pemesan</th>
                                <th class="p-4 border-b">Rincian Tiket</th>
                                <th class="p-4 border-b">Total Bayar</th>
                                <th class="p-4 border-b">Status</th>
                                <th class="p-4 border-b text-center">Aksi Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="p-4 font-mono font-bold text-gray-700">
                                        #{{ $order->id }}
                                    </td>
                                    <td class="p-4 text-xs text-gray-500 whitespace-nowrap">
                                        {{ $order->created_at ? $order->created_at->format('d M Y, H:i') : '-' }}
                                    </td>
                                    <td class="p-4 font-bold text-gray-900">
                                        {{ $order->customer_name }}
                                    </td>
                                    <td class="p-4 text-xs font-mono text-gray-600">
                                        {{ $order->customer_email }}
                                    </td>
                                    <td class="p-4">
                                        <ul class="space-y-1.5 min-w-[200px]">
                                            @foreach($order->orderDetails as $detail)
                                                <li class="text-xs text-gray-700 flex items-start justify-between gap-2">
                                                    <span>
                                                        <strong class="font-semibold text-gray-900">{{ $detail->event->title ?? 'Tiket Dihapus' }}</strong>
                                                        <span class="text-gray-500">x{{ $detail->quantity }}</span>
                                                    </span>
                                                    <span class="font-semibold text-gray-800 whitespace-nowrap">
                                                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-gray-800 font-semibold">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-gray-800 font-medium">
                                        {{ $order->status }}
                                    </td>
                                    <td class="p-4 text-center whitespace-nowrap">
                                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()"
                                                    class="text-xs border border-gray-300 rounded-lg p-1.5 bg-white shadow-sm font-semibold text-gray-700 cursor-pointer focus:ring-2 focus:ring-blue-500 outline-none">
                                                <option value="Pending" {{ strtolower($order->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="Confirmed" {{ in_array(strtolower($order->status), ['confirmed', 'completed', 'selesai']) ? 'selected' : '' }}>Confirmed</option>
                                                <option value="Cancelled" {{ in_array(strtolower($order->status), ['cancelled', 'canceled', 'batal']) ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-10 text-center text-gray-400 font-medium">
                                        Belum ada pesanan tiket yang masuk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
