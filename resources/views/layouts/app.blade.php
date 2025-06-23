<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Thryft')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JS (plus jQuery for tabs) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Swiper -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="font-sans text-gray-800 bg-gray-50">
    <!-- Navbar -->
    <nav class="flex items-center justify-between px-6 py-4 shadow-md bg-white sticky top-0 z-[9999]">
    <!-- Left: Logo -->
    <div class="text-3xl font-bold text-yellow-500 text-center md:text-left hover:cursor-pointer transition-transform hover:scale-105 md:col-span-1"
        onclick="window.location.href='{{ route('home') }}'">
        THRYFT
    </div>

    <!-- Middle: Navigation Links - Diperbaiki -->
    <div class="hidden md:flex flex-1 justify-center">
            <ul class="flex space-x-8 text-lg">
                @php
                    $navLinks = [
                        'home' => 'Home',
                        'shop' => 'Shop',
                        'aboutus' => 'About',
                        'categories' => 'Categories',
                    ];
                @endphp
                @foreach ($navLinks as $route => $label)
                <li>
                    <a href="{{ route($route) }}"
                       class="transition-colors hover:text-yellow-500 {{ request()->routeIs($route) ? 'text-yellow-500 font-semibold' : 'text-black' }}">
                        {{ $label }}
                    </a>
                </li>
                @endforeach
                @auth
                @if(auth()->user()->role === 'seller')
                    <li>
                        <a href="{{ route('myshop.index') }}"
                        class="transition-colors hover:text-yellow-500 {{ request()->routeIs('myshop.index') ? 'text-yellow-500 font-semibold' : 'text-black' }}">
                            My Shop
                        </a>
                    </li>
                @endif
            @endauth
            </ul>
        </div>

    <!-- Right: Icons -->
    <div class="flex items-center space-x-4">
        <!-- Desktop Icons -->
        <div class="hidden md:flex items-center space-x-4">

            <!-- Search -->
            <div x-data="{ openSearch: false }" class="relative group">
                <button @click="openSearch = !openSearch" class="p-2 hover:bg-gray-100 rounded-full">
                    <img src="{{ asset('images/search-interface-symbol.png') }}" alt="Search" class="w-6 h-6">
                </button>
                <form method="GET" action="{{ route('search') }}"
                      x-show="openSearch"
                      @click.outside="openSearch = false"
                      x-transition
                      class="absolute right-0 mt-2 bg-white p-2 shadow-md rounded z-10">
                    <input type="text" name="query" placeholder="Search products..."
                        class="border border-gray-300 rounded px-3 py-1 text-sm w-64" />
                </form>
                <div class="absolute top-full mt-1 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 hidden group-hover:block z-50">
                    Search
                </div>
            </div>

            <!-- AI Chatbot -->
            <a href="{{ route('ai') }}" 
            class="relative group p-2 rounded-full hover:bg-gray-100 transition {{ request()->routeIs('ai') ? 'bg-yellow-100 ring-2 ring-yellow-400' : '' }}">
            <img src="{{ asset('images/chat-bot.png') }}" alt="AI Chatbot" class="w-6 h-6">
            <div class="absolute top-full mt-1 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 hidden group-hover:block z-50">
                AI
            </div>
            </a>

            <!-- Cart -->
            @php
                $cartItems = session('cart', []);
                $totalQuantity = array_sum(array_column($cartItems, 'quantity'));
            @endphp
            <a href="{{ route('cart') }}" 
               class="relative group p-2 rounded-full hover:bg-gray-100 transition {{ request()->routeIs('cart') ? 'bg-yellow-100 ring-2 ring-yellow-400' : '' }}">
                <img src="{{ asset('images/shopping-cart.png') }}" alt="Cart" class="w-6 h-6">
                <span id="cart-count-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5 {{ $totalQuantity == 0 ? 'hidden' : '' }}">
                    {{ $totalQuantity > 99 ? '99+' : $totalQuantity }}
                </span>
                <div class="absolute top-full mt-1 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 hidden group-hover:block z-50">
                    Cart
                </div>
            </a>

            <!-- Chat -->
            <a href="{{ route('room') }}" 
               class="relative group p-2 rounded-full hover:bg-gray-100 transition {{ request()->routeIs('room') ? 'bg-yellow-100 ring-2 ring-yellow-400' : '' }}">
                <img src="{{ asset('images/chat.png') }}" alt="Chat" class="w-6 h-6">
                <div class="absolute top-full mt-1 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 hidden group-hover:block z-50">
                    Chat
                </div>
            </a>

            <!-- User Profile (Diperbarui) -->
            <a href="{{ auth()->check() ? route('profile.index') : route('login') }}"
                   class="relative group p-2 rounded-full hover:bg-gray-100 transition {{ request()->routeIs('profile') || request()->routeIs('login') ? 'bg-yellow-100 ring-2 ring-yellow-400' : '' }}">
                    @if(auth()->check() && !empty(auth()->user()->profile_picture_url))
                        <!-- Tampilkan foto profil dalam bentuk lingkaran -->
                        <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-white shadow-md">
                            <img src="{{ asset('storage/profile_photos/' . auth()->user()->profile_picture_url) }}" 
                                 alt="Profile Photo" 
                                 class="w-full h-full object-cover">
                        </div>
                    @else
                        <!-- Tampilkan ikon user default jika tidak ada foto -->
                        <img src="{{ asset('images/user.png') }}" alt="User" class="w-6 h-6">
                    @endif
                    
                    <div class="absolute top-full mt-1 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 hidden group-hover:block z-50">
                        {{ auth()->check() ? 'Profile' : 'Login' }}
                    </div>
                </a>
            </div>

        <!-- Mobile Icons -->
        <div class="flex md:hidden items-center space-x-4">
            <!-- Search (mobile) -->
            <div x-data="{ open: false }" class="relative group">
                <button @click="open = !open" class="p-2 hover:bg-gray-100 rounded-full">
                    <img src="{{ asset('images/search-interface-symbol.png') }}" alt="Search" class="w-6 h-6">
                </button>
                <form method="GET" action="{{ route('search') }}"
                      x-show="open"
                      @click.outside="open = false"
                      x-transition
                      class="absolute right-0 mt-2 bg-white p-2 shadow-md rounded z-10">
                    <input type="text" name="query" placeholder="Search products..."
                           class="border border-gray-300 rounded px-3 py-1 text-sm w-64" />
                </form>
                <div class="absolute top-full mt-1 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 hidden group-hover:block z-50">
                    Search
                </div>
            </div>
        
        <!-- User Profile Mobile (Diperbarui) -->
        <a href="{{ auth()->check() ? route('profile.index') : route('login') }}"
                   class="relative group p-2 rounded-full hover:bg-gray-100 transition">
                    @if(auth()->check() && !empty(auth()->user()->profile_picture_url))
                        <!-- Tampilkan foto profil dalam bentuk lingkaran -->
                        <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-white shadow-md">
                            <img src="{{ asset('storage/profile_photos/' . auth()->user()->profile_picture_url) }}" 
                                 alt="Profile Photo" 
                                 class="w-full h-full object-cover">
                        </div>
                    @else
                        <!-- Tampilkan ikon user default jika tidak ada foto -->
                        <img src="{{ asset('images/user.png') }}" alt="User" class="w-6 h-6">
                    @endif
                </a>

            <!-- Hamburger Menu -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="p-2 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-4 top-16 w-48 bg-white shadow-lg rounded-md py-2 z-50">
                    <a href="{{ route('home') }}" class="block px-4 py-2 hover:bg-gray-100">Home</a>
                    <a href="{{ route('shop') }}" class="block px-4 py-2 hover:bg-gray-100">Shop</a>
                    <a href="{{ route('aboutus') }}" class="block px-4 py-2 hover:bg-gray-100">About</a>
                    <a href="{{ route('categories') }}" class="block px-4 py-2 hover:bg-gray-100">Categories</a>
                    <a href="{{ route('room') }}" class="block px-4 py-2 hover:bg-gray-100">Chat</a>
                    <a href="{{ route('cart') }}" class="block px-4 py-2 hover:bg-gray-100">Cart</a>
                     @auth
                        @if(auth()->user()->role === 'seller')
                            <a href="{{ route('myshop.index') }}" class="block px-4 py-2 hover:bg-gray-100">My Shop</a>
                        @endif
                    @endauth
                    <a href="{{ auth()->check() ? route('profile.index') : route('login') }}"
                       class="block px-4 py-2 hover:bg-gray-100">
                        {{ auth()->check() ? 'Profile' : 'Login' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>


    <!-- Footer -->
    @if(!request()->is('chat*'))
    <footer class="bg-white border-t mt-12">
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-4 md:mb-0">
                    <div class="text-3xl font-bold text-yellow-500 mb-2">THRYFT</div>
                    <p class="text-gray-600">©️ {{ date('Y') }} All rights reserved</p>
                </div>
                <div class="flex space-x-6">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-yellow-500">Home</a>
                    <a href="{{ route('shop') }}" class="text-gray-600 hover:text-yellow-500">Shop</a>
                    <a href="https://wa.me/6287862052143" target="_blank" class="text-gray-600 hover:text-yellow-500">Contact</a>
                </div>
            </div>
            <div class="border-t mt-6 pt-4 text-center text-sm text-gray-500">
                Sustainable thrifting platform • Based in Jakarta, Indonesia
            </div>
        </div>
    </footer>
    @endif

    @yield('scripts')
</body>
</html>