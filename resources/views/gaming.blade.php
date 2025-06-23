@extends('layouts.app')

@section('content')



<!-- Hero Shop Section -->
<section class="relative">
  <img src="{{ asset('images/blur.jpg') }}" alt="Shop Banner" class="w-full h-64 object-cover">
  <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-black">
    <h1 class="text-4xl font-bold mb-2">Product</h1>
    <p class="text-gray-700">
      <a href="{{ route('categories') }}" class="hover:text-yellow-500 hover:underline">Categories</a>
      > Gaming
    </p>
  </div>
</section>

<!-- Filter & Sorting -->
<section class="sticky top-16 z-50 bg-gray-50 border-b shadow w-full">
  <div class="flex items-center p-6 justify-start pl-4">
    <div class="flex items-center space-x-4">
      <div class="flex items-center mb-0"></div>
      <div class="flex items-center space-x-2">
        <span class="text-sm">Sort by</span>
        <select id="sortPrice" class="border rounded px-2 py-1 text-sm">
          <option value="">Default</option>
          <option value="asc">Price: Low to High</option>
          <option value="desc">Price: High to Low</option>
        </select>
      </div>
    </div>
  </div>
</section>

<!-- Product Grid -->
<section class="p-6 bg-white">
  <div id="product-list" class="grid grid-cols-2 sm:grid-cols-4 gap-6">

    <!-- Product Cards -->
    <!-- @php
      $products = [
        ['image' => 'keyboard gaming.jpg', 'name' => 'Gaming Keyboard', 'price' => '75.000,00'],
        ['image' => 'gaming mouse.jpg', 'name' => 'Gaming Mouse', 'price' => '45.000,00'],
        ['image' => 'Nintendo Switch Ori.jpeg', 'name' => 'Nintendo Switch', 'price' => '1.500.000,00'],
        ['image' => 'nintendo switch lite.jpeg', 'name' => 'Nintendo Switch Lite', 'price' => '1.000.000,00'],
        ['image' => 'ps 1 slim.jpg', 'name' => 'Playstation 1', 'price' => '200.000,00'],
        ['image' => 'ps2.jpeg', 'name' => 'Playstation 2', 'price' => '500.000,00'],
        ['image' => 'ps 3.jpeg', 'name' => 'Playstation 3', 'price' => '900.000,00'],
        ['image' => 'ps4.jpg', 'name' => 'Playstation 4', 'price' => '2.000.000,00'],
        ['image' => 'ps5.jpg', 'name' => 'Playstation 5', 'price' => '3.500.000,00'],
        ['image' => 'headset gaming.jpg', 'name' => 'Gaming Headset', 'price' => '50.000,00'],
        ['image' => 'physical games.jpeg', 'name' => 'Playstation Physical Copy', 'price' => '100.000,00'],
        ['image' => 'nintendo physical copy.jpg', 'name' => 'Nintendo Physical Copy', 'price' => '150.000,00'],

      ];
    @endphp -->

    @php
  $products = App\Models\Product::whereHas('category', function($query) {
    $query->where('name', 'Gaming');
  })->get();
@endphp



@foreach($products as $product)
<a href="{{ route('detail', ['category' => strtolower($product->category->name), 'id' => $product->id]) }}" 
       class="product-item block bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition group"
       data-price="{{ $product->price }}">
  <div class="relative overflow-hidden transition-all duration-500 ease-in-out">
      <img src="{{ asset('images/' . $product->image) }}"
          alt="{{ $product->name }}"
          class="w-full h-64 object-contain group-hover:scale-95 transition-all duration-500 ease-in-out" />
      <span class="absolute top-0 right-0 font-medium py-0.5 px-1 text-sm z-30 rounded inline-block no-underline pointer-events-none {{ $product->condition === 'New' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800' }}">
  {{ $product->condition === 'New' ? 'Unused' : ($product->condition !== 'Not specified' ? $product->condition : '') }}
</span>

  </div>
  <div class="p-4 text-center">
      <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
      <p class="text-sm text-gray-500 mb-1">{{ $product->category->name ?? 'Tanpa Kategori' }}</p>
      <p class="text-sm text-gray-700 mb-1">{{ Str::limit($product->description, 60) }}</p>
      <p class="text-yellow-600 font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
  </div>
</a>
@endforeach

  </div>
</section>

<!-- Service Info -->
<div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; display: flex; justify-content: space-between; align-items: flex-start; gap: 30px; padding: 20px; border: 1px solid #eee; border-radius: 8px;">
  <div style="flex: 1;">
    <h3 style="font-size: 18px; margin: 0 0 8px 0; color: #333;">Guaranteed Quality</h3>
    <p style="margin: 0; font-size: 14px; color: #666;">Thoroughly inspected for your satisfaction</p>
  </div>
  <div style="width: 1px; background-color: #eee; height: 50px;"></div>
  <div style="flex: 1;">
    <h3 style="font-size: 18px; margin: 0 0 8px 0; color: #333;">Free Shipping</h3>
    <p style="margin: 0; font-size: 14px; color: #666;">Order over Rp. 100.000,00</p>
  </div>
  <div style="width: 1px; background-color: #eee; height: 50px;"></div>
  <div style="flex: 1;">
    <h3 style="font-size: 18px; margin: 0 0 8px 0; color: #333;">24 / 7 Support</h3>
    <p style="margin: 0; font-size: 14px; color: #666;">Dedicated support</p>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const sortSelect = document.getElementById('sortPrice');
  
  if (sortSelect) {
    sortSelect.addEventListener('change', function() {
      const sortValue = this.value;
      const productList = document.getElementById('product-list');
      const items = Array.from(productList.querySelectorAll('.product-item'));

      if (sortValue === 'asc') {
        items.sort((a, b) => {
          const priceA = parseInt(a.dataset.price);
          const priceB = parseInt(b.dataset.price);
          return priceA - priceB;
        });
      } else if (sortValue === 'desc') {
        items.sort((a, b) => {
          const priceA = parseInt(a.dataset.price);
          const priceB = parseInt(b.dataset.price);
          return priceB - priceA;
        });
      }

      // Clear the container
      productList.innerHTML = '';
      
      // Re-add sorted items
      items.forEach(item => {
        productList.appendChild(item);
      });
    });
  }
});
</script>


@endsection
