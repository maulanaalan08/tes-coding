<?php

namespace App\Http\Controllers;

use App\Exports\MasterItemsExport;
use App\Http\Requests\StoreMasterItemRequest;
use App\Http\Requests\UpdateMasterItemRequest;
use App\Models\Kategori;
use App\Models\MasterItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MasterItemsController extends Controller
{
    /**
     * Display a listing of master items.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('master_items.index.index');
    }

    /**
     * Search master items with filters (AJAX endpoint).
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = MasterItem::query();

        // Apply filters
        if ($request->filled('kode')) {
            $query->where('kode', $request->kode);
        }

        if ($request->filled('nama')) {
            $query->where('nama', 'LIKE', '%' . $request->nama . '%');
        }

        if ($request->filled('hargamin') && is_numeric($request->hargamin)) {
            $query->where('harga_beli', '>=', (int) $request->hargamin);
        }

        if ($request->filled('hargamax') && is_numeric($request->hargamax)) {
            $query->where('harga_beli', '<=', (int) $request->hargamax);
        }

        // Eager load kategoris to prevent N+1 queries
        $items = $query->with('kategoris:id,nama,kode')
            ->select('id', 'kode', 'nama', 'foto', 'jenis', 'harga_beli', 'laba', 'supplier')
            ->orderBy('id')
            ->get();

        // Transform data
        $data = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'kode' => $item->kode,
                'nama' => $item->nama,
                'foto' => $item->foto,
                'foto_url' => $item->foto_url,
                'jenis' => $item->jenis,
                'harga_beli' => $item->harga_beli,
                'laba' => $item->laba,
                'supplier' => $item->supplier,
                'kategoris' => $item->kategoris ? $item->kategoris->pluck('nama')->implode(', ') : '',
            ];
        });

        return response()->json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new master item.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $kategoris = Kategori::orderBy('nama')->get();

        return view('master_items.form.index', [
            'item' => (object) [],
            'method' => 'create',
            'kategoris' => $kategoris,
        ]);
    }

    /**
     * Store a newly created master item in storage.
     *
     * @param  StoreMasterItemRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreMasterItemRequest $request)
    {
        DB::beginTransaction();

        try {
            $kode = $this->generateKode();

            $item = new MasterItem();
            $item->kode = $kode;
            $item->nama = $request->nama;
            $item->harga_beli = $request->harga_beli;
            $item->laba = $request->laba;
            $item->supplier = $request->supplier;
            $item->jenis = $request->jenis;

            if ($request->hasFile('foto')) {
                $item->foto = $this->handleFileUpload($request->file('foto'));
            }

            $item->save();

            if ($request->has('kategoris')) {
                $item->kategoris()->sync($request->kategoris);
            }

            DB::commit();

            return redirect()->route('master-items.index')
                ->with('success', 'Master item berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($item->foto)) {
                $this->deleteFile($item->foto);
            }

            return back()
                ->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified master item.
     *
     * @param  string $kode
     * @return \Illuminate\View\View
     */
    public function show($kode)
    {
        $item = MasterItem::with('kategoris')
            ->where('kode', $kode)
            ->firstOrFail();

        return view('master_items.single.index', ['data' => $item]);
    }

    /**
     * Show the form for editing the specified master item.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $item = MasterItem::with('kategoris')->findOrFail($id);
        $kategoris = Kategori::orderBy('nama')->get();

        return view('master_items.form.index', [
            'item' => $item,
            'method' => 'edit',
            'kategoris' => $kategoris,
        ]);
    }

    /**
     * Update the specified master item in storage.
     *
     * @param  UpdateMasterItemRequest $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateMasterItemRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $item = MasterItem::findOrFail($id);

            $item->nama = $request->nama;
            $item->harga_beli = $request->harga_beli;
            $item->laba = $request->laba;
            $item->supplier = $request->supplier;
            $item->jenis = $request->jenis;

            if ($request->hasFile('foto')) {
                if ($item->foto) {
                    $this->deleteFile($item->foto);
                }

                $item->foto = $this->handleFileUpload($request->file('foto'));
            }

            $item->save();

            if ($request->has('kategoris')) {
                $item->kategoris()->sync($request->kategoris);
            } else {
                $item->kategoris()->sync([]);
            }

            DB::commit();

            return redirect()->route('master-items.index')
                ->with('success', 'Master item berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Gagal update data: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified master item from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $item = MasterItem::findOrFail($id);
            $item->delete();

            return redirect()->route('master-items.index')
                ->with('success', 'Master item berhasil dihapus');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    /**
     * Export master items to Excel.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel()
    {
        return Excel::download(
            new MasterItemsExport,
            'master_items_' . now()->format('YmdHis') . '.xlsx'
        );
    }

    /**
     * Generate unique sequential kode for new master item.
     *
     * @return string
     */
    private function generateKode()
    {
        $lastItem = MasterItem::withTrashed()
            ->orderBy('id', 'desc')
            ->first();

        if ($lastItem) {
            $lastNumber = (int) $lastItem->kode;
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Handle file upload to public/uploads and storage.
     *
     * @param  \Illuminate\Http\UploadedFile $file
     * @return string
     */
    private function handleFileUpload($file)
    {
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        $destinationPath = public_path('uploads');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        $file->move($destinationPath, $fileName);

        $storagePath = storage_path('app/public/uploads');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0777, true);
        }
        @copy(
            $destinationPath . DIRECTORY_SEPARATOR . $fileName,
            $storagePath . DIRECTORY_SEPARATOR . $fileName
        );

        return $fileName;
    }

    /**
     * Delete uploaded file from public and storage.
     *
     * @param  string $fileName
     * @return void
     */
    private function deleteFile($fileName)
    {
        $publicFile = public_path('uploads' . DIRECTORY_SEPARATOR . basename($fileName));
        if (file_exists($publicFile)) {
            @unlink($publicFile);
        }

        $storageFile = storage_path('app/public/uploads' . DIRECTORY_SEPARATOR . basename($fileName));
        if (file_exists($storageFile)) {
            @unlink($storageFile);
        }
    }

    /**
     * Update random data (utility).
     */
    public function updateRandomData()
    {
        $data = MasterItem::get();
        foreach ($data as $item) {
            $kode = $item->id;
            $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);

            $item->harga_beli = rand(100, 1000000);
            $item->laba = rand(10, 99);
            $item->kode = $kode;
            $item->supplier = $this->getRandomSupplier();
            $item->jenis = $this->getRandomJenis();
            $item->save();
        }
    }

    private function getRandomSupplier()
    {
        $array = ['Tokopaedi', 'Bukulapuk', 'TokoBagas', 'E Commurz', 'Blublu'];
        $random = rand(0, 4);
        return $array[$random];
    }

    private function getRandomJenis()
    {
        $array = ['Obat', 'Alkes', 'Matkes', 'Umum', 'ATK'];
        $random = rand(0, 4);
        return $array[$random];
    }
}
