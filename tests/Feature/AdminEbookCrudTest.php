<?php

namespace Tests\Feature;

use App\Models\Ebook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminEbookCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login_when_accessing_ebooks_index(): void
    {
        $response = $this->get('/admin/ebooks');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_create_ebook_with_cover_and_pdf(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create([
            'status_akun' => 'aktif',
        ]);

        $payload = [
            'title' => 'Judul E-book',
            'author' => 'Penulis',
            'description' => 'Deskripsi singkat',
            'price' => 150000,
            'stock' => 10,
            'is_active' => 1,
            'cover_image' => UploadedFile::fake()->image('cover.jpg', 600, 800),
            'pdf_file' => UploadedFile::fake()->create('book.pdf', 500, 'application/pdf'),
        ];

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.ebooks.store'), $payload);

        $response->assertRedirect(route('admin.ebooks.index'));
        $response->assertSessionHas('success');

        $ebook = Ebook::query()->firstOrFail();

        $this->assertSame('Judul E-book', $ebook->title);
        $this->assertSame('Penulis', $ebook->author);
        $this->assertSame('Deskripsi singkat', $ebook->description);
        $this->assertTrue($ebook->is_active);
        $this->assertSame(10, $ebook->stock);

        Storage::disk('public')->assertExists($ebook->cover_image);
        Storage::disk('public')->assertExists($ebook->pdf_file);
    }

    public function test_create_validates_pdf_mime_type(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create([
            'status_akun' => 'aktif',
        ]);

        $payload = [
            'title' => 'Judul E-book',
            'author' => 'Penulis',
            'description' => 'Deskripsi singkat',
            'price' => 150000,
            'stock' => 10,
            'is_active' => 1,
            'cover_image' => UploadedFile::fake()->image('cover.jpg', 600, 800),
            'pdf_file' => UploadedFile::fake()->create('book.jpg', 10, 'image/jpeg'),
        ];

        $response = $this->actingAs($admin, 'admin')
            ->from(route('admin.ebooks.create'))
            ->post(route('admin.ebooks.store'), $payload);

        $response->assertRedirect(route('admin.ebooks.create'));
        $response->assertSessionHasErrors(['pdf_file']);
        $this->assertDatabaseCount('ebooks', 0);
    }

    public function test_admin_can_update_ebook_and_replace_files(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create([
            'status_akun' => 'aktif',
        ]);

        $oldCover = UploadedFile::fake()->image('old.jpg', 300, 400)->store('ebooks/covers', 'public');
        $oldPdf = UploadedFile::fake()->create('old.pdf', 100, 'application/pdf')->store('ebooks/pdf', 'public');

        $ebook = Ebook::factory()->create([
            'cover_image' => $oldCover,
            'pdf_file' => $oldPdf,
            'is_active' => true,
        ]);

        $payload = [
            'title' => 'Judul Baru',
            'author' => 'Penulis Baru',
            'description' => 'Deskripsi baru',
            'price' => 250000,
            'stock' => 7,
            'is_active' => 0,
            'cover_image' => UploadedFile::fake()->image('new.jpg', 600, 800),
            'pdf_file' => UploadedFile::fake()->create('new.pdf', 200, 'application/pdf'),
        ];

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.ebooks.update', $ebook), array_merge($payload, ['_method' => 'PUT']));

        $response->assertRedirect(route('admin.ebooks.index'));
        $response->assertSessionHas('success');

        $ebook->refresh();

        $this->assertSame('Judul Baru', $ebook->title);
        $this->assertSame('Penulis Baru', $ebook->author);
        $this->assertFalse($ebook->is_active);
        $this->assertSame(7, $ebook->stock);

        Storage::disk('public')->assertMissing($oldCover);
        Storage::disk('public')->assertMissing($oldPdf);
        Storage::disk('public')->assertExists($ebook->cover_image);
        Storage::disk('public')->assertExists($ebook->pdf_file);
    }

    public function test_admin_can_soft_delete_ebook(): void
    {
        $admin = User::factory()->admin()->create([
            'status_akun' => 'aktif',
        ]);

        $ebook = Ebook::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->delete(route('admin.ebooks.destroy', $ebook));

        $response->assertRedirect(route('admin.ebooks.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('ebooks', [
            'id' => $ebook->id,
        ]);
    }
}
