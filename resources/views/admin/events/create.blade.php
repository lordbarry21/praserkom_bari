<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-800 leading-tight">
                    {{ __('Tambah Event Konser Baru') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Lengkapi form di bawah untuk memasukkan master data konser musik baru</p>
            </div>
            <a href="{{ route('events.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-gray-600 hover:text-gray-900 transition">
                &larr; Kembali ke Daftar Event
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">

                <!-- Validasi Error -->
                @if($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                        <span class="font-bold block mb-1">Terdapat kesalahan pada input:</span>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Nama Event / Konser -->
                    <div>
                        <label for="title" class="block text-sm font-bold text-gray-700 mb-1.5">
                            Nama Konser / Event <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                               placeholder="Contoh: Coldplay: Music of the Spheres World Tour"
                               class="w-full bg-slate-50 border border-gray-300 rounded-xl px-4 py-2.5 text-gray-900 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>

                    <!-- Kategori & Harga Tiket -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="category" class="block text-sm font-bold text-gray-700 mb-1.5">
                                Kategori Event <span class="text-rose-500">*</span>
                            </label>
                            <select id="category" name="category" required
                                    class="w-full bg-slate-50 border border-gray-300 rounded-xl px-4 py-2.5 text-gray-900 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition cursor-pointer">
                                <option value="Musik" {{ old('category') == 'Musik' ? 'selected' : '' }}>Musik</option>
                                <option value="Festival" {{ old('category') == 'Festival' ? 'selected' : '' }}>Festival</option>
                                <option value="Seminar" {{ old('category') == 'Seminar' ? 'selected' : '' }}>Seminar</option>
                            </select>
                        </div>
                        <div>
                            <label for="ticket_price" class="block text-sm font-bold text-gray-700 mb-1.5">
                                Harga per Tiket (Rp) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" id="ticket_price" name="ticket_price" value="{{ old('ticket_price', old('price')) }}" min="0" required
                                   placeholder="Contoh: 750000"
                                   class="w-full bg-slate-50 border border-gray-300 rounded-xl px-4 py-2.5 text-gray-900 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>
                    </div>

                    <!-- Deskripsi Event -->
                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-700 mb-1.5">
                            Deskripsi Lengkap Event <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="4" required
                                  placeholder="Tuliskan deskripsi lengkap mengenai musisi, lokasi, tanggal konser, maupun rundown acara..."
                                  class="w-full bg-slate-50 border border-gray-300 rounded-xl px-4 py-2.5 text-gray-900 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition">{{ old('description') }}</textarea>
                    </div>

                    <!-- Upload Poster Event -->
                    <div>
                        <label for="image" class="block text-sm font-bold text-gray-700 mb-1.5">
                            Poster Konser (Opsional)
                        </label>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/jpg"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border file:border-gray-300 file:text-xs file:font-semibold file:bg-white file:text-gray-700 hover:file:bg-gray-50 cursor-pointer border border-gray-300 rounded-xl bg-slate-50">
                        <p class="text-xs text-gray-400 mt-1">Format file: JPG, PNG, atau JPEG (Maks. 2MB)</p>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md shadow-blue-500/20 transition">
                            Simpan Data Konser
                        </button>
                        <a href="{{ route('events.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-xl transition">
                            Batal
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>