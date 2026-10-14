# Ticket Box - Sistem Pemesanan Tiket Konser & Event Musik

> **Uji Kompetensi Keahlian (JWP - Junior Web Programmer)**  
> **Tema Proyek:** Sistem Pemesanan Tiket Konser / Event Musik (Ticket Box)  
> **Nama Folder Proyek:** `praserkom_bari`

---

## 📌 Deskripsi & Skenario Aplikasi

**Ticket Box** adalah aplikasi web dinamis berbasis **Laravel** untuk pemesanan tiket konser musik, festival, dan seminar secara online.

### Fitur Utama:
1. **Halaman Publik (Customer UI):**
   - Katalog daftar tiket konser dan event dengan tampilan kartu modern & responsif (Tailwind CSS).
   - Filter daftar konser secara instan tanpa reload berdasarkan kategori: **Semua**, **Musik**, **Festival**, dan **Seminar**.
   - Pemilihan jumlah tiket dengan tombol counter (+ / -) yang interaktif.
   - Form pengisian data diri pelanggan (**Nama Lengkap & Alamat Email**).
   - Modal dialog konfirmasi pesanan (invoice preview & rincian total pembayaran) sebelum checkout dikirim.
   - Pemrosesan transaksi aman yang dibungkus oleh **Database Transaction (`DB::beginTransaction`)** untuk menjamin integritas multi-tabel (`orders` dan `order_details`).

2. **Halaman Pengelolaan Terproteksi (Admin UI):**
   - Sistem otentikasi login/logout terproteksi menggunakan Laravel Breeze.
   - **Dashboard Ringkasan Statistik:** Total Pesanan Masuk, Total Omset/Pendapatan, Pesanan Pending, dan Pesanan Terkonfirmasi.
   - **Rekap Pesanan Tiket:** Tabel pemantauan seluruh transaksi pesanan, data pemesan, rincian tiket beserta kuantitas & subtotalnya, serta fitur pengubah status pesanan (**Pending**, **Confirmed**, **Cancelled**).
   - **CRUD Master Data Konser:** Tambah event baru, edit informasi event, hapus event beserta pembersihan otomatis berkas poster lama di storage server, dan upload gambar poster konser.

---

## 🛠️ Kebutuhan Sistem (System Requirements)

- **PHP:** Versi 8.2 atau 8.3+ (dengan ekstensi `pdo_mysql`, `mbstring`, `fileinfo`, `openssl`, `gd`)
- **Web Server / Database:** Apache & MySQL (XAMPP / Laragon)
- **Composer:** Versi 2.x
- **Node.js & NPM:** Node.js v18+ atau v20+

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

Ikuti langkah-langkah di bawah ini untuk menginstal dan menjalankan aplikasi:

### 1. Masuk ke Direktori Proyek
Buka terminal (Command Prompt / PowerShell / Git Bash) di dalam folder proyek:
```bash
cd praserkom_bari
```

### 2. Instalasi Dependensi PHP & Frontend
```bash
composer install
npm install
npm run build
```

### 3. Konfigurasi Environment (`.env`)
Salin file konfigurasi environment bila belum ada:
```bash
cp .env.example .env
```
Pastikan pengaturan koneksi database di file `.env` mengarah ke database `db_ticket_konser`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_ticket_konser
DB_USERNAME=root
DB_PASSWORD=
```

Generate application key:
```bash
php artisan key:generate
```

### 4. Menghubungkan Storage Gambar (Symlink)
Jalankan perintah ini agar poster konser yang diunggah dapat diakses oleh browser:
```bash
php artisan storage:link
```

### 5. Migrasi & Seeder Database
Buat database `db_ticket_konser` di MySQL (lewat phpMyAdmin / terminal), lalu jalankan:
```bash
php artisan migrate:fresh --seed
```
*Perintah ini akan membuat struktur tabel `events`, `orders`, `order_details`, serta mengisi data awal konser musik dan akun admin default.*

### 6. Menjalankan Server Lokal
```bash
php artisan serve
```
Aplikasi dapat diakses melalui browser di: **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

## 🔑 Hak Akses & Kredensial Default

### 1. Pelanggan (Customer / Publik)
- **URL Akses:** `http://127.0.0.1:8000/`
- Tidak memerlukan login. Pelanggan dapat langsung memilih tiket konser, mengisi nama dan email, lalu melakukan checkout.

### 2. Administrator (Petugas / Panitia)
- **URL Login:** `http://127.0.0.1:8000/login`
- **Email:** `admin@gmail.com`
- **Password:** `password123`
- **Halaman Dashboard:** `http://127.0.0.1:8000/dashboard`
- **Halaman Master Data Event:** `http://127.0.0.1:8000/events`

---

## 🗄️ Struktur Basis Data (`db_ticket_konser`)

Sesuai ketentuan teknis JWP, basis data dirancang minimal menggunakan 3 tabel dengan relasi foreign key yang terstruktur:

### 1. Tabel `events` (Master Data Event / Konser)
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Auto Increment Primary Key |
| `title` | Varchar(255) | Nama Konser / Event |
| `category` | Enum | Pilihan: `'Musik'`, `'Festival'`, `'Seminar'` |
| `ticket_price`| Integer | Harga per tiket (Rp) |
| `description` | Text | Deskripsi lengkap konser / event |
| `image` | Varchar(255) (Nullable) | Lokasi path file poster di storage |
| `created_at` & `updated_at` | Timestamps | Waktu pembuatan & pembaruan data |

### 2. Tabel `orders` (Header Transaksi Pesanan)
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Auto Increment Primary Key |
| `customer_name` | Varchar(255) | Nama lengkap pemesan tiket |
| `customer_email` | Varchar(255) | Alamat email pemesan tiket |
| `total_price` | Integer | Total pembayaran keseluruhan pesanan |
| `status` | Enum | Pilihan: `'Pending'`, `'Confirmed'`, `'Cancelled'` |
| `created_at` & `updated_at` | Timestamps | Waktu transaksi dibuat & diperbarui |

### 3. Tabel `order_details` (Detail Transaksi / Item Tiket)
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Auto Increment Primary Key |
| `order_id` | BigInt (FK) | Relasi Foreign Key ke `orders.id` (onDelete: cascade) |
| `event_id` | BigInt (FK) | Relasi Foreign Key ke `events.id` (onDelete: cascade) |
| `quantity` | Integer | Jumlah tiket konser yang dipesan |
| `subtotal` | Integer | Harga tiket dikali jumlah kuantitas |
| `created_at` & `updated_at` | Timestamps | Waktu pencatatan data item tiket |

---

## 💡 Penjelasan Aspek Teknis (Kompetensi JWP)

1. **Database Transaction (`DB::beginTransaction()`):**
   - Diimplementasikan pada `OrderController@store`.
   - Mengelompokkan proses insert ke tabel header `orders` dan looping insert ke tabel detail `order_details` dalam satu transaksi tunggal.
   - Bila salah satu item gagal tersimpan, `DB::rollBack()` akan otomatis dipanggil untuk meng-undo seluruh perubahan sehingga mencegah terjadinya data yatim piatu / corrupt data di database.
   - Bila seluruh proses tervalidasi dan berhasil, `DB::commit()` akan mengesahkan data permanen ke MySQL.

2. **Keamanan Input & Validasi Server-Side:**
   - Menggunakan `$request->validate()` di seluruh controller (`OrderController` dan `EventController`) untuk memastikan format email, keabsahan harga non-negatif, teks judul, serta pembatasan file upload gambar (maks. 2MB dengan ekstensi JPEG/PNG/JPG).
   - Perlindungan serangan Cross-Site Request Forgery menggunakan token directive `@csrf` di setiap formulir transaksi dan aksi status.

3. **Optimasi Performa & Query Database:**
   - Menerapkan **Eager Loading** (`Order::with(['orderDetails.event'])`) di dashboard admin guna menghindari masalah **N+1 Query Problem**, sehingga ribuan baris data transaksi dapat dimuat dalam waktu milidetik secara efisien.

4. **Testing Otomatis (Unit & Feature Test):**
   - Proyek dilengkapi pengujian otomatis PHPUnit yang mencakup katalog publik, checkout transaksi multi-tabel, otentikasi login admin, CRUD konser dengan upload gambar poster, dan perubahan status pesanan.
   - Menjalankan test:
     ```bash
     php artisan test
     ```

---
*Dikembangkan untuk Uji Pra-Serkom / Sertifikasi Kompetensi JWP (Junior Web Programmer).*
