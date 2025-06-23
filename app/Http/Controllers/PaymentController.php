<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderHistories;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class PaymentController extends controller
{
    public function show(Request $request)
    {
        $request->validate([
            'selected_products' => 'required|array|min:1'
        ]);

        $selectedIds = $request->input('selected_products', []);
        $cartItems = session('cart', []);
        
        $selectedItems = [];
        $total = 0;
        
        foreach ($selectedIds as $id) {
            $product = Product::withTrashed()->find($id);
            
            if ($product && isset($cartItems[$id])) {
                $selectedItems[$id] = [
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $cartItems[$id]['quantity']
                ];
                $total += $product->price * $cartItems[$id]['quantity'];
            }
        }
        
        if (empty($selectedItems)) {
            return redirect()->route('cart')
                ->with('error', 'Produk tidak ditemukan dalam keranjang.')
                ->withInput();
        }
        
        // Store in session for confirmation
        session([
            'selectedItems' => $selectedItems,
            'total' => $total
        ]);
        
        return view('payment', compact('selectedItems', 'total'));
    }

    public function process(Request $request)
    {
        try {
            $user = Auth::user();
            $paymentMethod = $request->input('payment_method', 'debit_credit');
            
            // Validate address
            if (empty($user->address)) {
                return redirect()->route('payment.show')->withErrors(['address' => 'Please complete your address first']);
            }
            
            // Conditional validation rules
            $rules = [
                'shipping_option' => 'required|in:hemat,reguler',
                'selected_products' => 'required|array|min:1',
            ];
            
            if ($paymentMethod === 'virtual_account') {
                $rules['va_bank'] = 'required|in:bca,bni,bri,mandiri';
            }
            
            if ($paymentMethod === 'debit_credit') {
                $rules['card_name'] = 'required|string|max:255';
                $rules['card_number'] = 'required|digits_between:13,19';
                $rules['expiry'] = ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'];
                $rules['cvv'] = 'required|digits:3';
            }
            
            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                return redirect()->route('payment.show')
                    ->withErrors($validator)
                    ->withInput();
            }

            $validated = $validator->validated();

            $cartItems = session('cart', []);
            $selectedIds = $request->input('selected_products', []);
            
            // Generate VA number
            $vaNumber = ($paymentMethod === 'virtual_account') 
                ? 'VA' . strtoupper(Str::random(3)) . '-' . mt_rand(1000, 9999) . '-' . mt_rand(1000, 9999)
                : null;

            DB::beginTransaction();

            try {
                foreach ($selectedIds as $id) {
                    $product = Product::withTrashed()->find($id);
                    $item = $cartItems[$id];
                    
                    OrderHistories::create([
                        'user_id' => auth()->id(),
                        'product_id' => $id,
                        'product_name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'shipping_method' => $validated['shipping_option'],
                        'payment_method' => $paymentMethod,
                        'payment_date' => now(),
                        'buyer_name' => ($paymentMethod === 'debit_credit') ? $validated['card_name'] : 'VA Customer',
                        'card_number' => ($paymentMethod === 'debit_credit') ? $validated['card_number'] : 'N/A',
                        'virtual_account' => $vaNumber,
                        'va_bank' => ($paymentMethod === 'virtual_account') ? $validated['va_bank'] : null,
                    ]);

                    // Remove from cart
                    unset($cartItems[$id]);
                }

                session(['cart' => $cartItems]);
                DB::commit();

                session([
                    'payment_result' => [
                        'status' => 'success',
                        'message' => $paymentMethod === 'virtual_account'
                            ? 'Virtual account berhasil dibuat (PROTOTYPE)'
                            : 'Pembayaran berhasil (PROTOTYPE)',
                        'method' => $paymentMethod,
                        'va_number' => $vaNumber,
                        'bank' => ($paymentMethod === 'virtual_account') ? $validated['va_bank'] : null
                    ]
                ]);

                return redirect()->route('home');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('Payment error: ' . $e->getMessage());
            
            // PROTOTYPE-ONLY: Even errors will show as success
            session([
                'payment_result' => [
                    'status' => 'success',
                    'message' => 'Simulasi pembayaran berhasil (PROTOTYPE)',
                    'method' => 'debit_credit'
                ]
            ]);
            
            return redirect()->route('home');
        }
    }

    // Di PaymentController.php
// public function showConfirmation()
// {
//     if (!session()->has('payment_result')) {
//         return redirect()->route('home')->with('error', 'Sesi pembayaran tidak valid');
//     }
    
//     return view('paymentConfirmation');
// }
}