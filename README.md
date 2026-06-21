# Pet Food Inventory System 🐾

Sistem Manajemen Inventaris Makanan & Vitamin Hewan Peliharaan berbasis Web yang modern, responsif, dan interaktif. Proyek ini dibangun dengan memprioritaskan performa tinggi, validasi data yang ketat, dan kemudahan penggunaan bagi pemilik toko hewan peliharaan.

---

## ✨ Fitur Utama

*   **TALL Stack Modern Architecture**: Menggunakan kombinasi teknologi mutakhir **T**ailwind CSS, **A**lpine.js, **L**aravel, dan **L**ivewire v3 untuk performa aplikasi single-page application (SPA) yang sangat cepat tanpa perlu menulis JavaScript kompleks secara manual.
*   **Real-time Search & Filter**: Fitur pencarian produk secara instan berdasarkan nama produk tanpa reload halaman menggunakan keunggulan `wire:model.live` dari Livewire.
*   **Modal Form yang Halus**: Pengalaman penginputan data yang mulus dengan transisi backdrop blur dan modal scaling menggunakan integrasi Alpine.js.
*   **Validasi Harga & Stok yang Ketat**:
    *   Stok tidak boleh negatif (minimal `0`).
    *   Harga beli dan harga jual wajib bernilai positif.
    *   Proteksi logika bisnis: Harga jual tidak boleh lebih rendah dari harga beli (`gte:purchase_price`).
*   **Notifikasi Sukses Interaktif (Flash Message)**: Tampilan toast/alert Tailwind yang intuitif dengan fungsionalitas auto-dismiss (tutup otomatis) setelah 4 detik.
*   **Format Rupiah Otomatis**: Semua tampilan harga beli dan harga jual diubah secara dinamis ke dalam format rupiah Indonesia (`Rp 50.000`).
*   **Automated Tests (100% Pass)**: Dilengkapi dengan cakupan pengujian unit dan fitur (Feature Tests) menyeluruh yang mencakup validasi form, logika harga, hak akses, pencarian, dan seluruh operasi CRUD.

---

## 🛠️ Stack Teknologi

*   **Framework**: Laravel 13
*   **Frontend**: Livewire v3, Alpine.js, Tailwind CSS
*   **Database**: SQLite (dapat dikonfigurasi ke MySQL/PostgreSQL)
*   **Testing**: PHPUnit / Laravel Test Suite

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

Ikuti langkah-langkah mudah berikut untuk menjalankan proyek ini di mesin lokal Anda:

### Prasyarat (Prerequisites)
Pastikan sistem Anda sudah terinstal:
*   PHP >= 8.3
*   Composer
*   Node.js & NPM

### Langkah-Langkah Instalasi

1.  **Instal Dependensi PHP (Composer)**:
    ```bash
    composer install
    ```

2.  **Konfigurasi Environment**:
    Salin file `.env.example` menjadi `.env`:
    ```bash
    copy .env.example .env
    ```
    *(Catatan: Proyek ini menggunakan SQLite secara default. Pastikan database SQLite kosong `database/database.sqlite` sudah dibuat atau biarkan migrasi membuatnya).*

3.  **Generate Application Key**:
    ```bash
    php artisan key:generate
    ```

4.  **Instal Dependensi Frontend (NPM)**:
    ```bash
    npm install
    ```

5.  **Jalankan Migrasi & Database Seeder**:
    Perintah ini akan membuat struktur tabel baru sekaligus mengisi data kategori (Makanan Kucing, Makanan Anjing, Vitamin) serta 20+ produk tiruan yang realistis:
    ```bash
    php artisan migrate:fresh --seed
    ```

6.  **Jalankan Server Lokal**:
    Jalankan server Laravel dan compiler aset Vite secara bersamaan menggunakan perintah:
    ```bash
    npm run dev
    ```
    atau jalankan server php artisan:
    ```bash
    php artisan serve
    ```

Aplikasi sekarang dapat diakses melalui browser Anda di alamat `http://localhost:8000` (atau port yang tertera pada terminal Anda).

---

## 🧪 Menjalankan Pengujian (Testing)

Proyek ini dilengkapi dengan 34 skenario pengujian otomatis yang dapat dijalankan untuk memastikan integritas aplikasi:

```bash
php artisan test
```

Semua pengujian dijamin lulus **100% PASS** untuk memastikan fungsionalitas sistem berjalan stabil.

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).
