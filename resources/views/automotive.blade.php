@extends('layouts.app')

@section('content')

<!-- Hero Shop Section -->
<section class="relative">
  <img src="{{ asset('images/blur.jpg') }}" alt="Shop Banner" class="w-full h-64 object-cover">
  <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-black">
    <h1 class="text-4xl font-bold mb-2">Product</h1>
    <p class="text-gray-700">
      <a href="{{ route('categories') }}" class="hover:text-yellow-500 hover:underline">Categories</a>
      > Automotive 
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
    @php
      $products = App\Models\Product::whereHas('category', function($query) {
        $query->where('name', 'Automotive');
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
<div class="max-w-4xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4 p-5 border border-gray-200 rounded-lg my-6">
  <div class="text-center">
    <h3 class="font-semibold text-lg">Guaranteed Quality</h3>
    <p class="text-gray-600 text-sm">Thoroughly inspected for your satisfaction</p>
  </div>
  <div class="hidden sm:block h-12 w-px bg-gray-200"></div>
  <div class="text-center">
    <h3 class="font-semibold text-lg">Free Shipping</h3>
    <p class="text-gray-600 text-sm">Order over Rp. 100.000,00</p>
  </div>
  <div class="hidden sm:block h-12 w-px bg-gray-200"></div>
  <div class="text-center">
    <h3 class="font-semibold text-lg">24 / 7 Support</h3>
    <p class="text-gray-600 text-sm">Dedicated support</p>
  </div>
</div>

<script>
document.getElementById('sortPrice').addEventListener('change', function() {
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
</script>

@endsection