# PRD - Tes Rekrutmen Programmer Medify 2026

## Overview
Pengembangan fitur CRUD Kategori Items dengan relasi many-to-many ke Master Items, perbaikan bug, dan export PDF/Excel untuk sistem manajemen master items berbasis Laravel 9.

**Posisi**: Backend Programmer (Laravel - ERP/SIMRS Experience Preferred)  
**Deadline**: Live Coding Test  
**Stack**: Laravel 9 + Bootstrap 5 + Vite + MySQL + DomPDF

---

## Job Requirements Alignment

Tes ini dirancang untuk mendemonstrasikan kemampuan sesuai kualifikasi lowongan:

| Requirement Lowongan | Demonstrated in Test |
|---------------------|---------------------|
| ✅ Laravel Framework | Laravel 9 MVC pattern, Eloquent ORM, Blade templates |
| ✅ Database Migrations | Create 2 migrations (kategoris + pivot table) |
| ✅ MVC Architecture | Controller → Model → View separation yang jelas |
| ✅ REST API Development | JSON API endpoints untuk DataTable (search/filter) |
| ✅ Git Version Control | Commit strategy, branching workflow dokumentasi |
| ✅ ERP/SIMRS Patterns | Many-to-many relasi, export reports (PDF/Excel), master data CRUD |
| ✅ Client Communication | Code documentation, clear variable naming, README |

---

## System Architecture

### MVC Layers

```
┌─────────────────────────────────────────────────────────────┐
│                        PRESENTATION LAYER                    │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Blade      │  │  Bootstrap   │  │   Vite       │      │
│  │  Templates   │  │   UI/UX      │  │   Assets     │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            ↕ HTTP Request/Response
┌─────────────────────────────────────────────────────────────┐
│                      APPLICATION LAYER                       │
│  ┌──────────────────────────────────────────────────────┐   │
│  │              Controllers (HTTP Logic)                │   │
│  │  - MasterItemsController (existing)                  │   │
│  │  - KategorisController (new)                         │   │
│  │  - Auth Controllers (existing)                       │   │
│  └──────────────────────────────────────────────────────┘   │
│                            ↕                                 │
│  ┌──────────────────────────────────────────────────────┐   │
│  │        Business Logic (Optional Service Layer)       │   │
│  │  - Exports/MasterItemsExport (Excel generation)      │   │
│  │  - PDF generation logic (DomPDF)                     │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            ↕ Eloquent ORM
┌─────────────────────────────────────────────────────────────┐
│                         DATA LAYER                           │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │  MasterItem  │  │   Kategori   │  │     User     │      │
│  │    Model     │  │    Model     │  │    Model     │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
│         ↕                 ↕                   ↕              │
│  ┌──────────────────────────────────────────────────────┐   │
│  │              MySQL Database (coba)                   │   │
│  │  - master_items (with soft deletes)                  │   │
│  │  - kategoris (with soft deletes)                     │   │
│  │  - kategori_master_item (pivot table)                │   │
│  │  - users, migrations, etc.                           │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

### Request Flow (Example: Create Master Item with Kategori)

```
1. User → Form Submit (POST /master-items/form/new)
   ↓
2. MasterItemsController@formSubmit
   ↓
3. Validation (Request rules)
   ↓
4. File Upload (foto) → public/uploads + storage/app/public/uploads
   ↓
5. MasterItem::create() [Eloquent ORM]
   ↓
6. $item->kategoris()->sync($request->kategoris) [Pivot table]
   ↓
7. Redirect → /master-items (with success message)
```

---

## REST API Design

### Endpoint Specifications

#### 1. Master Items Search API
```
GET /master-items/search
```

**Query Parameters**:
- `kode` (string, optional) - Exact match filter
- `nama` (string, optional) - Partial match (LIKE %nama%)
- `hargamin` (integer, optional) - Minimum harga_beli
- `hargamax` (integer, optional) - Maximum harga_beli

**Response** (JSON):
```json
{
  "status": 200,
  "data": [
    {
      "id": 1,
      "kode": "00001",
      "nama": "Paracetamol 500mg",
      "foto": "1699012345_abc123.jpg",
      "jenis": "Obat",
      "harga_beli": 50000,
      "laba": 25,
      "supplier": "Tokopaedi"
    }
  ]
}
```

**Error Response**:
```json
{
  "status": 422,
  "errors": {
    "hargamin": ["The hargamin must be a number."]
  }
}
```

#### 2. Kategori Search API
```
GET /kategoris/search
```

**Query Parameters**:
- `kode` (string, optional) - Exact/partial match
- `nama` (string, optional) - Partial match (LIKE %nama%)

**Response** (JSON):
```json
{
  "status": 200,
  "data": [
    {
      "id": 1,
      "kode": "KAT-00001",
      "nama": "Obat Generik",
      "created_at": "2026-11-03T05:30:00.000000Z",
      "updated_at": "2026-11-03T05:30:00.000000Z"
    }
  ]
}
```

---

## Git Workflow Strategy

### Branch Structure
```
main (production-ready)
  └── develop (integration branch)
       ├── feature/fix-harga-filter
       ├── feature/kategori-crud
       ├── feature/kategori-relation
       ├── feature/export-pdf
       └── feature/export-excel
```

### Commit Message Convention (Conventional Commits)
```
type(scope): subject

[optional body]

[optional footer]
```

**Types**:
- `feat`: New feature
- `fix`: Bug fix
- `refactor`: Code restructure (no behavior change)
- `docs`: Documentation only
- `test`: Test addition/modification
- `chore`: Build/tooling changes

**Examples**:
```bash
# Bug fix
git commit -m "fix(master-items): fix harga min/max filter not working

- Cast query params to integer
- Add validation for numeric inputs
- Test range filter 100-500000"

# New feature
git commit -m "feat(kategori): implement CRUD kategori with many-to-many relation

- Create Kategori model with soft deletes
- Add belongsToMany relation to MasterItem
- Implement KategorisController with index/search/form/single views
- Add navbar links for navigation"

# Export feature
git commit -m "feat(export): add PDF export for kategori detail

- Use barryvdh/dompdf package
- Include kategori info + items table
- Add timestamp footer
- Export route: GET /kategoris/export-pdf/{id}"
```

### Workflow Steps
```bash
# 1. Create feature branch
git checkout -b feature/kategori-crud

# 2. Work & commit (atomic commits)
git add app/Models/Kategori.php
git commit -m "feat(kategori): create Kategori model with relations"

# 3. Push to remote
git push origin feature/kategori-crud

# 4. Create Pull Request (Github/Gitlab)
# Title: [FEATURE] CRUD Kategori Items
# Description: Implement task #3 from test requirements

# 5. Merge to develop after review
# 6. Final merge to main after all tests pass
```

---

## Code Quality Standards

### PSR-12 Compliance
```php
<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategorisController extends Controller
{
    /**
     * Display index page for kategoris.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('kategoris.index.index');
    }

    /**
     * Search kategoris with filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Kategori::query();

        if ($request->filled('kode')) {
            $query->where('kode', 'LIKE', '%' . $request->kode . '%');
        }

        if ($request->filled('nama')) {
            $query->where('nama', 'LIKE', '%' . $request->nama . '%');
        }

        $data = $query->select('id', 'kode', 'nama', 'created_at', 'updated_at')
                      ->orderBy('id', 'desc')
                      ->get();

        return response()->json([
            'status' => 200,
            'data' => $data,
        ]);
    }
}
```

### Laravel Best Practices

#### ✅ DO (Good Practices)
```php
// 1. Use Eloquent relationships
$kategori = Kategori::with('masterItems')->find($id); // Eager loading
foreach ($kategori->masterItems as $item) { ... }

// 2. Use Form Requests for complex validation (optional, but better)
// php artisan make:request StoreKategoriRequest

// 3. Use route model binding
Route::get('/kategoris/{kategori}', function (Kategori $kategori) {
    return view('kategoris.show', compact('kategori'));
});

// 4. Use fillable/guarded in models
protected $fillable = ['kode', 'nama'];

// 5. Use accessors for computed attributes
public function getHargaJualAttribute()
{
    return $this->harga_beli + (($this->laba / 100) * $this->harga_beli);
}
```

#### ❌ DON'T (Bad Practices)
```php
// 1. N+1 Query Problem
$kategoris = Kategori::all();
foreach ($kategoris as $kategori) {
    echo $kategori->masterItems->count(); // N queries!
}

// 2. Raw SQL when Eloquent can do it
DB::select("SELECT * FROM kategoris WHERE kode = ?", [$kode]);
// Use: Kategori::where('kode', $kode)->get();

// 3. Mass assignment without fillable/guarded
$kategori = new Kategori;
$kategori->fill($request->all()); // DANGEROUS!

// 4. Hardcoded values
if ($request->status == 'active') { ... }
// Use: constants or enums
```

### Variable Naming (Clear & Self-Documenting)
```php
// ✅ Good
$kategoriWithItems = Kategori::with('masterItems')->find($id);
$totalHargaJual = $item->harga_beli + $item->calculateLaba();
$exportFileName = 'master_items_' . now()->format('YmdHis') . '.xlsx';

// ❌ Bad
$k = Kategori::find($id);
$total = $item->harga + $item->calc();
$file = 'export.xlsx';
```

---

## ERP/SIMRS Pattern Implementation

Tes ini menggunakan pattern umum di sistem ERP/SIMRS:

### 1. Master Data Management
**Pattern**: Central master data with hierarchical categorization
- `master_items` = master data inventory/katalog item (medicines, supplies, equipment)
- `kategoris` = classification/grouping (e.g., Obat Generik, Alkes Medis, dll)
- Many-to-many relation = flexible categorization (1 item bisa punya multiple kategori)

**Real-world Analogy (SIMRS)**:
```
Master Item: Paracetamol 500mg
Kategori: 
  - Obat Generik
  - Farmasi
  - Stok Wajib
  - Insurance Covered
```

### 2. Soft Deletes (Audit Trail)
**Why**: ERP/SIMRS membutuhkan audit trail untuk compliance
```php
use Illuminate\Database\Eloquent\SoftDeletes;

// Delete tidak permanently remove data
$item->delete(); // Sets deleted_at timestamp
$item->restore(); // Undelete capability
```

### 3. Code Generation Pattern
**Pattern**: Auto-generate unique codes untuk master data
```php
// KAT-00001, KAT-00002, etc.
$kode = Kategori::count('id') + 1;
$kode = 'KAT-' . str_pad($kode, 5, '0', STR_PAD_LEFT);
```

**Real-world**: Medical record numbers, transaction IDs, batch numbers

### 4. Report Generation
**Pattern**: Export PDF/Excel untuk reporting & audit
- **PDF**: Detail view single entity (e.g., kategori + items)
- **Excel**: Bulk data export untuk analysis (e.g., all items dengan kategori)

**Real-world**: 
- Patient medical records (PDF)
- Monthly inventory reports (Excel)
- Procurement documents (PDF)

### 5. Filter & Search
**Pattern**: Multi-criteria search dengan kombinasi filter
```php
// AND conditions
->where('kode', $kode)
->where('nama', 'LIKE', '%' . $nama . '%')
->where('harga_beli', '>=', $hargamin)
->where('harga_beli', '<=', $hargamax)
```

**Real-world**: 
- Search patients by name, MR number, date range
- Search inventory by code, category, price range, supplier

### 6. Relational Integrity
**Pattern**: Foreign key constraints + cascade delete
```sql
FOREIGN KEY (kategori_id) REFERENCES kategoris(id) ON DELETE CASCADE
```

**Why**: Prevent orphaned records, maintain data integrity

---

## Documentation Standards

### 1. README.md Structure
```markdown
# Medify Master Items Management

## Features
- Master Items CRUD dengan upload foto
- Kategori Items CRUD dengan many-to-many relation
- Filter & search (kode, nama, harga)
- Export PDF (detail kategori)
- Export Excel (master items dengan kategori)

## Installation
[Commands to setup]

## API Endpoints
[List of routes]

## Testing
[How to test]

## Tech Stack
Laravel 9 | PHP 8.2 | MySQL | Bootstrap 5
```

### 2. Inline Code Comments
```php
/**
 * Search master items with multiple filters.
 * Used by DataTable AJAX for dynamic filtering.
 *
 * @param Request $request Query params: kode, nama, hargamin, hargamax
 * @return JsonResponse Status 200 dengan data array
 */
public function search(Request $request)
{
    // Initialize query builder
    $query = MasterItem::query();
    
    // Apply filters only if provided
    if ($request->filled('kode')) {
        $query->where('kode', $request->kode);
    }
    
    // Return with eager loaded relations to prevent N+1
    return response()->json([
        'status' => 200,
        'data' => $query->with('kategoris')->get()
    ]);
}
```

### 3. Migration Comments
```php
/**
 * Migration: Add foto column to master_items
 * Requirement: TASK 1 - Upload foto feature
 * Dependencies: master_items table must exist
 */
public function up()
{
    Schema::table('master_items', function (Blueprint $table) {
        // Nullable because existing records don't have foto
        $table->string('foto')->nullable()->after('jenis');
    });
}
```

### 4. Commit Messages (Review Friendly)
```bash
# Clear intent + context
git commit -m "feat(kategori): implement many-to-many relation with master items

- Add kategoris belongsToMany relation
- Add sync logic in formSubmit
- Update form view with multi-select input
- Test: assign 3 kategori to 1 item successfully

Closes: TASK-3.4"
```

---

## Technical Stack (Existing)
- **Backend**: Laravel 9.x, PHP 8.2
- **Frontend**: Bootstrap 5.2.1, Vite 3.0
- **Database**: MySQL (DB: `coba`)
- **Packages**: 
  - `barryvdh/laravel-dompdf` v3.1 (PDF export)
  - Laravel UI v4.0 (Auth scaffold)
  - Laravel Sanctum v3.0

---

## Database Schema

### 1. Table: `kategoris` (NEW)
```sql
CREATE TABLE kategoris (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    kode VARCHAR(255) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);
```

### 2. Pivot Table: `kategori_master_item` (NEW)
```sql
CREATE TABLE kategori_master_item (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    kategori_id BIGINT UNSIGNED NOT NULL,
    master_item_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (kategori_id) REFERENCES kategoris(id) ON DELETE CASCADE,
    FOREIGN KEY (master_item_id) REFERENCES master_items(id) ON DELETE CASCADE
);
```

### 3. Table: `master_items` (EXISTING - NO CHANGE)
```sql
-- Sudah ada: id, kode, nama, harga_beli, laba, supplier, jenis, foto, timestamps, deleted_at
-- Field 'foto' sudah ditambahkan via migration 2026_11_03_052414
```

---

## Features & Requirements

### ✅ TASK 1: Upload Foto di Master Items
**Status**: SUDAH SELESAI (sudah ada di code)
- Migration `2026_11_03_052414_add_foto_to_master_items_table.php` ✓
- Controller upload logic ✓
- Model fillable + accessor `getFotoUrlAttribute()` ✓

**Action**: SKIP (sudah complete)

---

### 🔴 TASK 2: Fix Bug Filter Harga Min/Max

**Bug**: Filter harga min dan harga max di halaman index Master Items tidak bekerja dengan benar.

**Current Implementation** (`MasterItemsController@search`):
```php
if ($request->filled('hargamin')) {
    $data_search = $data_search->where('harga_beli', '>=', $hargamin);
}
if ($request->filled('hargamax')) {
    $data_search = $data_search->where('harga_beli', '<=', $hargamax);
}
```

**Expected Fix**:
- Pastikan parameter `hargamin` dan `hargamax` di-cast ke integer
- Validasi input numerik
- Test case: filter harga 100-500000 harus return item dalam range tersebut

**Acceptance Criteria**:
- [ ] Filter hargamin/hargamax berfungsi (exact match & range)
- [ ] Kombinasi filter (kode + nama + harga) bekerja bersamaan
- [ ] Response JSON status 200 + data array

---

### 🔴 TASK 3: CRUD Kategori Items (Many-to-Many)

#### 3.1 Database Setup
**Files to Create**:
1. `database/migrations/2026_11_03_XXXXXX_create_kategoris_table.php`
2. `database/migrations/2026_11_03_XXXXXX_create_kategori_master_item_table.php`

**Migration Run**:
```bash
php artisan migrate
```

#### 3.2 Model Setup
**File**: `app/Models/Kategori.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['kode', 'nama'];

    public function masterItems()
    {
        return $this->belongsToMany(MasterItem::class, 'kategori_master_item');
    }
}
```

**Update**: `app/Models/MasterItem.php`
```php
public function kategoris()
{
    return $this->belongsToMany(Kategori::class, 'kategori_master_item');
}
```

#### 3.3 Routes
**File**: `routes/web.php`
```php
// Kategori Routes
Route::get('/kategoris', [App\Http\Controllers\KategorisController::class, 'index']);
Route::get('/kategoris/search', [App\Http\Controllers\KategorisController::class, 'search']);
Route::get('/kategoris/form/{method}/{id?}', [App\Http\Controllers\KategorisController::class, 'formView']);
Route::post('/kategoris/form/{method}/{id?}', [App\Http\Controllers\KategorisController::class, 'formSubmit']);
Route::get('/kategoris/view/{kode}', [App\Http\Controllers\KategorisController::class, 'singleView']);
Route::get('/kategoris/delete/{id}', [App\Http\Controllers\KategorisController::class, 'delete']);
Route::get('/kategoris/export-pdf/{id}', [App\Http\Controllers\KategorisController::class, 'exportPdf']);
```

#### 3.4 Controller
**File**: `app/Http/Controllers/KategorisController.php`

**Methods Required**:
1. `index()` - Tampil halaman list
2. `search(Request $request)` - API endpoint untuk DataTable
   - Filter: `kode`, `nama`
   - Return: JSON `{status: 200, data: [...]}`
3. `formView($method, $id = 0)` - Form create/edit
4. `formSubmit(Request $request, $method, $id = 0)` - Submit form
   - Validation: kode required, nama required
   - Auto-generate kode jika perlu (seperti MasterItem)
5. `singleView($kode)` - Detail single kategori
   - Eager load: `$kategori->load('masterItems')`
   - Pass data items ke view
6. `delete($id)` - Soft delete
7. `exportPdf($id)` - Export PDF kategori (TASK 4)

#### 3.5 Views Structure
**Directory**: `resources/views/kategoris/`
```
kategoris/
├── index/
│   ├── index.blade.php     (main page)
│   ├── filter.blade.php    (form filter kode & nama)
│   ├── table.blade.php     (DataTable markup)
│   └── js.blade.php        (Ajax logic)
├── form/
│   ├── index.blade.php     (wrapper)
│   └── form.blade.php      (form fields)
└── single/
    └── index.blade.php     (detail view + items table + PDF button)
```

**UI Reference**: Copy struktur dari `resources/views/master_items/` untuk consistency

#### 3.6 Update Master Items CRUD
**File**: `resources/views/master_items/form/form.blade.php`

**Tambahkan Input Kategori** (multi-select):
```html
<div class="form-group">
    <label for="kategoris">Kategori</label>
    <select name="kategoris[]" id="kategoris" class="form-control" multiple>
        @foreach($kategoris as $kategori)
            <option value="{{ $kategori->id }}" 
                {{ isset($item->kategoris) && $item->kategoris->contains($kategori->id) ? 'selected' : '' }}>
                {{ $kategori->nama }} ({{ $kategori->kode }})
            </option>
        @endforeach
    </select>
</div>
```

**Update Controller**:
- `formView()`: pass `$kategoris = Kategori::all()` ke view
- `formSubmit()`: sync kategoris
  ```php
  if ($request->has('kategoris')) {
      $data_item->kategoris()->sync($request->kategoris);
  }
  ```

#### 3.7 Navbar Links
**File**: `resources/views/layouts/app.blade.php`

**Tambahkan di navbar**:
```html
<li class="nav-item">
    <a class="nav-link" href="{{ url('/master-items') }}">Master Items</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ url('/kategoris') }}">Kategori</a>
</li>
```

**Acceptance Criteria TASK 3**:
- [ ] Table kategoris + pivot table created
- [ ] CRUD Kategori lengkap (create, read, update, delete)
- [ ] Index kategori: filter kode & nama berfungsi
- [ ] Single view kategori: tampil nama, kode, list items
- [ ] Master Items form: ada field multi-select kategori
- [ ] Relasi many-to-many berfungsi (save & load)
- [ ] Navbar link Kategori & Master Items aktif
- [ ] UI consistent dengan Master Items

---

### 🔴 TASK 4: Export PDF Detail Kategori

**Package**: `barryvdh/laravel-dompdf` (sudah installed)

**Controller Method**: `KategorisController@exportPdf($id)`

**Implementation**:
```php
use Barryvdh\DomPDF\Facade\Pdf;

public function exportPdf($id)
{
    $kategori = Kategori::with('masterItems')->findOrFail($id);
    
    $data = [
        'kategori' => $kategori,
        'items' => $kategori->masterItems,
        'tanggal_cetak' => now()->format('d/m/Y H:i:s')
    ];
    
    $pdf = Pdf::loadView('kategoris.pdf.detail', $data);
    
    return $pdf->download('kategori_' . $kategori->kode . '_' . now()->format('YmdHis') . '.pdf');
}
```

**View**: `resources/views/kategoris/pdf/detail.blade.php`

**Content Required**:
1. **Header**: Nama Kategori + Kode Kategori
2. **Table Items**:
   - Columns: No, Kode Item, Nama Item, Harga Beli, Laba, Supplier, Jenis
   - Loop `$items`
3. **Footer**: "Dicetak pada: {{ $tanggal_cetak }}"

**PDF Styling**:
```html
<style>
    body { font-family: Arial, sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .footer { margin-top: 40px; font-size: 10px; color: #666; }
</style>
```

**Button di Single View**:
```html
<a href="{{ url('/kategoris/export-pdf/' . $data->id) }}" class="btn btn-danger">
    <i class="bi bi-file-pdf"></i> Download PDF
</a>
```

**Acceptance Criteria**:
- [ ] PDF berisi nama & kode kategori
- [ ] Table items dengan kategori tersebut (all columns)
- [ ] Footer dengan timestamp cetak
- [ ] Download dengan nama file `kategori_{kode}_{timestamp}.pdf`
- [ ] Styling rapi (table borders, header bold)

---

### 🔴 TASK 5: Export Excel Master Items

**Package**: Gunakan `Maatwebsite/Laravel-Excel` atau native PHP (PhpSpreadsheet)

**Recommended**: Install `maatwebsite/excel`
```bash
composer require maatwebsite/excel
```

**Route**:
```php
Route::get('/master-items/export-excel', [App\Http\Controllers\MasterItemsController::class, 'exportExcel']);
```

**Controller Method**: `MasterItemsController@exportExcel()`

**Implementation** (using Laravel Excel):
```php
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MasterItemsExport;

public function exportExcel()
{
    return Excel::download(new MasterItemsExport, 'master_items_' . now()->format('YmdHis') . '.xlsx');
}
```

**Export Class**: `app/Exports/MasterItemsExport.php`
```php
<?php

namespace App\Exports;

use App\Models\MasterItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MasterItemsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return MasterItem::with('kategoris')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Kategori',
            'Nama Items',
            'Nama Supplier',
            'Harga',
            'Laba',
            'Harga Jual'
        ];
    }

    public function map($item): array
    {
        static $no = 0;
        $no++;
        
        $kategoriNames = $item->kategoris->pluck('nama')->implode(', ');
        $hargaJual = $item->harga_beli + (($item->laba / 100) * $item->harga_beli);
        
        return [
            $no,
            $kategoriNames ?: '-',
            $item->nama,
            $item->supplier,
            $item->harga_beli,
            $item->laba . '%',
            $hargaJual
        ];
    }
}
```

**Button Placement**: Di halaman index Master Items
```html
<a href="{{ url('/master-items/export-excel') }}" class="btn btn-success">
    <i class="bi bi-file-excel"></i> Export Excel
</a>
```

**Columns Exact Order**:
1. No (auto-increment)
2. Nama Kategori (comma-separated jika multiple)
3. Nama Items
4. Nama Supplier
5. Harga (harga_beli)
6. Laba (%)
7. Harga Jual (harga_beli + (laba% × harga_beli))

**Acceptance Criteria**:
- [ ] Excel download berfungsi
- [ ] 7 kolom sesuai spec (No, Kategori, Nama, Supplier, Harga, Laba, Harga Jual)
- [ ] Nama kategori comma-separated jika multiple
- [ ] Harga jual calculated correctly
- [ ] Eager loading kategoris (no N+1 query)
- [ ] Filename format: `master_items_{timestamp}.xlsx`

---

## Implementation Checklist

### Phase 1: Bug Fix & Setup
- [ ] Fix bug filter harga min/max di MasterItemsController
- [ ] Test filter: kode, nama, harga (individual & combined)
- [ ] Install `maatwebsite/excel` package

### Phase 2: Database & Models
- [ ] Create migration `create_kategoris_table`
- [ ] Create migration `create_kategori_master_item_table`
- [ ] Run migrations
- [ ] Create Model `Kategori` dengan relasi
- [ ] Update Model `MasterItem` tambah relasi kategoris

### Phase 3: Kategori CRUD
- [ ] Create `KategorisController`
- [ ] Create routes kategori (7 routes)
- [ ] Create views: index/filter/table/js
- [ ] Create views: form (create/edit)
- [ ] Create views: single (detail view)
- [ ] Test CRUD: create, read, update, delete kategori

### Phase 4: Integration Master Items ↔ Kategori
- [ ] Update `MasterItemsController@formView`: pass kategoris
- [ ] Update form Master Items: tambah multi-select kategori
- [ ] Update `MasterItemsController@formSubmit`: sync kategoris
- [ ] Test: assign kategori ke item, edit, remove

### Phase 5: Navbar & UI
- [ ] Update navbar: tambah link Master Items & Kategori
- [ ] Verify UI consistency (Bootstrap classes sama)
- [ ] Test responsive layout

### Phase 6: Export PDF
- [ ] Create view `kategoris/pdf/detail.blade.php`
- [ ] Implement `KategorisController@exportPdf`
- [ ] Test: PDF download dengan data lengkap + footer timestamp
- [ ] Tambahkan button PDF di single view kategori

### Phase 7: Export Excel
- [ ] Create `MasterItemsExport` class
- [ ] Implement `MasterItemsController@exportExcel`
- [ ] Test: Excel 7 kolom, harga jual correct, kategori comma-separated
- [ ] Tambahkan button Excel di index Master Items

### Phase 8: Final Testing
- [ ] Test all filters (kategori & master items)
- [ ] Test relasi many-to-many (assign, edit, delete cascade)
- [ ] Test PDF: layout, data, timestamp
- [ ] Test Excel: kolom, data, formula harga jual
- [ ] Test navbar navigation
- [ ] Check console errors (browser DevTools)
- [ ] Verify eloquent + eager loading (check query count)

---

## Notes & Best Practices

### 1. Eloquent + Eager Loading
**Required di soal**: Gunakan eloquent dan eager loading.

**Good**:
```php
$kategori = Kategori::with('masterItems')->find($id);
$items = MasterItem::with('kategoris')->get();
```

**Bad** (N+1 problem):
```php
$kategori = Kategori::find($id);
foreach ($kategori->masterItems as $item) { ... } // N+1 query
```

### 2. UI Consistency
**Reference**: `resources/views/master_items/`
- Bootstrap 5 classes sama
- Button style sama (btn-primary, btn-success, btn-danger)
- Form layout sama (form-group, form-control)
- Table markup sama (table table-bordered table-hover)

### 3. Code Generation Pattern
**Auto Kode Kategori** (seperti MasterItem):
```php
if ($method == 'new') {
    $kode = Kategori::count('id') + 1;
    $kode = 'KAT-' . str_pad($kode, 5, '0', STR_PAD_LEFT);
}
```

### 4. Validation
```php
$request->validate([
    'nama' => 'required|string|max:255',
    'kode' => 'required|string|max:255|unique:kategoris,kode,' . $id,
    'kategoris' => 'nullable|array',
    'kategoris.*' => 'exists:kategoris,id'
]);
```

### 5. Soft Deletes
Semua model gunakan `SoftDeletes` trait (existing pattern).

---

## Testing Scenarios

### Filter Test (Master Items)
1. Filter hargamin=100 → return items >= 100
2. Filter hargamax=500000 → return items <= 500000
3. Filter hargamin=100&hargamax=500000 → return items 100-500000
4. Kombinasi: kode + nama + harga

### Relasi Test (Kategori ↔ Items)
1. Create kategori → assign ke 3 items → verify di single view
2. Edit item → change kategori → verify updated
3. Delete kategori → items tetap ada (only pivot deleted)
4. Delete item → pivot record juga deleted (cascade)

### Export Test
1. PDF: kategori dengan 10 items → check table complete
2. Excel: 50 items with multiple kategoris → check comma-separated
3. Harga jual calculation: harga=100000, laba=20 → jual=120000

---

## File Structure (Final)

```
D:\tes-coding/
├── app/
│   ├── Http/Controllers/
│   │   ├── MasterItemsController.php (UPDATED)
│   │   └── KategorisController.php (NEW)
│   ├── Models/
│   │   ├── MasterItem.php (UPDATED - tambah relasi)
│   │   └── Kategori.php (NEW)
│   └── Exports/
│       └── MasterItemsExport.php (NEW)
├── database/migrations/
│   ├── 2026_11_03_XXXXXX_create_kategoris_table.php (NEW)
│   └── 2026_11_03_XXXXXX_create_kategori_master_item_table.php (NEW)
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php (UPDATED - navbar)
│   ├── master_items/
│   │   └── form/form.blade.php (UPDATED - tambah select kategori)
│   └── kategoris/ (NEW)
│       ├── index/
│       │   ├── index.blade.php
│       │   ├── filter.blade.php
│       │   ├── table.blade.php
│       │   └── js.blade.php
│       ├── form/
│       │   ├── index.blade.php
│       │   └── form.blade.php
│       ├── single/
│       │   └── index.blade.php
│       └── pdf/
│           └── detail.blade.php
└── routes/
    └── web.php (UPDATED)
```

---

## Deliverables

1. ✅ **Code**: All files above implemented
2. ✅ **Database**: Migrations run, data seeded (test data)
3. ✅ **Testing**: All acceptance criteria passed
4. ✅ **Documentation**: README update (optional, nice to have)

---

## Timeline Estimate

| Phase | Task | Est. Time |
|-------|------|-----------|
| 1 | Bug fix filter + package install | 15 min |
| 2 | Database & Models | 20 min |
| 3 | Kategori CRUD (controller + views) | 45 min |
| 4 | Integration Master Items ↔ Kategori | 20 min |
| 5 | Navbar & UI | 10 min |
| 6 | Export PDF | 25 min |
| 7 | Export Excel | 25 min |
| 8 | Testing & Debugging | 30 min |
| **TOTAL** | | **~3 hours** |

---

## Quick Start Commands

```bash
# 1. Navigate to project
cd /d/tes-coding

# 2. Install package
composer require maatwebsite/excel

# 3. Create migrations
php artisan make:migration create_kategoris_table
php artisan make:migration create_kategori_master_item_table

# 4. Create model
php artisan make:model Kategori

# 5. Create controller
php artisan make:controller KategorisController

# 6. Create export class
php artisan make:export MasterItemsExport --model=MasterItem

# 7. Run migrations
php artisan migrate

# 8. Start dev server (if needed)
php artisan serve
npm run dev
```

---

**END OF PRD**
