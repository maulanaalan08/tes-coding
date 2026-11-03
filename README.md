# Medify Master Items Management

Sistem manajemen master data berbasis Laravel 9 dengan fitur CRUD lengkap, relasi many-to-many kategori, pencarian dinamis (filter harga min/max), upload foto, dan pelaporan export PDF/Excel.

---

## Fitur Utama

- ✅ **Master Items CRUD**: Create, Read, Update, Delete dengan arsitektur MVC yang bersih.
- ✅ **Kategori Items (Many-to-Many)**: Relasi banyak ke banyak antara items dan kategori menggunakan pivot table `kategori_master_item`.
- ✅ **Upload & Kelola Foto**: Upload foto master items dengan validasi gambar dan preview foto saat edit.
- ✅ **Filter Dinamis (Bug Fixed)**: Filter harga min, harga max, kode, dan nama yang bekerja secara independen maupun kombinasi.
- ✅ **Export PDF Detail Kategori**: Generate laporan PDF detail kategori dan daftar item menggunakan `barryvdh/laravel-dompdf`.
- ✅ **Export Excel Master Items**: Export data items dan kategori ke format `.xlsx` menggunakan `maatwebsite/excel`.
- ✅ **RESTful API Compliance (RFC 7231)**: Menggunakan resource routes dengan HTTP methods yang tepat (`POST`, `PUT`, `DELETE`).
- ✅ **CSRF Protection**: Semua operasi perubahan status (`POST`, `PUT`, `DELETE`) dilindungi token CSRF (mencegah vulnerability GET delete).
- ✅ **Concurrency-Safe Auto-Code Generation**: Penomoran kode otomatis menggunakan transaksi database dan `max(id)`.
- ✅ **Eager Loading (No N+1 Queries)**: Query data dioptimasi menggunakan `with('kategoris')` untuk performa tinggi.
- ✅ **Soft Deletes**: Menggunakan `SoftDeletes` untuk mempertahankan audit trail data SIMRS/ERP.

---

## Tech Stack

- **Backend**: Laravel 9.x, PHP 8.2
- **Frontend**: Bootstrap 5.2, jQuery, DataTables
- **Database**: MySQL 8.0
- **PDF Export**: DomPDF (`barryvdh/laravel-dompdf` v3.1)
- **Excel Export**: PhpSpreadsheet / Laravel Excel (`maatwebsite/excel` v3.1)
- **Asset Bundler**: Vite 3.0

---

## Instalasi & Menjalankan Aplikasi

```bash
# 1. Clone repository
git clone <repo-url>
cd tes-coding

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Setup database
php artisan migrate

# 5. Build assets
npm run dev

# 6. Menjalankan server lokal
php artisan serve
```

---

## Dokumentasi API & Routing

Daftar endpoint lengkap dapat dilihat pada [docs/REST-API.md](docs/REST-API.md).

### Ringkasan Routes:

| Method | URI | Action | Keterangan |
|---|---|---|---|
| GET | `/master-items` | `master-items.index` | Halaman daftar master items |
| GET | `/master-items/create` | `master-items.create` | Form tambah master item |
| POST | `/master-items` | `master-items.store` | Simpan master item baru |
| GET | `/master-items/view/{kode}` | `master-items.show` | Detail master item |
| GET | `/master-items/{id}/edit` | `master-items.edit` | Form edit master item |
| PUT | `/master-items/{id}` | `master-items.update` | Update master item |
| DELETE | `/master-items/{id}` | `master-items.destroy` | Hapus master item (soft delete) |
| GET | `/master-items/search` | `master-items.search` | AJAX endpoint DataTable master items |
| GET | `/master-items/export-excel` | `master-items.export` | Download export file Excel |
| GET | `/kategoris` | `kategoris.index` | Halaman daftar kategori |
| GET | `/kategoris/create` | `kategoris.create` | Form tambah kategori |
| POST | `/kategoris` | `kategoris.store` | Simpan kategori baru |
| GET | `/kategoris/view/{kode}` | `kategoris.show` | Detail kategori & list items |
| GET | `/kategoris/{id}/edit` | `kategoris.edit` | Form edit kategori |
| PUT | `/kategoris/{id}` | `kategoris.update` | Update kategori |
| DELETE | `/kategoris/{id}` | `kategoris.destroy` | Hapus kategori (soft delete) |
| GET | `/kategoris/search` | `kategoris.search` | AJAX endpoint DataTable kategori |
| GET | `/kategoris/export-pdf/{id}` | `kategoris.export-pdf` | Download export file PDF |

---

## Menjalankan Pengujian (Testing)

Semua unit dan feature test dapat dijalankan melalui PHPUnit:

```bash
php artisan test
```

Untuk menjalankan test suite master item dan kategori:
```bash
php artisan test --filter="KategoriTest|MasterItemTest"
```

---

## Lisensi

Aplikasi ini dilisensikan di bawah [MIT license](https://opensource.org/licenses/MIT).
