<?php

namespace Tests\Feature;

use App\Models\MasterItem;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class MasterItemTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('kategori_master_item')->truncate();
        MasterItem::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function test_can_view_master_items_index()
    {
        $response = $this->get('/master-items');
        $response->assertStatus(200);
        $response->assertSee('Daftar Master Items');
        $response->assertSee('Foto');
    }

    public function test_can_create_master_item_with_image_upload()
    {
        $fakeImage = UploadedFile::fake()->image('test_product.jpg', 200, 200);

        $response = $this->post(route('master-items.store'), [
            'nama' => 'Paracetamol 500mg',
            'harga_beli' => 5000,
            'laba' => 20,
            'supplier' => 'Tokopaedi',
            'jenis' => 'Obat',
            'foto' => $fakeImage,
        ]);

        $response->assertRedirect(route('master-items.index'));

        $this->assertDatabaseHas('master_items', [
            'nama' => 'Paracetamol 500mg',
            'harga_beli' => 5000,
            'laba' => 20,
            'supplier' => 'Tokopaedi',
            'jenis' => 'Obat',
        ]);

        $item = MasterItem::first();
        $this->assertNotNull($item->foto);
        $this->assertFileExists(public_path('uploads/' . $item->foto));

        // Clean up uploaded file
        if (file_exists(public_path('uploads/' . $item->foto))) {
            @unlink(public_path('uploads/' . $item->foto));
        }
    }

    public function test_can_create_master_item_without_image()
    {
        $response = $this->post(route('master-items.store'), [
            'nama' => 'Masker Medis',
            'harga_beli' => 15000,
            'laba' => 15,
            'supplier' => 'Bukulapuk',
            'jenis' => 'Alkes',
        ]);

        $response->assertRedirect(route('master-items.index'));

        $this->assertDatabaseHas('master_items', [
            'nama' => 'Masker Medis',
            'foto' => null,
        ]);
    }

    public function test_can_view_single_master_item()
    {
        $item = MasterItem::create([
            'kode' => '00001',
            'nama' => 'Amoxicillin',
            'harga_beli' => 10000,
            'laba' => 25,
            'supplier' => 'TokoBagas',
            'jenis' => 'Obat',
            'foto' => null,
        ]);

        $response = $this->get('/master-items/view/' . $item->kode);
        $response->assertStatus(200);
        $response->assertSee('Amoxicillin');
        $response->assertSee('TokoBagas');
        $response->assertSee('Foto');
    }

    public function test_can_edit_master_item_and_replace_image()
    {
        $oldImage = UploadedFile::fake()->image('old.jpg');
        $this->post(route('master-items.store'), [
            'nama' => 'Item Lama',
            'harga_beli' => 1000,
            'laba' => 10,
            'supplier' => 'Blublu',
            'jenis' => 'Umum',
            'foto' => $oldImage,
        ]);

        $item = MasterItem::first();
        $oldFilename = $item->foto;
        $this->assertFileExists(public_path('uploads/' . $oldFilename));

        $newImage = UploadedFile::fake()->image('new.jpg');
        $response = $this->put(route('master-items.update', $item->id), [
            'nama' => 'Item Baru',
            'harga_beli' => 2000,
            'laba' => 15,
            'supplier' => 'Blublu',
            'jenis' => 'Umum',
            'foto' => $newImage,
        ]);

        $response->assertRedirect(route('master-items.index'));

        $item->refresh();
        $this->assertEquals('Item Baru', $item->nama);
        $this->assertNotEquals($oldFilename, $item->foto);
        $this->assertFileDoesNotExist(public_path('uploads/' . $oldFilename));
        $this->assertFileExists(public_path('uploads/' . $item->foto));

        // Clean up
        if (file_exists(public_path('uploads/' . $item->foto))) {
            @unlink(public_path('uploads/' . $item->foto));
        }
    }

    public function test_can_delete_master_item_via_delete_method()
    {
        $item = MasterItem::create([
            'kode' => '00001',
            'nama' => 'Item Delete',
            'harga_beli' => 5000,
            'laba' => 10,
            'supplier' => 'Tokopaedi',
            'jenis' => 'ATK',
        ]);

        $response = $this->delete(route('master-items.destroy', $item->id));
        $response->assertRedirect(route('master-items.index'));

        $this->assertSoftDeleted('master_items', ['id' => $item->id]);
    }

    public function test_get_delete_route_does_not_exist()
    {
        $response = $this->get('/master-items/delete/1');
        $response->assertStatus(404);
    }

    public function test_filter_harga_min_only()
    {
        MasterItem::create(['kode' => '00001', 'nama' => 'Murah', 'harga_beli' => 1000, 'laba' => 10, 'supplier' => 'A', 'jenis' => 'Umum']);
        MasterItem::create(['kode' => '00002', 'nama' => 'Sedang', 'harga_beli' => 5000, 'laba' => 10, 'supplier' => 'A', 'jenis' => 'Umum']);
        MasterItem::create(['kode' => '00003', 'nama' => 'Mahal', 'harga_beli' => 10000, 'laba' => 10, 'supplier' => 'A', 'jenis' => 'Umum']);

        $response = $this->getJson('/master-items/search?hargamin=5000');
        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(2, $data);
        $this->assertEquals(['Sedang', 'Mahal'], array_column($data, 'nama'));
    }

    public function test_filter_harga_max_only()
    {
        MasterItem::create(['kode' => '00001', 'nama' => 'Murah', 'harga_beli' => 1000, 'laba' => 10, 'supplier' => 'A', 'jenis' => 'Umum']);
        MasterItem::create(['kode' => '00002', 'nama' => 'Sedang', 'harga_beli' => 5000, 'laba' => 10, 'supplier' => 'A', 'jenis' => 'Umum']);
        MasterItem::create(['kode' => '00003', 'nama' => 'Mahal', 'harga_beli' => 10000, 'laba' => 10, 'supplier' => 'A', 'jenis' => 'Umum']);

        $response = $this->getJson('/master-items/search?hargamax=5000');
        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(2, $data);
        $this->assertEquals(['Murah', 'Sedang'], array_column($data, 'nama'));
    }

    public function test_filter_harga_min_and_max()
    {
        MasterItem::create(['kode' => '00001', 'nama' => 'Murah', 'harga_beli' => 1000, 'laba' => 10, 'supplier' => 'A', 'jenis' => 'Umum']);
        MasterItem::create(['kode' => '00002', 'nama' => 'Sedang', 'harga_beli' => 5000, 'laba' => 10, 'supplier' => 'A', 'jenis' => 'Umum']);
        MasterItem::create(['kode' => '00003', 'nama' => 'Mahal', 'harga_beli' => 10000, 'laba' => 10, 'supplier' => 'A', 'jenis' => 'Umum']);

        $response = $this->getJson('/master-items/search?hargamin=2000&hargamax=8000');
        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Sedang', $data[0]['nama']);
    }

    public function test_filter_harga_min_zero()
    {
        MasterItem::create(['kode' => '00001', 'nama' => 'Gratis', 'harga_beli' => 0, 'laba' => 10, 'supplier' => 'A', 'jenis' => 'Umum']);
        MasterItem::create(['kode' => '00002', 'nama' => 'Sedang', 'harga_beli' => 5000, 'laba' => 10, 'supplier' => 'A', 'jenis' => 'Umum']);

        $response = $this->getJson('/master-items/search?hargamin=0&hargamax=1000');
        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Gratis', $data[0]['nama']);
    }

    public function test_filter_all_empty_returns_all()
    {
        MasterItem::create(['kode' => '00001', 'nama' => 'Item 1', 'harga_beli' => 1000, 'laba' => 10, 'supplier' => 'A', 'jenis' => 'Umum']);
        MasterItem::create(['kode' => '00002', 'nama' => 'Item 2', 'harga_beli' => 2000, 'laba' => 10, 'supplier' => 'A', 'jenis' => 'Umum']);

        $response = $this->getJson('/master-items/search?kode=&nama=&hargamin=&hargamax=');
        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(2, $data);
    }

    public function test_auto_generate_kode_sequential()
    {
        $this->post(route('master-items.store'), [
            'nama' => 'Item 1',
            'harga_beli' => 1000,
            'laba' => 10,
            'supplier' => 'Tokopaedi',
            'jenis' => 'Obat',
        ]);

        $this->post(route('master-items.store'), [
            'nama' => 'Item 2',
            'harga_beli' => 2000,
            'laba' => 15,
            'supplier' => 'Tokopaedi',
            'jenis' => 'Obat',
        ]);

        $items = MasterItem::orderBy('id')->get();
        $this->assertEquals('00001', $items[0]->kode);
        $this->assertEquals('00002', $items[1]->kode);
    }
}
