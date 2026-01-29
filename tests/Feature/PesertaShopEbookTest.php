<?php

namespace Tests\Feature;

use App\Models\Ebook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PesertaShopEbookTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_shows_only_active_ebooks(): void
    {
        Ebook::factory()->create([
            'title' => 'Aktif 1',
            'author' => 'Penulis A',
            'is_active' => true,
        ]);

        Ebook::factory()->create([
            'title' => 'Nonaktif 1',
            'author' => 'Penulis B',
            'is_active' => false,
        ]);

        $response = $this->get(route('peserta.shop'));

        $response->assertOk();
        $response->assertSee('E-book');
        $response->assertSee('Aktif 1');
        $response->assertDontSee('Nonaktif 1');
    }

    public function test_shop_search_filters_ebooks(): void
    {
        Ebook::factory()->create([
            'title' => 'Laravel untuk Pemula',
            'author' => 'Penulis A',
            'description' => 'Belajar dasar',
            'is_active' => true,
        ]);

        Ebook::factory()->create([
            'title' => 'Vue untuk Pemula',
            'author' => 'Penulis B',
            'description' => 'Frontend',
            'is_active' => true,
        ]);

        $response = $this->get(route('peserta.shop', [
            'ebook_q' => 'Laravel',
        ]));

        $response->assertOk();
        $response->assertSee('Laravel untuk Pemula');
        $response->assertDontSee('Vue untuk Pemula');
    }

    public function test_guest_cannot_download_ebook_pdf(): void
    {
        Storage::fake('public');

        $pdfPath = UploadedFile::fake()
            ->create('book.pdf', 100, 'application/pdf')
            ->store('ebooks/pdf', 'public');

        $ebook = Ebook::factory()->create([
            'title' => 'Buku PDF',
            'pdf_file' => $pdfPath,
            'is_active' => true,
        ]);

        $response = $this->get(route('peserta.ebooks.download', $ebook));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_download_ebook_pdf(): void
    {
        Storage::fake('public');

        $pdfPath = UploadedFile::fake()
            ->create('book.pdf', 100, 'application/pdf')
            ->store('ebooks/pdf', 'public');

        $ebook = Ebook::factory()->create([
            'title' => 'Buku PDF',
            'pdf_file' => $pdfPath,
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'role' => 'user',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($user, 'web')->get(route('peserta.ebooks.download', $ebook));

        $response->assertOk();
        $response->assertDownload('Buku PDF.pdf');
    }
}
