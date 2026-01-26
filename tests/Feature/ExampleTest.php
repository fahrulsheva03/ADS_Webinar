<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Paket;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_guest_cannot_open_dashboard(): void
    {
        $this->get(route('peserta.dashboard'))->assertRedirect(route('login'));
    }

    public function test_user_can_start_checkout_and_open_payment_page(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'status_akun' => 'aktif',
        ]);

        $event = Event::query()->create([
            'judul' => 'Event Test',
            'deskripsi' => null,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDay()->toDateString(),
            'status' => 'published',
            'created_by' => $user->id,
        ]);

        $paket = Paket::query()->create([
            'event_id' => $event->id,
            'nama_paket' => 'Paket Test',
            'deskripsi' => 'Deskripsi',
            'harga' => 100000,
            'status' => 'aktif',
            'akses_live' => true,
            'akses_rekaman' => false,
            'kuota' => null,
        ]);

        $response = $this->actingAs($user, 'web')->post(route('peserta.checkout.start'), [
            'paket_id' => $paket->id,
            'qty' => 1,
        ]);

        $pesanan = Pesanan::query()->where('user_id', $user->id)->latest('id')->first();
        $this->assertNotNull($pesanan);

        $response->assertRedirect(route('peserta.checkout.payment', ['pesanan' => $pesanan->id]));

        $this->actingAs($user, 'web')
            ->get(route('peserta.checkout.payment', ['pesanan' => $pesanan->id]))
            ->assertOk()
            ->assertSee('Metode pembayaran')
            ->assertSee($pesanan->kode_pesanan);
    }

    public function test_dashboard_shows_pending_status_when_latest_order_pending(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'status_akun' => 'aktif',
        ]);

        $event = Event::query()->create([
            'judul' => 'Event Test',
            'deskripsi' => null,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDay()->toDateString(),
            'status' => 'published',
            'created_by' => $user->id,
        ]);

        $paket = Paket::query()->create([
            'event_id' => $event->id,
            'nama_paket' => 'Paket Test',
            'deskripsi' => 'Deskripsi',
            'harga' => 100000,
            'status' => 'aktif',
            'akses_live' => true,
            'akses_rekaman' => false,
            'kuota' => null,
        ]);

        Pesanan::query()->create([
            'user_id' => $user->id,
            'paket_id' => $paket->id,
            'kode_pesanan' => 'TRX-TEST-123',
            'status_pembayaran' => 'pending',
            'total_bayar' => 100000,
            'metode_pembayaran' => null,
            'waktu_bayar' => null,
        ]);

        $this->actingAs($user, 'web')
            ->get(route('peserta.dashboard'))
            ->assertOk()
            ->assertSee('Paket & Pembayaran')
            ->assertSee('Pending')
            ->assertSee('Bayar Sekarang');
    }
}
