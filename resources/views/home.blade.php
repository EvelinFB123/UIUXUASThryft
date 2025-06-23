@extends('layouts.app')

@section('title', 'Thryft - Home')

@section('content')


<!-- Hero Carousel -->
<section 
  x-data="carousel()" 
  x-init="init"
  @touchstart="handleTouchStart"
  @touchmove="handleTouchMove"
  @touchend="handleTouchEnd"
  class="relative h-[500px] overflow-hidden select-none touch-pan-y"
  style="overscroll-behavior-x: none;"
>

  <!-- Slides -->
  <template x-for="(slide, index) in slides" :key="index">
    <div 
      x-show="activeSlide === index"
      class="absolute inset-0 bg-cover bg-center transition-shadow duration-1000 ease-in-out"
      :style="'background-image: url(' + slide.image + ')'">
      
      <!-- Overlay -->
      <div class="absolute inset-0 bg-black bg-opacity-40"></div>

      <!-- Content -->
      <div class="container mx-auto px-6 h-full flex items-center justify-end relative z-10">
        <div class="bg-yellow-50 bg-opacity-95 p-8 rounded-lg shadow-lg max-w-md text-right">
          <p class="text-xs sm:text-sm uppercase tracking-widest text-gray-700 mb-2">New Arrival</p>
          <h1 class="text-3xl sm:text-4xl font-extrabold text-yellow-800 leading-snug mb-4" x-text="slide.title"></h1>
          <p class="text-gray-700 mb-6" x-text="slide.subtitle"></p>
          <button 
            class="px-6 py-3 bg-yellow-500 text-white font-semibold rounded hover:bg-yellow-600 transition duration-300"
            @click="goToShop">
            Shop Now
          </button>
        </div>
      </div>
    </div>
  </template>

  <!-- Navigation Arrows -->
  <button @click="prev" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-yellow-500 text-white p-2 rounded-full hover:bg-yellow-600 z-20">
    &#8592;
  </button>
  <button @click="next" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-yellow-500 text-white p-2 rounded-full hover:bg-yellow-600 z-20">
    &#8594;
  </button>
</section>





<!-- Categories -->
<section class="p-8">
  <h2 class="text-2xl font-semibold mb-6 text-center">Categories</h2>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-6 overflow-visible">
    <div class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer" onclick="window.location.href='{{ route('woman') }}'">
      <div class="h-44 overflow-hidden">
        <img src="https://down-id.img.susercontent.com/file/id-11134207-7ras8-m3w79vsvoglv5f" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" alt="">
      </div>
      <div class="p-4 text-center font-semibold text-gray-800">Woman Clothing & Accessories</div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer" onclick="window.location.href='{{ route('carpentry') }}'">
      <div class="h-44 overflow-hidden">
        <img src="https://encrypted-tbn3.gstatic.com/images?q=tbn:ANd9GcSQuayB55R94Pp5CkjXRnypyNaP2u6p_x1DJToaIxNinZ78mx_Q" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" alt="">
      </div>
      <div class="p-4 text-center font-semibold text-gray-800">Carpentry</div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer" onclick="window.location.href='{{ route('electronics') }}'">
      <div class="h-44 overflow-hidden">
        <img src="{{ asset('images/elecs.jpg') }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" alt="">
      </div>
      <div class="p-4 text-center font-semibold text-gray-800">Electronics</div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer" onclick="window.location.href='{{ route('furniture') }}'">
      <div class="h-44 overflow-hidden">
        <img src="{{ asset('images/furniture.jpg') }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" alt="">
      </div>
      <div class="p-4 text-center font-semibold text-gray-800">Furniture</div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer" onclick="window.location.href='{{ route('kitchen') }}'">
      <div class="h-44 overflow-hidden">
        <img src="{{ asset('images/kitchenware.jpg') }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" alt="">
      </div>
      <div class="p-4 text-center font-semibold text-gray-800">Kitchen Items</div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer" onclick="window.location.href='{{ route('sports') }}'">
      <div class="h-44 overflow-hidden">
        <img src="{{ asset('images/sport.jpg') }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" alt="">
      </div>
      <div class="p-4 text-center font-semibold text-gray-800">Sports Equipment</div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer" onclick="window.location.href='{{ route('books') }}'">
      <div class="h-44 overflow-hidden">
        <img src="{{ asset('images/booka.jpg') }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" alt="">
      </div>
      <div class="p-4 text-center font-semibold text-gray-800">Books</div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer" onclick="window.location.href='{{ route('automotive') }}'">
      <div class="h-44 overflow-hidden">
        <img src="{{ asset('images/sparepart.jpg') }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" alt="">
      </div>
      <div class="p-4 text-center font-semibold text-gray-800">Automotive</div>
    </div>
  </div>
  
  <div class="mt-8 text-center">
  <button 
    onclick="window.location.href='{{ route('categories') }}'"
    class="px-6 py-3 bg-white text-yellow-500 rounded border-2 border-yellow-500 
          hover:!bg-yellow-500 hover:!text-white transition-colors duration-300">
    See More
  </button>
</div>

  </section>



<!-- Top Picks -->
<section class="p-8 bg-[#f8f4e9]">
  <div class="flex flex-col md:flex-row items-center gap-8">
    
    <!-- Left Text -->
    <div class="md:w-1/3">
      <h2 class="text-3xl font-bold mb-4">Our Top Picks</h2>
      <p class="text-gray-600 mb-6">See what our top products are!</p>
      <button class="px-5 py-3 bg-yellow-500 text-white rounded hover:bg-yellow-600" onclick="window.location.href='{{ route('shop') }}'">Explore More</button>
    </div>

    <!-- Right Images -->
    <div class="md:w-2/3 grid grid-cols-2 gap-4 ">
      <!-- Item 1 -->
      @foreach($featuredProducts as $produk)
    <a href="{{ route('detail', ['category' => strtolower($produk->category->name), 'id' => $produk->id]) }}">
      <div class="relative group overflow-hidden rounded-lg hover:shadow-lg cursor-pointer">
        <img src="{{ asset('images/' . $produk->image) }}" alt="{{ $produk->name }}" class="object-cover w-full h-[350px]">
        <div class="absolute bottom-0 left-0 right-0 bg-white bg-opacity-90 p-4">
          <p class="text-xs text-gray-500">
            {{ sprintf('%02d', $loop->iteration) }} — {{ $produk->category->name }}
          </p>
          <h3 class="text-lg font-semibold">{{ $produk->name }}</h3>
        </div>
      </div>
    </a>
  @endforeach
      <!-- <div class="relative group overflow-hidden rounded-lg hover:shadow-lg cursor-pointer">
        <img src="{{ asset('images/dress.jpg') }}" alt="Flower Dress" class="object-cover w-full h-[350px]">
        <div class="absolute bottom-0 left-0 right-0 bg-white bg-opacity-90 p-4">
          <p class="text-xs text-gray-500 ">01 — Woman</p>
          <h3 class="text-lg font-semibold">Flower Dress</h3>
        </div> -->
      </div>

      <!-- Item 2 -->
      <!-- <div class="relative group overflow-hidden rounded-lg hover:shadow-lg cursor-pointer">
        <img src="{{ asset('images/linen.jpg') }}" alt="Linen Pants" class="object-cover w-full h-[350px]">
        <div class="absolute bottom-0 left-0 right-0 bg-white bg-opacity-90 p-4">
          <p class="text-xs text-gray-500">02 — Man</p>
          <h3 class="text-lg font-semibold">Linen Pants</h3>
        </div>
      </div>
    </div> -->

  </div>
</section>


<!-- Gallery -->
<section class="p-8 text-center">
  <h2 class="text-2xl font-semibold mb-6">Share your preloved with us!<br>
    <span class="text-3xl font-bold text-yellow-500">#FunTHRYFT</span>
  </h2>

  <div class="grid grid-cols-2 md:grid-cols-6 gap-4 auto-rows-[150px] grid-flow-dense">

    <!-- Gambar 1 -->
    <div class="col-span-1 md:col-span-2 row-span-2 overflow-hidden rounded-lg">
      <img src="{{ asset('images/sport.jpg') }}" class="object-cover w-full h-full">
    </div>

    <!-- Gambar 2 -->
    <div class="col-span-1 overflow-hidden rounded-lg">
      <img src="{{ asset('images/capman.jpg') }}" class="object-cover w-full h-full">
    </div>

    <!-- Gambar 3 -->
    <div class="col-span-1 md:col-span-2 overflow-hidden rounded-lg">
      <img src="{{ asset('images/canon.jpg') }}" class="object-cover w-full h-full">
    </div>

    <!-- Gambar 4 -->
    <div class="col-span-1 overflow-hidden rounded-lg">
      <img src="{{ asset('images/belt.jpg') }}" class="object-cover w-full h-full">
    </div>

    <!-- Gambar 5 -->
    <div class="col-span-1 md:col-span-2 overflow-hidden rounded-lg">
      <img src="{{ asset('images/furniture.jpg') }}" class="object-cover w-full h-full">
    </div>

    <!-- Gambar 6 -->
    <div class="col-span-1 overflow-hidden rounded-lg">
      <img src="{{ asset('images/gadget.jpg') }}" class="object-cover w-full h-full">
    </div>

  </div>
</section>


@endsection

<script>
      document.addEventListener("DOMContentLoaded", function () {
        new Swiper(".mySwiper", {
          loop: true,
          spaceBetween: 20,
          slidesPerView: 1,
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          breakpoints: {
            768: {
              slidesPerView: 2,
            },
            1024: {
              slidesPerView: 3,
            },
          }
        });
      });

      // Alpine.js Carousel Function
      function carousel() {
        return {
          activeSlide: 0,
          startX: 0,
          endX: 0,
          slides: [
            {
              image: "{{ asset('images/sandal.jpg') }}",
              title: 'Langkah Nyaman, Gaya Berkelanjutan',
              subtitle: 'Sandal preloved dengan kualitas prima, nyaman dipakai dan ramah lingkungan.',
            },
            {
              image:"{{ asset('images/sun.jpg') }}",
              title: 'Gaya Cerah untuk Hari Cerah',
              subtitle: 'Kacamata hitam preloved, tampil gaya sambil jaga bumi.',
            },
            {
              image: "{{ asset('images/kursi.jpg') }}",
              title: 'Sentuhan Vintage untuk Rumahmu',
              subtitle: 'Kursi preloved penuh karakter, sempurna untuk sudut nyaman rumahmu.',
            },
          ],
          next() {
            this.activeSlide = (this.activeSlide + 1) % this.slides.length;
          },
          prev() {
            this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length;
          },
          goToShop() {
            window.location.href = "{{ route('shop') }}";
          },
          init() {
            setInterval(() => this.next(), 5000);
          },
          handleTouchStart(e) {
            this.startX = e.touches[0].clientX;
            e.preventDefault(); // cegah gesture default
          },
          handleTouchMove(e) {
            this.endX = e.touches[0].clientX;
            e.preventDefault(); // cegah gesture default
          },
          handleTouchEnd(e) {
            e.preventDefault(); // cegah gesture default
            let diffX = this.startX - this.endX;
            if (Math.abs(diffX) > 50) {
              if (diffX > 0) {
                this.next(); // geser ke kiri
              } else {
                this.prev(); // geser ke kanan
              }
            }
          }
        }
      }
    </script>