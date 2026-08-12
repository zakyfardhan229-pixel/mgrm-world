@extends('layouts.shop')

@section('title', 'Komunitas')

@section('content')

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-12">

        <!-- <div class="mb-8 text-center">

                <h1 class="text-2xl sm:text-[25px] font-extrabold text-neutral-900">Komunitas</h1>

                <p class="mt-1 text-sm text-neutral-500">
                    Galeri momen dari komunitas MGRM World.
                </p>

            </div> -->

        @if ($images->isEmpty())

            <div class="border border-neutral-200 p-12 text-center">
                <p class="text-lg font-semibold text-neutral-900">
                    Belum ada foto
                </p>

                <p class="mt-1 text-sm text-neutral-500">
                    Foto komunitas akan muncul di sini.
                </p>
            </div>

        @else

            <div class="columns-2 md:columns-3 lg:columns-4 gap-0">

                @foreach ($images as $image)

                    <figure class="group relative break-inside-avoid">
                        <img src="{{ $image->image_url }}" alt="{{ $image->caption ?: 'Foto komunitas' }}"
                            class="w-full h-auto block">

                        @if ($image->caption)

                            <figcaption
                                class="absolute inset-0 flex items-end bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <p class="px-4 py-3 text-xs font-semibold text-white">
                                    {{ $image->caption }}
                                </p>
                            </figcaption>

                        @endif
                    </figure>

                @endforeach

            </div>

            <div class="mt-14">
                {{ $images->links() }}
            </div>

        @endif

    </section>

@endsection