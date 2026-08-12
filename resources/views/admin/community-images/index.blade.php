@extends('layouts.admin')

@section('title', 'Galeri Komunitas')
@section('page', 'Galeri Komunitas')

@section('content')
    <div class="bg-white rounded-3xl border border-neutral-200 shadow-card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <form action="{{ route('admin.galeri.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari caption..."
                           class="w-full rounded-full border border-neutral-300 pl-10 pr-4 py-2 text-sm focus:border-ink focus:ring-ink">
                </div>
                <button type="submit" class="rounded-full bg-ink px-4 py-2 text-sm font-bold text-white shadow-card hover:shadow-hover transition">Cari</button>
                @if (request('search'))
                    <a href="{{ route('admin.galeri.index') }}" class="text-sm font-semibold text-neutral-500 hover:text-neutral-900">Reset</a>
                @endif
            </form>
            <a href="{{ route('admin.galeri.create') }}" class="inline-flex items-center justify-center rounded-full bg-ink px-5 py-2.5 text-sm font-bold text-white shadow-card hover:shadow-hover transition">+ Tambah Foto</a>
        </div>

        @if ($images->isEmpty())
            <div class="mt-8 text-center py-10">
                <p class="font-semibold text-neutral-900">Tidak ada foto ditemukan</p>
            </div>
        @else
            <div class="mt-6 overflow-x-auto">
                <table class="w-full min-w-[640px] text-sm">
                    <thead>
                        <tr class="text-left text-xs font-bold uppercase tracking-wider text-neutral-400 border-b border-neutral-100">
                            <th class="pb-3 pr-4">Foto</th>
                            <th class="pb-3 pr-4">Caption</th>
                            <th class="pb-3 pr-4">Tanggal</th>
                            <th class="pb-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach ($images as $image)
                            <tr>
                                <td class="py-3.5 pr-4">
                                    <img src="{{ $image->image_url }}" alt="{{ $image->caption ?: 'Foto komunitas' }}"
                                        class="h-16 w-16 object-cover rounded-xl border border-neutral-100">
                                </td>
                                <td class="py-3.5 pr-4 font-bold text-neutral-900 max-w-xs">{{ $image->caption ?: '—' }}</td>
                                <td class="py-3.5 pr-4 text-neutral-500">{{ $image->created_at->format('d M Y') }}</td>
                                <td class="py-3.5">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.galeri.edit', $image) }}" class="rounded-full bg-neutral-100 px-3.5 py-1.5 text-xs font-bold text-neutral-700 hover:bg-neutral-200 transition">Edit</a>
                                        <form action="{{ route('admin.galeri.destroy', $image) }}" method="POST" onsubmit="return confirm('Hapus foto ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-full bg-red-50 px-3.5 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100 transition">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $images->links() }}
            </div>
        @endif
    </div>
@endsection