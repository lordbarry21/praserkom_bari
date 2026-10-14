<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Tiket Konser</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 font-sans">

    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Pemesanan Tiket Konser</h1>
            <p class="text-gray-500 text-sm mt-1">Pilih tiket konser / event musik dan masukkan data pemesan Anda</p>
        </div>

        <!-- NOTIFIKASI SUCCESS / ERROR / VALIDATION -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 text-center font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 text-center font-semibold">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 font-semibold">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- FILTER KATEGORI (JAVASCRIPT) -->
        <div class="flex flex-wrap justify-center gap-3 mb-8">
            <button type="button" onclick="filterCategory('all', this)" 
               class="btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-blue-600 text-white shadow-md">
                Semua Event
            </button>
            <button type="button" onclick="filterCategory('Musik', this)" 
               class="btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-white text-gray-600 hover:bg-gray-200 border">
                Musik
            </button>
            <button type="button" onclick="filterCategory('Festival', this)" 
               class="btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-white text-gray-600 hover:bg-gray-200 border">
                Festival
            </button>
            <button type="button" onclick="filterCategory('Seminar', this)" 
               class="btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-white text-gray-600 hover:bg-gray-200 border">
                Seminar
            </button>
        </div>

        <form id="orderForm" action="{{ route('customer.checkout') }}" method="POST">
            @csrf

            <!-- 1. Informasi Pemesan -->
            <div class="bg-white p-6 rounded-xl shadow-sm border mb-6">
                <h2 class="text-lg font-bold text-gray-700 mb-4 pb-2 border-b">1. Informasi Pemesan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Nama Lengkap</label>
                        <input type="text" id="customer_name" name="customer_name" required placeholder="Masukkan nama pemesan" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Email Pemesan</label>
                        <input type="email" id="customer_email" name="customer_email" required placeholder="Contoh: pemesan@gmail.com" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
            </div>

            <!-- 2. Pilih Menu / Tiket -->
            <h2 class="text-lg font-bold text-gray-700 mb-4">2. Pilih Tiket Konser</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach(($foods ?? $events) as $food)
                    <!-- Tambahkan data-category di setiap card -->
                    <div class="food-card bg-white rounded-xl shadow-sm border overflow-hidden flex flex-col justify-between" 
                         data-category="{{ $food->category ?? 'Musik' }}">
                        <div>
                            @if($food->image)
                                <img src="{{ asset('storage/' . $food->image) }}" alt="{{ $food->title ?? $food->name }}" class="w-full h-40 object-cover">
                            @else
                                <div class="bg-gray-200 h-40 flex items-center justify-center text-gray-400 font-medium">
                                    Tanpa Gambar
                                </div>
                            @endif

                            <div class="p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs bg-blue-100 text-blue-700 font-semibold px-2.5 py-0.5 rounded">
                                        {{ $food->category ?? 'Musik' }}
                                    </span>
                                    <span class="font-bold text-gray-900">Rp {{ number_format($food->ticket_price ?? $food->price, 0, ',', '.') }}</span>
                                </div>
                                <h3 class="font-bold text-gray-800 text-lg item-name">{{ $food->title ?? $food->name }}</h3>
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $food->description }}</p>
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 border-t">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Jumlah Tiket</label>
                            <input type="number" name="items[{{ $food->id }}]" min="0" value="0" 
                                   data-name="{{ $food->title ?? $food->name }}" 
                                   data-price="{{ $food->ticket_price ?? $food->price }}"
                                   class="item-qty w-full border rounded-lg px-3 py-1.5 text-center font-bold text-gray-700 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pesan jika kategori kosong -->
            <div id="emptyCategoryMessage" class="hidden bg-white p-8 rounded-xl border text-center text-gray-500 font-medium mt-4">
                Tiket untuk kategori ini belum tersedia.
            </div>

            <!-- Tombol Submit -->
            <div class="mt-8 text-right">
                <button type="button" onclick="showConfirmationModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-xl shadow-md transition">
                    Pesan Sekarang
                </button>
            </div>

            <!-- MODAL KONFIRMASI PESANAN (DI DALAM FORM) -->
            <div id="confirmModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl transform transition-all">
                    <h3 class="text-xl font-bold text-gray-800 border-b pb-3 mb-4">Konfirmasi Pesanan Tiket</h3>
                    
                    <div class="space-y-2 text-sm text-gray-600 mb-4">
                        <div class="flex justify-between"><span class="font-semibold">Nama:</span> <span id="modalName" class="text-gray-900 font-bold"></span></div>
                        <div class="flex justify-between"><span class="font-semibold">Email:</span> <span id="modalEmail" class="text-gray-900 font-bold"></span></div>
                    </div>

                    <div class="border-t border-b py-3 mb-4 max-h-48 overflow-y-auto">
                        <p class="font-semibold text-xs text-gray-400 uppercase mb-2">Rincian Tiket</p>
                        <ul id="modalItemList" class="space-y-2 text-sm"></ul>
                    </div>

                    <div class="flex justify-between items-center text-lg font-bold text-gray-800 mb-6">
                        <span>Total Pembayaran:</span>
                        <span id="modalTotalPrice" class="text-gray-900 text-xl font-extrabold">Rp 0</span>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" onclick="closeConfirmationModal()" class="w-1/2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2.5 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="w-1/2 bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-xl shadow transition">
                            Ya, Kirim Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- SCRIPT JAVASCRIPT -->
    <script>
        // Fungsi Filter Kategori Tanpa Reload Halaman
        function filterCategory(category, element) {
            document.querySelectorAll('.btn-category').forEach(btn => {
                btn.className = "btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-white text-gray-600 hover:bg-gray-200 border";
            });

            element.className = "btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-blue-600 text-white shadow-md";

            const cards = document.querySelectorAll('.food-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const cardCategory = card.getAttribute('data-category');
                if (category === 'all' || cardCategory === category) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            const emptyMsg = document.getElementById('emptyCategoryMessage');
            if (visibleCount === 0) {
                emptyMsg.classList.remove('hidden');
            } else {
                emptyMsg.classList.add('hidden');
            }
        }

        // Fungsi Modal Konfirmasi
        function showConfirmationModal() {
            const name = document.getElementById('customer_name').value.trim();
            const email = document.getElementById('customer_email').value.trim();

            if (!name || !email) {
                alert('Silakan isi Nama Lengkap dan Email Pemesan terlebih dahulu!');
                return;
            }

            const items = document.querySelectorAll('.item-qty');
            let itemListHtml = '';
            let grandTotal = 0;
            let hasOrder = false;

            items.forEach(input => {
                const qty = parseInt(input.value) || 0;
                if (qty > 0) {
                    hasOrder = true;
                    const name = input.getAttribute('data-name');
                    const price = parseFloat(input.getAttribute('data-price'));
                    const subtotal = qty * price;
                    grandTotal += subtotal;

                    itemListHtml += `
                        <li class="flex justify-between items-center">
                            <div>
                                <span class="font-bold text-gray-800">${name}</span>
                                <span class="text-xs text-gray-500 block">x${qty} @ Rp ${price.toLocaleString('id-ID')}</span>
                            </div>
                            <span class="font-semibold text-gray-700">Rp ${subtotal.toLocaleString('id-ID')}</span>
                        </li>
                    `;
                }
            });

            if (!hasOrder) {
                alert('Pilih minimal 1 tiket konser dengan jumlah lebih dari 0!');
                return;
            }

            document.getElementById('modalName').textContent = name;
            document.getElementById('modalEmail').textContent = email;
            document.getElementById('modalItemList').innerHTML = itemListHtml;
            document.getElementById('modalTotalPrice').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');

            const modal = document.getElementById('confirmModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeConfirmationModal() {
            const modal = document.getElementById('confirmModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    </script>
</body>
</html>
