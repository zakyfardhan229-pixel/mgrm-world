@extends('layouts.admin')

@section('title', 'Tambah Produk')
@section('page', 'Tambah Produk')

@section('content')
    <div class="max-w-3xl bg-white rounded-3xl border border-neutral-200 shadow-card p-6">

        <form action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4"
            data-validate>
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- ===================================================== --}}
                {{-- NAMA PRODUK --}}
                {{-- ===================================================== --}}

                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-semibold text-neutral-700">
                        Nama Produk
                    </label>

                    <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="150"
                        data-required data-label="Nama produk" data-max="150"
                        class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">

                    @error('name')
                        <p class="mt-1 text-xs font-semibold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- ===================================================== --}}
                {{-- KATEGORI --}}
                {{-- ===================================================== --}}

                <div>
                    <label for="category_id" class="block text-sm font-semibold text-neutral-700">
                        Kategori
                    </label>

                    <select id="category_id" name="category_id" required data-required data-label="Kategori"
                        class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">
                        <option value="">Pilih kategori...</option>

                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('category_id')
                        <p class="mt-1 text-xs font-semibold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- ===================================================== --}}
                {{-- HARGA --}}
                {{-- ===================================================== --}}

                <div>
                    <label for="price" class="block text-sm font-semibold text-neutral-700">
                        Harga (Rp)
                    </label>

                    <input type="number" id="price" name="price" value="{{ old('price') }}" required min="0" step="0.01"
                        data-required data-number data-min="0" data-label="Harga"
                        class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">

                    @error('price')
                        <p class="mt-1 text-xs font-semibold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- ===================================================== --}}
                {{-- COLOR --}}
                {{-- ===================================================== --}}

                <div>
                    <label for="color" class="block text-sm font-semibold text-neutral-700">
                        Warna
                    </label>

                    <select id="color" name="color"
                        class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">
                        <option value="">Pilih warna...</option>

                        @foreach ([
                                'black' => 'Black',
                                'white' => 'White',
                                'blue' => 'Blue',
                                'red' => 'Red',
                                'green' => 'Green',
                                'yellow' => 'Yellow',
                                'pink' => 'Pink',
                                'grey' => 'Grey',
                                'brown' => 'Brown',
                            ] as $value => $label)

                            <option value="{{ $value }}" @selected(old('color') === $value)>
                                {{ $label }}
                            </option>

                        @endforeach
                    </select>

                    @error('color')
                        <p class="mt-1 text-xs font-semibold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- ===================================================== --}}
                {{-- SIZE --}}
                {{-- ===================================================== --}}

                <div>
                    <label for="size" class="block text-sm font-semibold text-neutral-700">
                        Ukuran
                    </label>

                    <select id="size" name="size"
                        class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">
                        <option value="">Pilih ukuran...</option>

                        @foreach (['S', 'M', 'L', 'XL', 'XXL'] as $size)

                            <option value="{{ $size }}" @selected(old('size') === $size)>
                                {{ $size }}
                            </option>

                        @endforeach
                    </select>

                    @error('size')
                        <p class="mt-1 text-xs font-semibold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- ===================================================== --}}
                {{-- DESKRIPSI --}}
                {{-- ===================================================== --}}

                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-neutral-700">
                        Deskripsi
                    </label>

                    <textarea id="description" name="description" rows="4" maxlength="5000" data-max="5000"
                        class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">{{ old('description') }}</textarea>

                    @error('description')
                        <p class="mt-1 text-xs font-semibold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- ===================================================== --}}
                {{-- STOK --}}
                {{-- ===================================================== --}}

                <div>
                    <label for="stock" class="block text-sm font-semibold text-neutral-700">
                        Stok
                    </label>

                    <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" required min="0" max="999999"
                        data-required data-number data-min="0" data-label="Stok"
                        class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">

                    @error('stock')
                        <p class="mt-1 text-xs font-semibold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- ===================================================== --}}
                {{-- GAMBAR --}}
                {{-- ===================================================== --}}

                <div>
                    <label for="image" class="block text-sm font-semibold text-neutral-700">
                        Gambar Produk (Utama)
                    </label>

                    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" data-file="image"
                        data-file-message="Format gambar harus JPG, PNG, atau WEBP (maks 2MB)"
                        class="mt-1.5 block w-full text-sm text-neutral-600 file:mr-3 file:rounded-full file:border-0 file:bg-neutral-100 file:px-4 file:py-2 file:text-sm file:font-bold file:text-neutral-700 hover:file:bg-neutral-200">

                    <p class="mt-1 text-xs text-neutral-400">
                        JPG, PNG, atau WEBP · maksimal 2MB
                    </p>

                    @error('image')
                        <p class="mt-1 text-xs font-semibold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- ===================================================== --}}
                {{-- GALLERY IMAGES --}}
                {{-- ===================================================== --}}

                <div class="sm:col-span-2">
                    <label for="gallery_images" class="block text-sm font-semibold text-neutral-700">
                        Galeri Gambar Produk
                    </label>

                    <input type="file" id="gallery_images" name="gallery_images[]" accept="image/jpeg,image/png,image/webp" multiple
                        class="mt-1.5 block w-full text-sm text-neutral-600 file:mr-3 file:rounded-full file:border-0 file:bg-neutral-100 file:px-4 file:py-2 file:text-sm file:font-bold file:text-neutral-700 hover:file:bg-neutral-200">

                    <p class="mt-1 text-xs text-neutral-400">
                        Upload beberapa gambar sekaligus · JPG, PNG, atau WEBP · maksimal 2MB per file
                    </p>

                    @error('gallery_images')
                        <p class="mt-1 text-xs font-semibold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                    @error('gallery_images.*')
                        <p class="mt-1 text-xs font-semibold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- ===================================================== --}}
                {{-- STATUS PRODUK --}}
                {{-- ===================================================== --}}

                <div class="sm:col-span-2">

                    <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4 space-y-3">

                        {{-- ACTIVE --}}

                        <label class="flex items-center gap-3 cursor-pointer">

                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))
                                class="rounded border-neutral-300 text-ink focus:ring-ink">

                            <div>
                                <p class="text-sm font-semibold text-neutral-800">
                                    Produk aktif
                                </p>

                                <p class="text-xs text-neutral-500">
                                    Produk akan ditampilkan di toko.
                                </p>
                            </div>

                        </label>


                        {{-- FEATURED --}}

                        <label class="flex items-center gap-3 cursor-pointer">

                            <input type="hidden" name="is_featured" value="0">
                            <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', false))
                                class="rounded border-neutral-300 text-ink focus:ring-ink">

                            <div>
                                <p class="text-sm font-semibold text-neutral-800">
                                    Produk unggulan
                                </p>

                                <p class="text-xs text-neutral-500">
                                    Tandai produk sebagai featured product.
                                </p>
                            </div>

                        </label>

                    </div>

                    @error('is_active')
                        <p class="mt-1 text-xs font-semibold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                    @error('is_featured')
                        <p class="mt-1 text-xs font-semibold text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- ACTION --}}
            {{-- ===================================================== --}}

            <div class="flex items-center gap-3 pt-2">

                <button type="submit"
                    class="rounded-full bg-ink px-6 py-2.5 text-sm font-bold text-white shadow-card hover:shadow-hover transition">
                    Simpan
                </button>

                <a href="{{ route('admin.produk.index') }}"
                    class="rounded-full bg-white border border-neutral-300 px-6 py-2.5 text-sm font-bold text-neutral-700 hover:bg-neutral-100 transition">
                    Batal
                </a>

            </div>

        </form>

    </div>
@endsection