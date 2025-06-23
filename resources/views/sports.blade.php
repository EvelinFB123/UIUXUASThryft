@extends('layouts.app')

@section('content')

<!-- Hero Shop Section -->
<section class="relative">
  <img src="{{ asset('images/blur.jpg') }}" alt="Shop Banner" class="w-full h-64 object-cover">
  <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-black">
    <h1 class="text-4xl font-bold mb-2">Product</h1>
    <p class="text-gray-700">
      <a href="{{ route('categories') }}" class="hover:text-yellow-500 hover:underline">Categories</a>
      > Sports
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
        ['image' => 'treadmill-lipat-bermotor-dengan-kemiringan-10-run500-domyos-8542707.jpg', 'name' => 'Treadmill', 'price' => '850.000,00'],
        ['image' => 'dumbbell.jpg', 'name' => 'Dumbbell', 'price' => '50.000,00'],
        ['image' => 'barbelll.jpeg', 'name' => 'Barbell', 'price' => '100.000,00'],
        ['image' => 'resistance band.jpg', 'name' => 'Resistance Band', 'price' => '20.000,00'],
        ['image' => 'rowing Machine (1).jpg', 'name' => 'Rowing Machine', 'price' => '200.000,00'],
        ['image' => 'leg-press.jpg', 'name' => 'Leg Press', 'price' => '400.000,00'],
        ['image' => 'static bike.jpg', 'name' => 'Static Bike', 'price' => '300.000,00'],
        ['image' => 'pull up bar.jpg', 'name' => 'Pull Up Bar', 'price' => '100.000,00'],
        ['image' => 'push-up-bar.jpg', 'name' => 'Push Up Bar', 'price' => '75.000,00'],
        ['image' => 'bench press.jpg', 'name' => 'Bench Press', 'price' => '150.000,00'],

      ];
    @endphp -->

    @php
  $products = App\Models\Product::whereHas('category', function($query) {
    $query->where('name', 'Sports Equipment');
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
