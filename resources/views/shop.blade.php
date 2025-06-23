@extends('layouts.app')

@section('content')

<!-- Hero Shop Section -->
<section class="relative">
  <img src="{{ asset('images/blur.jpg') }}" alt="Shop Banner" class="w-full h-64 object-cover">
  <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-black">
    <h1 class="text-4xl font-bold mb-2">Shop</h1>
    <p class="text-gray-700">
      <a href="{{ route('home') }}" class="hover:text-yellow-500 hover:underline">Home</a> > Shop
    </p>
  </div>
</section>

<!-- Filter Kategori (Dropdown) -->
<section class="sticky top-16 z-30 bg-white border-b px-6 py-4">
  <div class="flex items-center justify-between">
    <h2 class="text-xl font-semibold">Shop</h2>
    <div class="relative" x-data="{ open: false }">
      <button @click="open = !open"
        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded shadow-sm hover:bg-gray-100">
        <svg class="w-5 h-5 mr-2 text-gray-700" fill="none" stroke="currentColor" stroke-width="2"
          viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 14.414V19a1 1 0 01-.553.894l-4 2A1 1 0 019 21v-6.586L3.293 6.707A1 1 0 013 6V4z" />
        </svg>
        <span class="text-gray-700">
          {{ request('category') ? $categories->firstWhere('id', request('category'))?->name ?? 'Filter Kategori' : 'Semua Kategori' }}
        </span>
        <svg class="w-4 h-4 ml-2 text-gray-700" fill="none" stroke="currentColor" stroke-width="2"
          viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      <!-- Dropdown -->
      <div x-show="open" @click.away="open = false"
        class="absolute right-0 mt-2 w-60 bg-white border border-gray-300 rounded shadow z-10 max-h-64 overflow-auto">
        <a href="{{ route('shop') }}"
          class="block px-4 py-2 text-sm hover:bg-yellow-100 {{ request('category') ? 'text-gray-700' : 'bg-yellow-500 text-white' }}">
          Semua Kategori
        </a>
        @foreach($categories as $category)
          <a href="{{ route('shop', ['category' => $category->id]) }}"
            class="block px-4 py-2 text-sm hover:bg-yellow-100 {{ request('category') == $category->id ? 'bg-yellow-500 text-white' : 'text-gray-700' }}">
            {{ $category->name }}
          </a>
        @endforeach
      </div>
    </div>
  </div>
</section>

<!-- Daftar Produk -->
<section class="p-6 bg-white">
  @if($products->isNotEmpty())
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
      @foreach($products as $product)
      <a href="{{ route('detail', ['category' => strtolower($product->category->name), 'id' => $product->id]) }}" class="block group">
  <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 ease-in-out">
    <div class="relative overflow-hidden">
      <img src="{{ asset('images/' . ($product->image ?? 'default.png')) }}"
        alt="{{ $product->name }}"
        class="w-full h-64 object-contain group-hover:scale-95 transition duration-300 ease-in-out" />
      @if($product->condition !== 'Not specified')
        <span
          class="absolute top-0 right-0 font-medium py-0.5 px-2 text-sm z-10 rounded {{ $product->condition === 'New' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800' }}">
          {{ $product->condition === 'New' ? 'Unused' : $product->condition }}
        </span>
      @endif
    </div>
    <div class="p-4 text-center">
      <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
      <p class="text-sm text-gray-500 mb-1">{{ $product->category->name ?? 'Tanpa Kategori' }}</p>
      <p class="text-sm text-gray-700 mb-1">{{ Str::limit($product->description, 60) }}</p>
      <p class="text-yellow-600 font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
    </div>
  </div>
</a>
@endforeach
</div>

<!-- PAGINATION -->
    <div class="mt-8 flex justify-center">
      {{ $products->withQueryString()->links() }}
    </div>
  @else
    <div class="text-center text-gray-500">
      <p>Tidak ada produk untuk kategori ini.</p>
    </div>
  @endif
</section>

<!-- Service Info -->
<div class="max-w-5xl mx-auto px-4 py-6 border-t border-gray-200 mt-6 flex flex-col md:flex-row justify-between gap-6 text-sm text-gray-600">
  <div class="flex-1 text-center">
    <h3 class="text-base font-semibold text-gray-800 mb-1">Guaranteed Quality</h3>
    <p>Thoroughly inspected for your satisfaction</p>
  </div>
  <div class="hidden md:block w-px bg-gray-200 mx-4"></div>
  <div class="flex-1 text-center">
    <h3 class="text-base font-semibold text-gray-800 mb-1">Free Shipping</h3>
    <p>Order over Rp. 100.000,00</p>
  </div>
  <div class="hidden md:block w-px bg-gray-200 mx-4"></div>
  <div class="flex-1 text-center">
    <h3 class="text-base font-semibold text-gray-800 mb-1">24 / 7 Support</h3>
    <p>Dedicated support</p>
  </div>
</div>

@endsection
