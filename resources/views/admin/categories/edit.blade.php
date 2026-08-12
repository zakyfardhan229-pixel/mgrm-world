@extends('layouts.admin')

@section('title', 'Edit Kategori')
@section('page', 'Edit Kategori')

@section('content')
    <div class="max-w-2xl bg-white rounded-3xl border border-neutral-200 shadow-card p-6">
        <form action="{{ route('admin.kategori.update', $category) }}" method="POST" class="space-y-4" data-validate>
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-semibold text-neutral-700">Nama Kategori</label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required maxlength="100" data-required data-label="Nama kategori" data-max="100"
                       class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">
                @error('name') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-neutral-700">Deskripsi (opsional)</label>
                <textarea id="description" name="description" rows="4" maxlength="2000" data-max="2000"
                          class="mt-1.5 w-full rounded-xl border border-neutral-300 px-4 py-2.5 text-sm focus:border-ink focus:ring-ink">{{ old('description', $category->description) }}</textarea>
                @error('description') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="rounded-full bg-ink px-6 py-2.5 text-sm font-bold text-white shadow-card hover:shadow-hover transition">Simpan Perubahan</button>
                <a href="{{ route('admin.kategori.index') }}" class="rounded-full bg-white border border-neutral-300 px-6 py-2.5 text-sm font-bold text-neutral-700 hover:bg-neutral-100 transition">Batal</a>
            </div>
        </form>
    </div>
@endsection