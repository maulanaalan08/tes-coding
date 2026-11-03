<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\MasterItem;
use Tests\TestCase;

class KategoriTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('kategori_master_item')->truncate();
        Kategori::truncate();
        MasterItem::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function test_can_view_kategoris_index()
    {
        $response = $this->get('/kategoris');
        $response->assertStatus(200);
        $response->assertSee('Daftar Kategori');
    }

    public function test_can_create_kategori_with_auto_generated_code()
    {
        $response = $this->post(route('kategoris.store'), [
            'nama' => 'Obat Generik',
        ]);

        $response->assertRedirect(route('kategoris.index'));

        $this->assertDatabaseHas('kategoris', [
            'nama' => 'Obat Generik',
            'kode' => 'KAT-00001',
        ]);
    }

    public function test_can_create_kategori_with_custom_code()
    {
        $response = $this->post(route('kategoris.store'), [
            'kode' => 'CUSTOM-99',
            'nama' => 'Alkes Medis',
        ]);

        $response->assertRedirect(route('kategoris.index'));

        $this->assertDatabaseHas('kategoris', [
            'nama' => 'Alkes Medis',
            'kode' => 'CUSTOM-99',
        ]);
    }

    public function test_search_kategoris_api()
    {
        Kategori::create(['kode' => 'KAT-00001', 'nama' => 'Obat Sirup']);
        Kategori::create(['kode' => 'KAT-00002', 'nama' => 'Obat Tablet']);
        Kategori::create(['kode' => 'KAT-00003', 'nama' => 'Alat Bedah']);

        // Search by nama
        $res = $this->getJson('/kategoris/search?nama=Obat');
        $res->assertStatus(200);
        $this->assertCount(2, $res->json('data'));

        // Search by kode
        $res2 = $this->getJson('/kategoris/search?kode=KAT-00003');
        $res2->assertStatus(200);
        $this->assertCount(1, $res2->json('data'));
        $this->assertEquals('Alat Bedah', $res2->json('data.0.nama'));
    }

    public function test_can_view_single_kategori_with_items()
    {
        $kategori = Kategori::create(['kode' => 'KAT-00001', 'nama' => 'Farmasi']);
        $item = MasterItem::create([
            'kode' => '00001',
            'nama' => 'Paracetamol',
            'harga_beli' => 5000,
            'laba' => 20,
            'supplier' => 'Tokopaedi',
            'jenis' => 'Obat'
        ]);

        $kategori->masterItems()->attach($item->id);

        $response = $this->get('/kategoris/view/' . $kategori->kode);
        $response->assertStatus(200);
        $response->assertSee('Farmasi');
        $response->assertSee('Paracetamol');
        $response->assertSee('Download PDF');
    }

    public function test_can_edit_kategori_via_put()
    {
        $kategori = Kategori::create(['kode' => 'KAT-00001', 'nama' => 'Nama Awal']);

        $response = $this->put(route('kategoris.update', $kategori->id), [
            'kode' => 'KAT-00001',
            'nama' => 'Nama Baru'
        ]);

        $response->assertRedirect(route('kategoris.index'));
        $this->assertDatabaseHas('kategoris', [
            'id' => $kategori->id,
            'nama' => 'Nama Baru'
        ]);
    }

    public function test_can_delete_kategori_via_delete_method()
    {
        $kategori = Kategori::create(['kode' => 'KAT-00001', 'nama' => 'Kategori Hapus']);

        $response = $this->delete(route('kategoris.destroy', $kategori->id));
        $response->assertRedirect(route('kategoris.index'));

        $this->assertSoftDeleted('kategoris', ['id' => $kategori->id]);
    }

    public function test_get_delete_kategori_does_not_exist()
    {
        $response = $this->get('/kategoris/delete/1');
        $response->assertStatus(404);
    }

    public function test_can_export_pdf_detail_kategori()
    {
        $kategori = Kategori::create(['kode' => 'KAT-00001', 'nama' => 'Kategori PDF']);
        $item = MasterItem::create([
            'kode' => '00001',
            'nama' => 'Amoxicillin 500mg',
            'harga_beli' => 10000,
            'laba' => 20,
            'supplier' => 'Tokopaedi',
            'jenis' => 'Obat'
        ]);
        $kategori->masterItems()->attach($item->id);

        $response = $this->get('/kategoris/export-pdf/' . $kategori->id);
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_master_item_many_to_many_kategori_relation()
    {
        $kat1 = Kategori::create(['kode' => 'KAT-00001', 'nama' => 'Kategori 1']);
        $kat2 = Kategori::create(['kode' => 'KAT-00002', 'nama' => 'Kategori 2']);

        // Create master item with 2 categories
        $response = $this->post(route('master-items.store'), [
            'nama' => 'Item Multi Kategori',
            'harga_beli' => 25000,
            'laba' => 10,
            'supplier' => 'Tokopaedi',
            'jenis' => 'Obat',
            'kategoris' => [$kat1->id, $kat2->id]
        ]);

        $response->assertRedirect(route('master-items.index'));

        $item = MasterItem::where('nama', 'Item Multi Kategori')->first();
        $this->assertNotNull($item);
        $this->assertCount(2, $item->kategoris);

        // Edit and sync to 1 category
        $this->put(route('master-items.update', $item->id), [
            'nama' => 'Item Multi Kategori Edited',
            'harga_beli' => 25000,
            'laba' => 10,
            'supplier' => 'Tokopaedi',
            'jenis' => 'Obat',
            'kategoris' => [$kat1->id]
        ]);

        $item->refresh();
        $this->assertCount(1, $item->kategoris);
        $this->assertEquals($kat1->id, $item->kategoris->first()->id);
    }

    public function test_can_export_excel_master_items()
    {
        $kat = Kategori::create(['kode' => 'KAT-00001', 'nama' => 'Farmasi']);
        $item = MasterItem::create([
            'kode' => '00001',
            'nama' => 'Paracetamol',
            'harga_beli' => 10000,
            'laba' => 20,
            'supplier' => 'Tokopaedi',
            'jenis' => 'Obat'
        ]);
        $item->kategoris()->attach($kat->id);

        $response = $this->get('/master-items/export-excel');
        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
    }
}
