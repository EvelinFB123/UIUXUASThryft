@extends('layouts.app')

@section('content')
<div class="container mx-auto py-10">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">My Shop</h2>
    <a href="#" class="bg-yellow-500 text-white px-4 py-2 rounded-full hover:bg-yellow-600">
      + Upload Product
    </a>
  </div>

  <!-- Product List (replace with loop later) -->
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    <div class="bg-white p-4 rounded shadow text-center">
      <img src="{{ asset('images/sample-product.jpg') }}" class="w-full h-32 object-cover rounded mb-2" alt="">
      <h3 class="font-medium text-gray-700">Sample Product</h3>
      <p class="text-sm text-gray-500">Rp 20.000</p>
    </div>
    <!-- repeat products... -->
  </div>
</div>
@endsection
