@extends('layouts.admin')

@section('title', 'Edit Produk')
@section('page', 'Edit Produk')

@section('content')
    <div class="max-w-3xl bg-white rounded-3xl border border-neutral-200 shadow-card p-6">
        <form action="{{ route('admin.produk.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-4" data-validate>
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-semibold text-neutral-700">Nama Produk</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="150" data-required data-label="Nama produk" data-max="150"
                           class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">
                    @error('name') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-semibold text-neutral-700">Kategori</label>
                    <select id="category_id" name="category_id" required data-required data-label="Kategori"
                            class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">
                        <option value="">Pilih kategori...</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-semibold text-neutral-700">Harga (Rp)</label>
                    <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" required min="0" step="0.01" data-required data-number data-min="0" data-label="Harga"
                           class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">
                    @error('price') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-neutral-700">Deskripsi (opsional)</label>
                    <textarea id="description" name="description" rows="4" maxlength="5000" data-max="5000"
                              class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">{{ old('description', $product->description) }}</textarea>
                    @error('description') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="stock" class="block text-sm font-semibold text-neutral-700">Stok</label>
                    <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required min="0" max="999999" data-required data-number data-min="0" data-label="Stok"
                           class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">
                    @error('stock') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="color" class="block text-sm font-semibold text-neutral-700">Warna</label>
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
                            <option value="{{ $value }}" @selected(old('color', $product->color) === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('color') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="size" class="block text-sm font-semibold text-neutral-700">Ukuran</label>
                    <select id="size" name="size"
                            class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">
                        <option value="">Pilih ukuran...</option>
                        @foreach (['S', 'M', 'L', 'XL', 'XXL'] as $size)
                            <option value="{{ $size }}" @selected(old('size', $product->size) === $size)>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                    @error('size') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="image" class="block text-sm font-semibold text-neutral-700">Ganti Gambar (opsional)</label>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" data-file="image" data-file-message="Format gambar harus JPG, PNG, atau WEBP (maks 2MB)"
                           class="mt-1.5 block w-full text-sm text-neutral-600 file:mr-3 file:rounded-full file:border-0 file:bg-neutral-100 file:px-4 file:py-2 file:text-sm file:font-bold file:text-neutral-700 hover:file:bg-neutral-200">
                    <p class="mt-1 text-xs text-neutral-400">Kosongkan jika tidak mengganti gambar. JPG, PNG, WEBP · maks 2MB</p>
                    @error('image') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2 flex flex-wrap gap-6">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active)) class="rounded border-neutral-300 text-ink focus:ring-ink">
                        <span class="text-sm font-semibold text-neutral-700">Produk aktif (ditampilkan di toko)</span>
                    </label>

                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured)) class="rounded border-neutral-300 text-ink focus:ring-ink">
                        <span class="text-sm font-semibold text-neutral-700">Produk unggulan (featured)</span>
                    </label>
                </div>

                <div class="sm:col-span-2">
                    <p class="text-sm font-semibold text-neutral-700">Gambar Saat Ini</p>
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="mt-2 w-32 h-32 rounded-2xl object-cover border border-neutral-200">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="rounded-full bg-ink px-6 py-2.5 text-sm font-bold text-white shadow-card hover:shadow-hover transition">Simpan Perubahan</button>
                <a href="{{ route('admin.produk.index') }}" class="rounded-full bg-white border border-neutral-300 px-6 py-2.5 text-sm font-bold text-neutral-700 hover:bg-neutral-100 transition">Batal</a>
            </div>
        </form>
    </div>
@endsection