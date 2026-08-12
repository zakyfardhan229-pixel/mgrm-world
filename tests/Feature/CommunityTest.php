<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\CommunityImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CommunityTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => UserRole::Admin]);
    }

    private function customer(): User
    {
        return User::factory()->create(['role' => UserRole::Customer]);
    }

    public function test_guest_can_view_community_gallery(): void
    {
        CommunityImage::factory()->count(2)->create([
            'caption' => 'Momen seru komunitas',
        ]);

        $this->get('/komunitas')
            ->assertOk()
            ->assertSee('Momen seru komunitas')
            ->assertSee('columns-2')
            ->assertSee('break-inside-avoid');
    }

    public function test_community_page_shows_empty_state(): void
    {
        $this->get('/komunitas')
            ->assertOk()
            ->assertSee('Belum ada foto');
    }

    public function test_admin_can_create_community_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post('/admin/galeri', [
                'image' => UploadedFile::fake()->image('foto.jpeg', 400, 400),
                'caption' => 'Foto pertama komunitas',
            ])
            ->assertRedirect(route('admin.galeri.index'));

        $image = CommunityImage::where('caption', 'Foto pertama komunitas')->firstOrFail();

        $this->assertNotNull($image->image);
        Storage::disk('public')->assertExists($image->image);
    }

    public function test_community_image_rejects_invalid_file(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post('/admin/galeri', [
                'image' => UploadedFile::fake()->create('dokumen.txt'),
            ])
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('community_images', 0);
    }

    public function test_admin_can_update_community_image(): void
    {
        Storage::fake('public');

        $image = CommunityImage::factory()->create();

        $this->actingAs($this->admin())
            ->put("/admin/galeri/{$image->id}", [
                'image' => UploadedFile::fake()->image('baru.jpeg', 300, 300),
                'caption' => 'Caption diperbarui',
            ])
            ->assertRedirect(route('admin.galeri.index'));

        $image->refresh();

        $this->assertSame('Caption diperbarui', $image->caption);
        Storage::disk('public')->assertExists($image->image);
    }

    public function test_admin_can_delete_community_image(): void
    {
        Storage::fake('public');

        $image = CommunityImage::factory()->create();
        Storage::disk('public')->put($image->image, 'content');

        $this->actingAs($this->admin())
            ->delete("/admin/galeri/{$image->id}")
            ->assertRedirect();

        $this->assertDatabaseCount('community_images', 0);
        Storage::disk('public')->assertMissing($image->image);
    }

    public function test_customer_cannot_access_gallery_admin(): void
    {
        $this->actingAs($this->customer())
            ->get('/admin/galeri')
            ->assertForbidden();
    }

    public function test_guest_cannot_access_gallery_admin(): void
    {
        $this->get('/admin/galeri')
            ->assertRedirect(route('login'));
    }
}
