# 🚀 Inventaris - Product Management & Inventory API

Aplikasi Back-End RESTful API dan Web Dashboard profesional untuk pengelolaan produk inventaris. Dibangun menggunakan **PHP Native** dan **SQLite**, dilengkapi dengan antarmuka web modern bergaya SaaS (*Software as a Service*) menggunakan **Tailwind CSS**.

---

## 🛠️ Tech Stack & Tools
* **Bahasa Pemrograman:** PHP (>= 8.0)
* **Database:** SQLite (Relational Database, file lokal `database.sqlite`)
* **Styling & UI:** Tailwind CSS & Font Awesome (Dark Mode SaaS Theme)
* **Keamanan:** Password Hashing (`password_hash`), Prepared Statements (Anti SQL Injection), & Bearer Token Authentication
* **Tools Testing:** Browser, Postman, atau cURL

---

## 🌟 Fitur Utama & Kriteria Penilaian
1. **Autentikasi Aman Berbasis Database:** Proses login terintegrasi dengan tabel `users` di SQLite menggunakan fungsi enkripsi standar industri (*password hashing*).
2. **Full CRUD Produk:** 
   * Tambah produk baru dengan informasi lengkap (Nama, Deskripsi, Harga, Kategori, Status Ketersediaan).
   * Lihat daftar seluruh produk.
   * Perbarui (*Update*) data produk berdasarkan ID.
   * Hapus (*Delete*) produk berdasarkan ID.
3. **Pencarian & Filter Tingkat Lanjut:** 
   * Pencarian fleksibel berdasarkan nama produk atau kategori.
   * Filter produk berdasarkan status ketersediaan (`tersedia` / `habis`).
4. **Fitur Tambahan (Bonus Nilai Plus):**
   * **Dashboard Statistik Real-Time:** Endpoint dan tampilan kartu ringkasan untuk memantau total produk, stok tersedia, dan stok habis.
   * **Middleware Validasi Input:** Menolak data kosong atau tidak valid dengan respons HTTP `422 Unprocessable Entity`.
   * **Toast Notification & UI Modern:** Mengganti pop-up standar browser dengan notifikasi interaktif yang elegan.

---

## 📂 Struktur Direktori Proyek
```text
product-api/
├── database.php        # Konfigurasi koneksi PDO SQLite & Auto-Seeder Tabel
├── database.sqlite     # File database lokal (Tabel products & users)
├── index.php           # Single Entry Point (RESTful API Backend + Web Dashboard UI)
└── README.md           # Dokumentasi Proyek

---

## 🚀 Cara Menjalankan Aplikasi

1. **Clone Repository**
   ```bash
   git clone [https://github.com/username/product-api.git](https://github.com/username/product-api.git)
   cd product-api

2. **Jalankan Server Lokal PHP**
   ```bash
   php -S localhost:8000

3. **Akses Aplikasi Melalui Browser**
   Buka browser dan kunjungi: http://localhost:8000
   Masuk menggunakan kredensial default database:
   Email: admin@test.com
   Password: password123