@extends('layouts.app')

@section('content')

<!-- Sign Up Section -->
<section class="relative flex justify-center items-center min-h-[calc(100vh-200px)] py-12 bg-cover bg-center" style="background-image: url('{{ asset('images/blur.jpg') }}');">
  <div class="w-full max-w-md px-8 py-10 bg-white rounded-lg shadow-md">
    <h1 class="text-3xl font-bold text-center mb-2">SIGN UP</h1>
    <p class="text-center text-gray-600 mb-8">Create Your Account</p>
    
    <form method="POST" action="{{ route('signup') }}">
      @csrf
      
      <div class="mb-4">
        <label for="name" class="block text-gray-700 font-medium mb-2">Full Name</label>
        <input 
          type="text" 
          id="name" 
          name="name" 
          placeholder="Enter your full name" 
          class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
          required
        >
      </div>
      
      <div class="mb-4">
        <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          placeholder="Enter your email address" 
          class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
          required
        >
      </div>

      <!-- Password with toggle eye -->
      <div class="mb-4">
        <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
        <div class="flex items-center border rounded-lg focus-within:ring-2 focus-within:ring-yellow-500">
          <input 
            type="password" 
            id="password" 
            name="password" 
            placeholder="Enter password (6-8 digit)" 
            class="w-full px-4 py-2 focus:outline-none rounded-l-lg"
            minlength="6"
            maxlength="8"
            required
          >
          <button 
            type="button" 
            id="togglePassword" 
            class="px-4 text-gray-500 hover:text-yellow-500 focus:outline-none"
          >
            <!-- Mata tertutup -->
            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5 0-9-3-9-7s4-7 9-7a9.955 9.955 0 014.59 1.042M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
            </svg>
            <!-- Mata terbuka -->
            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Confirm Password Field with Toggle -->
      <div class="mb-6">
        <label for="password_confirmation" class="block text-gray-700 font-medium mb-2">Confirm Password</label>
        <div class="flex items-center border rounded-lg focus-within:ring-2 focus-within:ring-yellow-500">
          <input 
            type="password" 
            id="password_confirmation" 
            name="password_confirmation" 
            placeholder="Re-enter your password" 
            class="w-full px-4 py-2 rounded-l-lg focus:outline-none"
            required
          >
          <button 
            type="button" 
            id="toggleConfirmPassword" 
            class="px-4 text-gray-500 hover:text-yellow-500 focus:outline-none"
          >
            <svg id="eyeClosedConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5 0-9-3-9-7s4-7 9-7a9.955 9.955 0 014.59 1.042M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
            </svg>
            <svg id="eyeOpenConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>
        </div>

        <!-- Pilihan jenis akun -->
<!-- <div class="mb-4">
  <label for="role" class="block text-gray-700 font-medium mb-2">Register as</label>
  <select name="role" id="role" required
          class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
    <option value="buyer">Buyer</option>
    <option value="seller">Seller</option>
  </select>
</div> -->

      </div>

      
      <button 
        type="submit" 
        class="w-full bg-yellow-500 text-white py-3 px-4 rounded-lg font-medium hover:bg-yellow-600 transition duration-300 mb-4"
      >
        SIGN UP
      </button>
      
      <p class="text-center text-gray-600">
        Already have an account? 
        <a href="{{ route('login') }}" class="text-yellow-500 hover:underline">Login</a>
      </p>
    </form>
  </div>
</section>

<script>
  const togglePassword = document.querySelector('#togglePassword');
  const passwordInput = document.querySelector('#password');
  const eyeClosed = document.querySelector('#eyeClosed');
  const eyeOpen = document.querySelector('#eyeOpen');

  togglePassword.addEventListener('click', function () {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    eyeClosed.classList.toggle('hidden');
    eyeOpen.classList.toggle('hidden');
  });

  // Tambahkan ini untuk confirm password toggle
  const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
  const confirmPasswordInput = document.querySelector('#password_confirmation');
  const eyeClosedConfirm = document.querySelector('#eyeClosedConfirm');
  const eyeOpenConfirm = document.querySelector('#eyeOpenConfirm');

  toggleConfirmPassword.addEventListener('click', function () {
    const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    confirmPasswordInput.setAttribute('type', type);
    eyeClosedConfirm.classList.toggle('hidden');
    eyeOpenConfirm.classList.toggle('hidden');
  });
</script>

@endsection
