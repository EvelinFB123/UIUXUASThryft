@extends('layouts.app')

@section('content')

@auth
<!-- Chat Header -->
<div class="flex items-center justify-between p-4 border-b bg-white sticky top-0 z-50">
  <div class="flex items-center space-x-3">
    <a href="{{ url()->previous() }}" class="text-yellow-500">
  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
       viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round"
          stroke-width="2" d="M15 19l-7-7 7-7" />
  </svg>
</a>

    <img src="{{ asset('images/user.png') }}" alt="Seller" class="w-8 h-8 rounded-full">
    <h2 class="text-lg font-semibold">Seller</h2>
  </div>
</div>

<!-- Chat Messages -->
<div class="p-4 space-y-4 overflow-y-auto h-[70vh] bg-gray-50" id="chat-messages">

  <!-- Seller Message -->
  <div class="flex justify-start">
    <div class="max-w-xs p-3 rounded-lg bg-white border">
      <p class="text-sm">Halo! Ada yang bisa saya bantu?</p>
      <span class="block text-[10px] mt-1 opacity-70">10:00</span>
    </div>
  </div>

  <!-- User Message -->
  <div class="flex justify-end">
    <div class="max-w-xs p-3 rounded-lg bg-yellow-500 text-white">
      <p class="text-sm">Mau tanya soal barang ini, masih ada?</p>
      <span class="block text-[10px] mt-1 opacity-80">10:02</span>
    </div>
  </div>

  <!-- Seller Message -->
  <div class="flex justify-start">
    <div class="max-w-xs p-3 rounded-lg bg-white border">
      <p class="text-sm">Masih kak, pemakaian sekali</p>
      <span class="block text-[10px] mt-1 opacity-70">10:03</span>
    </div>
  </div>

</div>

<!-- Send Message Form -->
<form action="#" method="POST" class="fixed bottom-0 left-0 right-0 p-3 bg-white border-t flex space-x-2">
  <input type="text" name="message" placeholder="Type a message..." required
         class="flex-1 border rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
  <button type="submit"
          class="bg-yellow-500 text-white px-4 py-2 rounded-full hover:bg-yellow-600 transition">
    Send
  </button>
</form>
@endauth

@endsection