// In your paymentConfirmation.blade.php
@extends('layouts.app')

@section('content')
<div class="container">
    <!-- This will be hidden and only used to trigger the popups -->
    <div id="paymentFlow" style="display: none;"></div>
</div>

@if(session('payment_result'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // First popup - Processing
        Swal.fire({
            title: 'Memproses Pembayaran',
            html: '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-3">Transaksi sedang diproses...</p></div>',
            showConfirmButton: false,
            allowOutsideClick: false,
            timer: 3000, // Auto close after 3 seconds
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        }).then(() => {
            // After first popup closes, show success popup
            const result = @json(session('payment_result'));
            
            let html = `
                <div class="text-start">
                    <p class="mb-2">${result.message}</p>
                    ${result.method === 'virtual_account' && result.va_number ? `
                        <hr>
                        <p class="mb-1"><strong>Nomor VA:</strong></p>
                        <p class="h5 text-monospace">${result.va_number}</p>
                        <p class="mb-1"><strong>Bank:</strong></p>
                        <p class="h5">${result.bank.toUpperCase()}</p>
                    ` : ''}
                    <div class="alert alert-warning mt-3 mb-0">
                        <small>Ini hanya simulasi prototype</small>
                    </div>
                </div>
            `;
            
            Swal.fire({
                icon: 'success',
                title: 'Transaksi Berhasil',
                html: html,
                confirmButtonText: 'Kembali ke Beranda',
                customClass: {
                    popup: 'border-success'
                },
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            }).then(() => {
                window.location.href = "{{ route('home') }}";
            });
        });
    });
</script>
@endif

<!-- Include required libraries -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection