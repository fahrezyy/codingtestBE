# Inventaris - Product Management System (PHP & SQLite)

Aplikasi Back-End API sekaligus Web Dashboard profesional untuk pengelolaan produk inventaris. Dibangun menggunakan **PHP Native**, **SQLite** sebagai basis data lokal (mencakup tabel produk dan manajemen akun pengguna), serta **Tailwind CSS** untuk antarmuka pengguna bergaya SaaS modern.

---

## 🛠️ Bahasa Pemrograman & Tools
* **Bahasa:** PHP (>= 8.0)
* **Database:** SQLite (`database.sqlite`)
* **Styling & UI:** Tailwind CSS & Font Awesome
* **Tools Testing API:** Browser

---

## ✨ Fitur Utama
1. **Web Dashboard Interaktif & Modern:** Antarmuka bergaya SaaS profesional dengan mode gelap (*dark mode*) dan animasi responsif.
2. **Autentikasi Berbasis Database:** Proses login terintegrasi langsung dengan tabel `users` di SQLite (menggunakan *password hashing* yang aman).
3. **CRUD Produk:** Tambah, lihat, ubah, dan hapus data produk secara real-time.
4. **Pencarian & Filter:** Cari produk berdasarkan nama/kategori dan filter berdasarkan status ketersediaan (*tersedia* / *habis*).
5. **Statistik Real-Time:** Kartu ringkasan total produk, stok tersedia, dan stok habis.
6. **Validasi Input & Toast Notification:** Validasi ketat pada form dan sistem notifikasi mengapung yang interaktif.

---

## 🚀 Cara Menjalankan Aplikasi

1. **Clone Repository**
   ```bash
   git clone [https://github.com/username/product-api.git](https://github.com/username/product-api.git)
   cd product-api