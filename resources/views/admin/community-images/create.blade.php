@extends('layouts.admin')

@section('title', 'Tambah Foto')
@section('page', 'Tambah Foto Komunitas')

@section('content')
    <div class="max-w-3xl bg-white rounded-3xl border border-neutral-200 shadow-card p-6">

        <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4"
            data-validate>
            @csrf

            <div>
                <label for="image" class="block text-sm font-semibold text-neutral-700">
                    Foto
                </label>

                <input type="file" id="image" name="image" required accept="image/jpeg,image/png,image/webp"
                    data-required data-file="image"
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

            <div>
                <label for="caption" class="block text-sm font-semibold text-neutral-700">
                    Caption
                </label>

                <input type="text" id="caption" name="caption" value="{{ old('caption') }}" maxlength="255"
                    data-max="255"
                    class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink"
                    placeholder="Tulis caption foto (opsional)...">

                @error('caption')
                    <p class="mt-1 text-xs font-semibold text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="rounded-full bg-ink px-6 py-2.5 text-sm font-bold text-white shadow-card hover:shadow-hover transition">
                    Simpan
                </button>

                <a href="{{ route('admin.galeri.index') }}"
                    class="rounded-full bg-white border border-neutral-300 px-6 py-2.5 text-sm font-bold text-neutral-700 hover:bg-neutral-100 transition">
                    Batal
                </a>
            </div>

        </form>

    </div>
@endsection