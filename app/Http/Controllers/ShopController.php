<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Auth\AuthenticationException;


class ShopController extends Controller
{

    public function __construct()
    {
        // Middleware auth hanya di method addToCart dan buyNow
        $this->middleware('auth')->only(['addToCart', 'buyNow']);
    }

    public function index(Request $request)
    {
        $categories = Category::all();
        $products = Product::with('category')
            ->where('is_active', true) // Hanya produk aktif
            ->when($request->category, function ($query) use ($request) {
                $query->where('category_id', $request->category);
            })
            ->paginate(16);

        return view('shop', compact('products', 'categories'));
    }

    public function addToCart($id)
    {
        $product = Product::where('is_active', true)
                ->findOrFail($id);
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);
    
        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image ?? 'default.png',
                'quantity' => 1,
            ];
        }
    
        session()->put('cart', $cart);
    
        return redirect()->back()->with('success', 'Product added to cart!');
    }

    public function buyNow($id)
    {
        $product = Product::where('is_active', true)
                ->findOrFail($id);
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);
    
        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image ?? 'default.png',
                'quantity' => 1,
            ];
        }
    
        session()->put('cart', $cart);
    
        return redirect()->route('cart')->with('success', 'Product added to cart!');
    }

    public function ajaxRemove($id)
{
    $cart = session('cart', []);
    
    if (array_key_exists($id, $cart)) {
        unset($cart[$id]);
        session(['cart' => $cart]);
    }

    // Hitung total baru
    $total = array_sum(array_map(function ($item) {
        return $item['price'] * $item['quantity'];
    }, $cart));

    return response()->json([
        'success' => true,
        'new_total' => $total,
        'cart_count' => count($cart)
    ]);
}


public function cart()
{
    $cart = session()->get('cart', []);
    $invalidItems = [];

    // Filter item yang tidak valid (produk dihapus atau tidak aktif)
    foreach ($cart as $id => $item) {
        $product = Product::withTrashed()->find($id);
        
        if (!$product || $product->trashed() || !$product->is_active) {
            $invalidItems[] = $id;
            continue;
        }
        
        if (!isset($item['id'])) {
            $cart[$id]['id'] = $id;
        }
    }

    // Hapus item tidak valid dari keranjang
    foreach ($invalidItems as $id) {
        unset($cart[$id]);
    }

    session()->put('cart', $cart);

    return view('cart', compact('cart'));
}

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }

        return redirect()->guest(route('login'))->with('error', 'Silakan login terlebih dahulu untuk melanjutkan.');
    }

    public function updateCart(Request $request)
{
    $cart = session('cart', []);
    $id = $request->input('id');
    $quantity = $request->input('quantity');

    if ($quantity < 1) {
        return response()->json([
            'success' => false,
            'message' => 'Quantity must be at least 1'
        ]);
    }

    if (isset($cart[$id])) {
        $cart[$id]['quantity'] = $quantity;
        session(['cart' => $cart]);
        
        $itemTotal = $cart[$id]['price'] * $quantity;
        $cartTotal = array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));
        
        return response()->json([
            'success' => true,
            'item_total' => $itemTotal,
            'cart_total' => $cartTotal
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Product not found in cart'
    ]);
}

}
