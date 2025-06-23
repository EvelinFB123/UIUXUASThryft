<?php

namespace App\Http\Controllers;

use App\Models\OrderHistories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // Ambil data order history dari database
        $orders = OrderHistories::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('profile', compact('user', 'orders'));
    }

    public function update(Request $request)
{
    $user = Auth::user();
    Log::info('📤 Mulai update profile', ['user_id' => $user->id]);

    // Tambahkan validasi untuk address
    $validator = Validator::make($request->all(), [
        'username' => 'required|string|max:255',
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'kota' => 'nullable|string',
        'address' => 'nullable|string|max:255', // Tambahkan validasi untuk address
        'phone' => 'nullable|string|max:20',
        'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ], [
        'profile_photo.image' => 'File must be an image (jpeg, png, jpg, or gif)',
        'profile_photo.mimes' => 'Allowed formats: jpeg, png, jpg, gif',
        'profile_photo.max' => 'File size must not exceed 2MB'
    ]);

    if ($validator->fails()) {
        Log::warning('❌ Validasi gagal', $validator->errors()->toArray());
        return back()->withErrors($validator)->withInput();
    }

    // Reset foto jika diminta
    if ($request->reset_photo_flag == '1') {
        if ($user->profile_picture_url && Storage::exists('public/profile_photos/' . $user->profile_picture_url)) {
            Storage::delete('public/profile_photos/' . $user->profile_picture_url);
            Log::info('🧹 Foto lama dihapus karena reset');
        }
        $user->profile_picture_url = null;
    }

    // Upload foto baru jika ada
    elseif ($request->hasFile('profile_photo')) {
        $file = $request->file('profile_photo');
        $filename = time() . '.' . $file->getClientOriginalExtension();

        // Pastikan direktori penyimpanan ada
        $destinationPath = storage_path('app/public/profile_photos');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Hapus foto lama jika ada
        if ($user->profile_picture_url && file_exists($destinationPath . '/' . $user->profile_picture_url)) {
            unlink($destinationPath . '/' . $user->profile_picture_url);
            Log::info('🗑️ Foto lama dihapus sebelum simpan baru');
        }

        try {
            $file->move($destinationPath, $filename);
            Log::info('✅ Foto berhasil diupload', [
                'filename' => $filename,
                'path' => 'storage/profile_photos/' . $filename
            ]);
            $user->profile_picture_url = $filename;
        } catch (\Exception $e) {
            Log::error('❌ Gagal menyimpan file foto', ['error' => $e->getMessage()]);
            return back()->withErrors(['profile_photo' => 'Gagal menyimpan foto.'])->withInput();
        }
    }

    // Update data user termasuk address
    $user->username = $request->username;
    $user->name = $request->name;
    $user->email = $request->email;
    $user->kota = $request->kota;
    $user->address = $request->address; // Tambahkan baris ini
    $user->phone = $request->phone;
    $user->save();

    Log::info('✅ Profile berhasil diupdate', ['user_id' => $user->id]);

    return back()
        ->with('success', 'Profile updated successfully')
        ->with('cache_bust', time()); // Hindari cache pada foto
}

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully');
    }

    public function updateNotifications(Request $request)
    {
        auth()->user()->update([
            'email_comments' => $request->has('email_comments'),
            'email_forum' => $request->has('email_forum'),
            'email_follows' => $request->has('email_follows'),
            'email_news' => $request->has('email_news'),
            'email_updates' => $request->has('email_updates'),
            'email_blog' => $request->has('email_blog'),
        ]);

        return back()->with('success', 'Notification settings updated');
    }

    public function index()
    {
        $user = Auth::user();
        
        // Ambil data order history dari database
        $orders = OrderHistories::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('profile', compact('user', 'orders'));
    }
}