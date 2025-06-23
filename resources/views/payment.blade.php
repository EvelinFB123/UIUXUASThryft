@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold text-yellow-500 text-center mx-auto mb-10">Payment Confirmation</h2>

    {{-- Flash messages --}}
    @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Payment Successful',
                text: "{{ session('success') }}",
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = "{{ route('home') }}";
            });
        </script>
    @endif

    @if(session('va_success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const bankNames = {
                    'bca': 'BCA',
                    'bni': 'BNI',
                    'bri': 'BRI',
                    'mandiri': 'Mandiri'
                };
                
                Swal.fire({
                    icon: 'success',
                    title: 'Virtual Account Created!',
                    html: `
                        <p>{{ session('va_success.message') }}</p>
                        <p class="mt-3 font-medium">Bank: ${bankNames["{{ session('va_success.bank') }}"]}</p>
                        <p class="font-medium">Your Virtual Account Number:</p>
                        <p class="text-xl font-bold text-gray-800">{{ session('va_success.va_number') }}</p>
                        <p class="mt-3">Please complete the payment within 24 hours</p>
                    `,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = "{{ route('home') }}";
                });
            });
        </script>
    @endif
    
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $cartItems = $selectedItems ?? [];
        $total = $total ?? 0;
        $addressFilled = !empty(auth()->user()->address);
        $shippingCost = session('shipping_cost', 5000);
        $totalCost = $total + $shippingCost;
    @endphp

    <!-- Order Summary -->
    <div class="mb-6">
        <h3 class="text-xl font-semibold text-gray-700 mt-5 mb-3">Order Summary</h3>
        <div class="bg-gray-200 p-4 rounded">
            <p><span class="font-semibold">Products:</span></p>
            <ul class="list-disc list-inside text-gray-700 mb-3">
                @foreach ($cartItems as $id => $item)
                    <li>{{ $item['name'] }} - Rp. {{ number_format($item['price'], 0, ',', '.') }}</li>
                @endforeach
            </ul>

            <p><span class="font-semibold">Price: </span>Rp. {{ number_format($total, 0, ',', '.') }}</p>

            @if($addressFilled)
                <p><span class="font-semibold">Shipping: </span>Rp. <span id="shipping-cost">{{ number_format($shippingCost, 0, ',', '.') }}</span></p>
                <p class="mt-3 font-bold text-gray-800">Total: Rp. <span id="total-cost">{{ number_format($totalCost, 0, ',', '.') }}</span></p>
            @else
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded mt-3">
                    Please complete your address in <a href="{{ route('profile.index') }}" class="underline text-blue-600">Profile Settings</a> before checking out.
                </div>
            @endif
        </div>
    </div>

    <!-- Shipping Options -->
    @if($addressFilled)
        <div class="mb-6">
            <h3 class="text-xl font-semibold text-gray-700 mb-3">Shipping Options</h3>
            <div class="space-y-2">
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="shipping_option" value="hemat" data-cost="5000" class="mr-2" checked>
                    <span>Hemat: Rp. 5,000 (Delivery: {{ \Carbon\Carbon::now()->addDays(5)->format('d M Y') }})</span>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="shipping_option" value="reguler" data-cost="10000" class="mr-2">
                    <span>Reguler: Rp. 10,000 (Delivery: {{ \Carbon\Carbon::now()->addDays(2)->format('d M Y') }})</span>
                </label>
            </div>
        </div>
    @endif

    <!-- Payment Form -->
    <form id="paymentForm" action="{{ route('payment.process') }}" method="POST" class="space-y-4">
        @csrf

        <!-- Hidden input shipping option -->
        <input type="hidden" name="shipping_option" id="shipping_option" value="hemat">

        <!-- Hidden input for selected product IDs -->
        @foreach ($cartItems as $id => $item)
            <input type="hidden" name="selected_products[]" value="{{ $id }}">
        @endforeach

        <!-- Payment Options -->
        <div class="mb-6">
            <h3 class="text-xl font-semibold text-gray-700 mb-3">Payment Method</h3>
            <div class="space-y-2">
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="payment_method" value="debit_credit" class="mr-2" checked>
                    <span>Credit / Debit Card</span>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="payment_method" value="virtual_account" class="mr-2">
                    <span>Virtual Account (VA)</span>
                </label>
            </div>
            
            <!-- VA Bank Selection -->
            <div id="vaBankOptions" class="hidden mt-4 ml-6 space-y-2">
                <h4 class="font-medium text-gray-700 mb-2">Select Bank:</h4>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="va_bank" value="bca" class="mr-2" checked>
                        <span>BCA</span>
                    </label>
                    <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="va_bank" value="bni" class="mr-2">
                        <span>BNI</span>
                    </label>
                    <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="va_bank" value="bri" class="mr-2">
                        <span>BRI</span>
                    </label>
                    <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="va_bank" value="mandiri" class="mr-2">
                        <span>Mandiri</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Credit/Debit Card Payment Form -->
        <div id="cardSection" style="display: hidden">
            <div>
                <label class="block text-sm font-medium text-gray-700">Buyer's name on Credit/Debit Card</label>
                <input type="text" name="card_name" class="w-full mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Card Number</label>
                <input type="text" name="card_number" placeholder="13-19 digit" pattern="\d{13,19}" title="Harus 13-19 digit angka" class="w-full mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" />
            </div>

            <div class="flex space-x-4">
                <div class="w-1/2">
                    <label class="block text-sm font-medium text-gray-700">Expiry Date</label>
                    <input type="text" name="expiry" placeholder="MM/YY" pattern="^(0[1-9]|1[0-2])\/\d{2}$" title="Format MM/YY" class="w-full mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" />
                </div>
                <div class="w-1/2">
                    <label class="block text-sm font-medium text-gray-700">CVV</label>
                    <input type="text" name="cvv" placeholder="last 3 digit" pattern="\d{3}" title="3 digit angka terakhir" class="w-full mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" />
                </div>
            </div>
        </div>

        <!-- Virtual Account Section -->
<div id="vaSection" class="hidden bg-gray-100 border border-gray-300 p-4 rounded">
  <p class="font-medium text-gray-700 mb-2">Your Virtual Account Number:</p>
  <div class="flex items-center space-x-2">
    <p class="text-xl font-bold text-gray-800 select-all" id="vaNumberDisplay">
      <!-- VA number tampil di sini -->
    </p>
    <button type="button" onclick="copyVA()" class="text-yellow-500 hover:text-yellow-700 text-sm">
      Copy
    </button>
  </div>
  <p class="mt-2 text-gray-600">Bank: <span id="vaBankDisplay"></span></p>
</div>


        <button type="submit" id="submitBtn"
            class="w-full bg-black text-white py-3 px-4 rounded font-medium transition duration-300 hover:bg-yellow-500 block text-center mt-6 {{ !$addressFilled ? 'opacity-50 cursor-not-allowed' : '' }}"
            {{ !$addressFilled ? 'disabled' : '' }}>
            Pay
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Shipping Option Update
    const shippingRadios = document.querySelectorAll('input[name="shipping_option"]');
    const shippingCostDisplay = document.getElementById('shipping-cost');
    const totalCostDisplay = document.getElementById('total-cost');
    const productPrice = {!! json_encode($total) !!};
    const hiddenShippingInput = document.getElementById('shipping_option');

    function updateShippingCost() {
    const selectedShipping = document.querySelector('input[name="shipping_option"]:checked');

    if (!selectedShipping) {
        // Tidak ada yang dipilih, keluar dari fungsi
        console.warn('No shipping option selected.');
        return;
    }

    const shipping = parseInt(selectedShipping.dataset.cost);
    const total = productPrice + shipping;

    shippingCostDisplay.textContent = shipping.toLocaleString('id-ID');
    totalCostDisplay.textContent = total.toLocaleString('id-ID');
    hiddenShippingInput.value = selectedShipping.value;
}


    shippingRadios.forEach(radio => {
        radio.addEventListener('change', updateShippingCost);
    });

    // Initialize shipping cost
    updateShippingCost();

    // Payment Method Toggle
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const cardSection = document.getElementById('cardSection');
    const vaSection = document.getElementById('vaSection');
    const vaBankOptions = document.getElementById('vaBankOptions');

    // Bank names for display
    const bankNames = {
        'bca': 'BCA',
        'bni': 'BNI',
        'bri': 'BRI',
        'mandiri': 'Mandiri'
    };

    function togglePaymentSections() {
        console.log('Toggle payment triggered');
        const selectedPayment = document.querySelector('input[name="payment_method"]:checked')?.value;
        

        if (selectedPayment === 'debit_credit') {
            cardSection.style.display = 'block';
            vaSection.classList.add('hidden');
            vaBankOptions.classList.add('hidden');
        } else if (selectedPayment === 'virtual_account') {
            cardSection.style.display = 'none';
            vaSection.classList.remove('hidden');
            vaBankOptions.classList.remove('hidden');

            const selectedBank = document.querySelector('input[name="va_bank"]:checked')?.value || 'bca';

            const vaNumber = 'VA-' + Math.floor(Math.random() * 10000000000000000).toString().padStart(16, '0');
            document.getElementById('vaNumberDisplay').textContent = vaNumber;
            document.getElementById('vaBankDisplay').textContent = bankNames[selectedBank];
        }
    }


    // Listen for payment method changes
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', togglePaymentSections);
    });

    // Listen for bank selection changes
    document.querySelectorAll('input[name="va_bank"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const selectedBank = this.value;
            document.getElementById('vaBankDisplay').textContent = bankNames[selectedBank];
        });
    });

    // Initialize payment sections
    togglePaymentSections();

    @if(!$addressFilled)
        // Jika address kosong, munculkan alert dan redirect ke profile
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'warning',
                title: 'Address Required!',
                text: 'Please complete your shipping address in Profile Settings before proceeding to payment.',
                confirmButtonText: 'Go to Profile'
            }).then(() => {
                window.location.href = "{{ route('profile.index') }}";
            });
        });
    @endif

 // Form Submission (tanpa fetch async)
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Processing...';

    // Tampilkan loading popup
    Swal.fire({
        title: 'Processing Payment...',
        text: 'Please wait while we process your transaction.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Setelah 2 detik tampilkan popup completed
    setTimeout(() => {
        Swal.fire({
            title: 'Payment Completed',
            text: 'Thank you for using our platform',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            // Setelah user klik OK, submit form
            this.submit();
        });
    }, 2000);
});

  
    function copyVA() {
    const vaNumber = document.getElementById('vaNumberDisplay').textContent;
    navigator.clipboard.writeText(vaNumber).then(() => {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'VA Number copied!',
        showConfirmButton: false,
        timer: 1500
      });
    });
  }


</script>
@endsection