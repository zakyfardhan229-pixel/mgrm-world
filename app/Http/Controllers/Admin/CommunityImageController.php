<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommunityImageRequest;
use App\Models\CommunityImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CommunityImageController extends Controller
{
    /**
     * Display a paginated list of community images with search.
     */
    public function index(Request $request): View
    {
        $images = CommunityImage::query()
            ->when(
                filled($request->search),
                fn ($query) => $query->where('caption', 'like', "%{$request->search}%")
            )
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.community-images.index', [
            'images' => $images,
        ]);
    }

    /**
     * Show the form for creating a new community image.
     */
    public function create(): View
    {
        return view('admin.community-images.create');
    }

    /**
     * Store a newly created community image.
     */
    public function store(CommunityImageRequest $request): RedirectResponse
    {
        CommunityImage::create([
            'image' => $request->file('image')->store('community', 'public'),
            'caption' => $request->validated('caption'),
        ]);

        return Redirect::route('admin.galeri.index')
            ->with('success', 'Foto berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a community image.
     */
    public function edit(CommunityImage $communityImage): View
    {
        return view('admin.community-images.edit', [
            'communityImage' => $communityImage,
        ]);
    }

    /**
     * Update the given community image.
     */
    public function update(CommunityImageRequest $request, CommunityImage $communityImage): RedirectResponse
    {
        $communityImage->update([
            'image' => $this->storeImage($request, $communityImage),
            'caption' => $request->validated('caption'),
        ]);

        return Redirect::route('admin.galeri.index')
            ->with('success', 'Foto berhasil diperbarui.');
    }

    /**
     * Remove the given community image along with its stored file.
     */
    public function destroy(CommunityImage $communityImage): RedirectResponse
    {
        Storage::disk('public')->delete($communityImage->image);

        $communityImage->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    /**
     * Persist an uploaded image and return its storage path.
     */
    private function storeImage(CommunityImageRequest $request, CommunityImage $communityImage): ?string
    {
        if (! $request->hasFile('image')) {
            return $communityImage->image;
        }

        $path = $request->file('image')->store('community', 'public');

        if ($communityImage->image !== $path) {
            Storage::disk('public')->delete($communityImage->image);
        }

        return $path;
    }
}
