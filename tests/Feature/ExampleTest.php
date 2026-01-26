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
            ->assertSee('Pembayaran via Midtrans')
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

    public function test_midtrans_notification_updates_order_status_and_is_idempotent(): void
    {
        config()->set('services.midtrans.server_key', 'server-test');

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

        $pesanan = Pesanan::query()->create([
            'user_id' => $user->id,
            'paket_id' => $paket->id,
            'kode_pesanan' => 'TRX-TEST-999',
            'status_pembayaran' => 'pending',
            'total_bayar' => 100000,
            'metode_pembayaran' => null,
            'waktu_bayar' => null,
        ]);

        $orderId = (string) $pesanan->kode_pesanan;
        $statusCode = '200';
        $grossAmount = '100000';
        $serverKey = 'server-test';

        $payload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'transaction_status' => 'settlement',
            'signature_key' => hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey),
        ];

        $this->postJson('/api/payments/midtrans-notification', $payload)
            ->assertOk()
            ->assertJson(['status' => 'OK']);

        $paid = $pesanan->fresh();
        $this->assertSame('paid', $paid?->status_pembayaran);
        $this->assertSame('midtrans', $paid?->metode_pembayaran);
        $this->assertNotNull($paid?->waktu_bayar);

        $firstPaidAt = $paid?->waktu_bayar;

        $this->postJson('/api/payments/midtrans-notification', $payload)
            ->assertOk()
            ->assertJson(['status' => 'OK']);

        $paid2 = $pesanan->fresh();
        $this->assertSame('paid', $paid2?->status_pembayaran);
        $this->assertSame($firstPaidAt, $paid2?->waktu_bayar);
    }

    public function test_midtrans_notification_rejects_invalid_signature(): void
    {
        config()->set('services.midtrans.server_key', 'server-test');

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

        $pesanan = Pesanan::query()->create([
            'user_id' => $user->id,
            'paket_id' => $paket->id,
            'kode_pesanan' => 'TRX-TEST-998',
            'status_pembayaran' => 'pending',
            'total_bayar' => 100000,
            'metode_pembayaran' => null,
            'waktu_bayar' => null,
        ]);

        $this->postJson('/api/payments/midtrans-notification', [
            'order_id' => (string) $pesanan->kode_pesanan,
            'status_code' => '200',
            'gross_amount' => '100000',
            'transaction_status' => 'settlement',
            'signature_key' => 'invalid',
        ])->assertStatus(403);

        $fresh = $pesanan->fresh();
        $this->assertSame('pending', $fresh?->status_pembayaran);
        $this->assertNull($fresh?->waktu_bayar);
    }
}
