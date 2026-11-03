<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKategoriRequest;
use App\Http\Requests\UpdateKategoriRequest;
use App\Models\Kategori;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategorisController extends Controller
{
    /**
     * Display a listing of kategoris.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('kategoris.index.index');
    }

    /**
     * Search kategoris with filters (AJAX endpoint).
     *
     * @param  Request $request
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

        $data = $query->withCount('masterItems')
            ->select('id', 'kode', 'nama', 'created_at', 'updated_at')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new kategori.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('kategoris.form.index', [
            'kategori' => (object) [],
            'method' => 'create',
        ]);
    }

    /**
     * Store a newly created kategori in storage.
     *
     * @param  StoreKategoriRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreKategoriRequest $request)
    {
        DB::beginTransaction();

        try {
            $kode = $request->filled('kode') ? $request->kode : $this->generateKode();

            $kategori = new Kategori();
            $kategori->kode = $kode;
            $kategori->nama = $request->nama;
            $kategori->save();

            DB::commit();

            return redirect()->route('kategoris.index')
                ->with('success', 'Kategori berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Gagal menyimpan kategori: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified kategori.
     *
     * @param  string $kode
     * @return \Illuminate\View\View
     */
    public function show($kode)
    {
        $kategori = Kategori::with('masterItems')->where('kode', $kode)->firstOrFail();
        return view('kategoris.single.index', ['data' => $kategori]);
    }

    /**
     * Show the form for editing the specified kategori.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id);

        return view('kategoris.form.index', [
            'kategori' => $kategori,
            'method' => 'edit',
        ]);
    }

    /**
     * Update the specified kategori in storage.
     *
     * @param  UpdateKategoriRequest $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateKategoriRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $kategori = Kategori::findOrFail($id);

            $kategori->nama = $request->nama;
            if ($request->filled('kode')) {
                $kategori->kode = $request->kode;
            }
            $kategori->save();

            DB::commit();

            return redirect()->route('kategoris.index')
                ->with('success', 'Kategori berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Gagal update kategori: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified kategori from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $kategori = Kategori::findOrFail($id);
            $kategori->delete();

            return redirect()->route('kategoris.index')
                ->with('success', 'Kategori berhasil dihapus');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Gagal menghapus kategori: ' . $e->getMessage()]);
        }
    }

    /**
     * Export kategori detail to PDF.
     *
     * @param  int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportPdf($id)
    {
        $kategori = Kategori::with('masterItems')->findOrFail($id);

        $data = [
            'kategori' => $kategori,
            'items' => $kategori->masterItems,
            'tanggal_cetak' => now()->format('d/m/Y H:i:s'),
        ];

        $pdf = Pdf::loadView('kategoris.pdf.detail', $data);

        return $pdf->download('kategori_' . $kategori->kode . '_' . now()->format('YmdHis') . '.pdf');
    }

    /**
     * Generate unique sequential kode for new kategori.
     *
     * @return string
     */
    private function generateKode()
    {
        $lastKategori = Kategori::withTrashed()
            ->orderBy('id', 'desc')
            ->first();

        if ($lastKategori) {
            $num = preg_replace('/[^0-9]/', '', $lastKategori->kode);
            $nextNumber = $num ? ((int) $num + 1) : ($lastKategori->id + 1);
        } else {
            $nextNumber = 1;
        }

        return 'KAT-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
