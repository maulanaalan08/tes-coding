# REST API Documentation

Dokumentasi lengkap REST API untuk sistem manajemen Master Items & Kategori Medify.

---

## 1. Master Items Endpoints

### 1.1 List Items
```http
GET /master-items
```
* **Deskripsi**: Menampilkan halaman HTML daftar Master Items dengan DataTable AJAX.
* **Response**: View Blade (`master_items.index.index`).

---

### 1.2 Search Items (AJAX Endpoint)
```http
GET /master-items/search
```
* **Deskripsi**: Mengambil data items dengan filter dinamis dan eager loading relasi kategori.
* **Query Parameters**:
  * `kode` (string, opsional): Filter kode item (exact match).
  * `nama` (string, opsional): Filter nama item (partial match / LIKE).
  * `hargamin` (integer, opsional): Minimum `harga_beli`.
  * `hargamax` (integer, opsional): Maximum `harga_beli`.

* **Response (JSON)**:
```json
{
  "status": 200,
  "data": [
    {
      "id": 1,
      "kode": "00001",
      "nama": "Paracetamol 500mg",
      "foto": "1699012345_abc123.jpg",
      "foto_url": "http://localhost/uploads/1699012345_abc123.jpg",
      "jenis": "Obat",
      "harga_beli": 50000,
      "laba": 25,
      "supplier": "Tokopaedi",
      "kategoris": "Obat Generik, Farmasi"
    }
  ]
}
```

---

### 1.3 Create Form
```http
GET /master-items/create
```
* **Deskripsi**: Menampilkan form untuk membuat item baru.

---

### 1.4 Store Item
```http
POST /master-items
Content-Type: multipart/form-data
```
* **Headers**: `X-CSRF-TOKEN: {token}`
* **Request Body**:
  * `nama` (required, string, max 255)
  * `harga_beli` (required, numeric, min 0)
  * `laba` (required, numeric, 0-100)
  * `supplier` (required, string)
  * `jenis` (required, in: Obat, Alkes, Matkes, Umum, ATK)
  * `foto` (optional, image, max 2MB)
  * `kategoris[]` (optional, array of kategori IDs)
  * `_token` (required, CSRF token)

* **Response**: Redirect ke `/master-items` dengan flash message sukses.

---

### 1.5 View Single Item
```http
GET /master-items/view/{kode}
```
* **Deskripsi**: Menampilkan detail satu item berdasarkan kode item.

---

### 1.6 Edit Form
```http
GET /master-items/{id}/edit
```
* **Deskripsi**: Menampilkan form edit item berdasarkan ID.

---

### 1.7 Update Item
```http
PUT /master-items/{id}
Content-Type: multipart/form-data
```
* **Headers**: `X-CSRF-TOKEN: {token}`
* **Request Body**: Sama seperti store, ditambah `_method=PUT`.
* **Response**: Redirect ke `/master-items` dengan flash message sukses.

---

### 1.8 Delete Item
```http
DELETE /master-items/{id}
```
* **Headers**: `X-CSRF-TOKEN: {token}`
* **Request Body**: `_token` (CSRF token)
* **Response**: Redirect ke `/master-items` dengan flash message sukses (Soft Delete).

---

### 1.9 Export Excel
```http
GET /master-items/export-excel
```
* **Deskripsi**: Download file `.xlsx` berisi daftar Master Items lengkap dengan relasi kategori (comma-separated), harga beli, laba, dan harga jual yang telah dikalkulasi.

---

## 2. Kategori Items Endpoints

### 2.1 List Kategori
```http
GET /kategoris
```
* **Deskripsi**: Menampilkan halaman daftar kategori.

---

### 2.2 Search Kategori (AJAX Endpoint)
```http
GET /kategoris/search
```
* **Query Parameters**:
  * `kode` (string, opsional): Filter kode kategori.
  * `nama` (string, opsional): Filter nama kategori.
* **Response (JSON)**:
```json
{
  "status": 200,
  "data": [
    {
      "id": 1,
      "kode": "KAT-00001",
      "nama": "Obat-obatan",
      "master_items_count": 5,
      "created_at": "2026-11-03T05:30:00.000000Z",
      "updated_at": "2026-11-03T05:30:00.000000Z"
    }
  ]
}
```

---

### 2.3 Create Form Kategori
```http
GET /kategoris/create
```

---

### 2.4 Store Kategori
```http
POST /kategoris
```
* **Request Body**:
  * `nama` (required, string, max 255)
  * `kode` (optional, string, unique:kategoris,kode) - Auto-generated `KAT-XXXXX` jika tidak diisi.
  * `_token` (required, CSRF token)

---

### 2.5 View Single Kategori
```http
GET /kategoris/view/{kode}
```
* **Deskripsi**: Menampilkan detail kategori beserta daftar master items yang terhubung.

---

### 2.6 Edit Form Kategori
```http
GET /kategoris/{id}/edit
```

---

### 2.7 Update Kategori
```http
PUT /kategoris/{id}
```
* **Request Body**:
  * `nama` (required, string, max 255)
  * `kode` (optional, string)
  * `_method=PUT`
  * `_token` (CSRF token)

---

### 2.8 Delete Kategori
```http
DELETE /kategoris/{id}
```
* **Headers**: `X-CSRF-TOKEN: {token}`
* **Response**: Soft delete kategori (item yang terhubung tetap ada).

---

### 2.9 Export PDF Kategori Detail
```http
GET /kategoris/export-pdf/{id}
```
* **Deskripsi**: Download file `.pdf` berisi detail kategori dan tabel daftar items yang terhubung beserta tanggal/waktu pencetakan.

