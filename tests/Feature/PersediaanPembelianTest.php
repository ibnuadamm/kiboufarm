<?php

namespace Tests\Feature;

use App\Models\Pembelian;
use App\Models\Persediaan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PersediaanPembelianTest extends TestCase
{
    use RefreshDatabase;

    public function test_persediaan_can_be_created()
    {
        $this->seed([\Database\Seeders\CoaSeeder::class, \Database\Seeders\ModalSeeder::class]);
        $user = \App\Models\User::factory()->create();
        $this->withoutExceptionHandling();
        $response = $this->actingAs($user)->post(route('persediaan.store'), [
            'nama_barang' => 'Pakan Lele A1',
            'kategori' => 'Pakan',
            'satuan' => 'Sak',
        ]);

        $response->assertRedirect(route('persediaan.index'));
        $this->assertDatabaseHas('persediaans', ['nama_barang' => 'Pakan Lele A1']);
    }

    public function test_pembelian_updates_persediaan_stock()
    {
        $this->seed([\Database\Seeders\CoaSeeder::class, \Database\Seeders\ModalSeeder::class]);
        $user = \App\Models\User::factory()->create();
        $vendor = \App\Models\Vendor::create(['nama' => 'Test Vendor']);
        // 1. Create Persediaan
        $persediaan = Persediaan::create([
            'nama_barang' => 'Pakan Lele A1',
            'kategori' => 'Pakan',
            'satuan' => 'Sak',
            'stok_masuk' => 0,
            'stok_keluar' => 0,
            'stok_sisa' => 0,
        ]);

        $this->assertEquals(0, $persediaan->stok_sisa);

        // 2. Create Pembelian via POST request to controller (to test full flow) or direct model create
        // Let's test controller flow
        $response = $this->actingAs($user)->post(route('pembelian.store'), [
            'persediaan_id' => $persediaan->id,
            'vendor_id' => $vendor->id,
            'jumlah' => 10,
            'harga_satuan' => 200000,
            'tanggal_pembelian' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('pembelian.index'));

        // 3. Check Database
        $persediaan->refresh();
        
        $this->assertEquals(10, $persediaan->stok_masuk);
        $this->assertEquals(10, $persediaan->stok_sisa);
        $this->assertDatabaseHas('pembelians', [
            'persediaan_id' => $persediaan->id,
            'vendor_id' => $vendor->id,
            'jumlah' => 10,
            'total_harga' => 2000000,
        ]);

        // Verifikasi Jurnal Otomatis
        // Pastikan COA ada dulu (harus di-seed dalam test atau create manual)
        // Karena test pakai RefreshDatabase, seed hilang. Kita harus seed di test atau setUp.
        
        $this->assertDatabaseHas('jurnals', [
            'ref_type' => Pembelian::class,
            // 'ref_id' => ... (dynamic)
        ]);

        // 4. Add another purchase
         $this->actingAs($user)->post(route('pembelian.store'), [
            'persediaan_id' => $persediaan->id,
            'vendor_id' => $vendor->id,
            'jumlah' => 5,
            'harga_satuan' => 210000,
            'tanggal_pembelian' => now()->toDateString(),
        ]);

        $persediaan->refresh();
        $this->assertEquals(15, $persediaan->stok_masuk);
        $this->assertEquals(15, $persediaan->stok_sisa);
    }
}
