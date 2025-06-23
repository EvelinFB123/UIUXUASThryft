@extends('layouts.app')

@section('content')

@php
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;
    use App\Models\OrderHistories;
    
    $user = $user ?? Auth::user();
    
    // Ambil data expense dari database
    $expenses = OrderHistories::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();

    // Hitung total pengeluaran dari order histories
    $totalExpenses = $expenses->sum(function($order) {
        return $order->price * $order->quantity;
    });
    
    // URL foto default
    $defaultProfilePhoto = 'https://bootdey.com/img/Content/avatar/avatar1.png';
    
    // URL foto profil user
    $cacheBust = session('cache_bust') ?? time();
    $profilePhoto = $defaultProfilePhoto . '?v=' . $cacheBust;
    
    if (!empty($user->profile_picture_url)) {
        $profilePhoto = asset('storage/profile_photos/' . $user->profile_picture_url) . '?v=' . $cacheBust;
    }
    
    // Ambil data order dari database
    $orders = OrderHistories::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();
@endphp

<div class="container light-style flex-grow-1 container-p-y">
    <h4 class="font-weight-bold py-4 mb-4" style="font-size: 1.5rem;">
        Account settings
    </h4>
    
    <!-- Alert messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any()))
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card overflow-hidden">
        <div class="row no-gutters row-bordered row-border-light" style="margin-top: 25px; margin-bottom: 25px">
            <div class="col-md-3 pt-0">
                <div class="list-group list-group-flush account-settings-links">
                    <a class="list-group-item list-group-item-action active" data-toggle="list"
                        href="#account-general">General</a>
                    <a class="list-group-item list-group-item-action" data-toggle="list"
                        href="#account-change-password">Change password</a>
                    <a class="list-group-item list-group-item-action" data-toggle="list"
                        href="#order-history">Order History</a>
                    <a class="list-group-item list-group-item-action" data-toggle="list"
                        href="#expense-history">Expense History</a>
                    <a href="/logout" id="logoutBtn" class="list-group-item list-group-item-action text-danger font-weight-bold"
                        href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                </div>
            </div>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>

            <div class="col-md-8">
                <div class="col-md-12"> <!-- Ubah menjadi col-md-12 untuk lebar penuh -->
                    <div class="tab-content">
                        <!-- Bagian General -->
                        <div class="tab-pane fade active show" id="account-general">
                            <!-- Form untuk mengupdate profil termasuk foto -->
                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="reset_photo_flag" id="reset_photo_flag" value="0">
                                
                                <!-- Enhanced greeting section -->
                                <div class="card-body text-left py-4">
                                    <h2 class="font-weight-bold mb-3 hello-text">
                                        Hello, {{ $user->name }}!
                                    </h2>
                                </div>
                                
                                <div class="card-body media align-items-center">
                                    <img src="{{ $profilePhoto }}" alt="Profile Photo" id="profilePhotoPreview"
                                        class="d-block ui-w-80" style="width:80px; height:80px; border-radius: 50%; object-fit: cover;">
                                    <div class="media-body ml-4">
                                        <label class="btn btn-outline-primary">
                                            Upload new photo
                                            <input type="file" name="profile_photo" id="profile_photo" 
                                                class="account-settings-fileinput" 
                                                style="position:absolute;visibility:hidden;width:1px;height:1px;opacity:0;"
                                                onchange="previewProfilePhoto(event)">
                                        </label> &nbsp;
                                        <button type="button" class="btn btn-default md-btn-flat" onclick="resetProfilePhoto()">Reset</button>
                                        <div class="text-light small mt-1">Allowed JPG, GIF or PNG. Max size of 2MB</div>
                                        
                                        @if($errors->has('profile_photo'))
                                            <div class="text-danger small mt-1">
                                                {{ $errors->first('profile_photo') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <hr class="border-light m-0">

                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control mb-1" name="username" id="username" 
                                            value="{{ old('username', $user->username) }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" id="name" 
                                            value="{{ old('name', $user->name) }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">E-mail</label>
                                        <input type="email" class="form-control mb-1" name="email" id="email" 
                                            value="{{ old('email', $user->email) }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Kota</label>
                                        <select class="custom-select" name="kota" id="kota">
                                            <option value="Surabaya" {{ old('kota', $user->kota) == 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
                                            <option value="Jakarta" {{ old('kota', $user->kota) == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                                            <option value="Bali" {{ old('kota', $user->kota) == 'Bali' ? 'selected' : '' }}>Bali</option>
                                            <option value="Bandung" {{ old('kota', $user->kota) == 'Bandung' ? 'selected' : '' }}>Bandung</option>
                                            <option value="Semarang" {{ old('kota', $user->kota) == 'Semarang' ? 'selected' : '' }}>Semarang</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Alamat</label>
                                        <textarea class="form-control" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Phone</label>
                                        <input type="text" class="form-control" name="phone" id="phone" 
                                            value="{{ old('phone', $user->phone) }}">
                                    </div>
                                </div>
                                <div class="text-right mt-3">
                                    <button type="submit" class="btn btn-primary">Save changes</button>&nbsp;
                                    <button type="button" class="btn btn-default" id="cancelButton">Cancel</button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Password Change Section -->
                        <div class="tab-pane fade" id="account-change-password">
                            <form method="POST" action="{{ route('profile.password.update') }}">
                                @csrf
                                @method('PUT')
                                <div class="card-body pb-2">
                                    <div class="form-group">
                                        <label class="form-label">Current password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="current_password" id="current_password" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text password-toggle" onclick="togglePassword('current_password')">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">New password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="password" id="new_password" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text password-toggle" onclick="togglePassword('new_password')">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Repeat new password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text password-toggle" onclick="togglePassword('password_confirmation')">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right mt-3">
                                        <button type="submit" class="btn btn-primary">Update Password</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Order History Section -->
                        <div class="tab-pane fade" id="order-history">
                            <div class="card-body pb-2">
                            <h6 class="mb-4">Order History</h6>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="filterDate">Filter by Order Date</label>
                                    <input type="date" id="filterDate" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="filterMonth">Filter by Month</label>
                                    <select id="filterMonth" class="form-control">
                                        <option value="">-- All Months --</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="filterYear">Filter by Year</label>
                                    <select id="filterYear" class="form-control">
                                        <option value="">-- All Years --</option>
                                        @php
                                            $years = $orders->pluck('created_at')->map(function($date) {
                                                return \Carbon\Carbon::parse($date)->format('Y');
                                            })->unique()->sort();
                                        @endphp
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                                        
                                <div class="alert alert-info mb-4">
                                    <h6 class="font-weight-bold">Payment Information</h6>
                                    <p class="mb-1">Buyer Name: {{ $user->name }}</p>
                                    @if($orders->isNotEmpty())
                                        @php
                                            $firstOrder = $orders->first();
                                            $cardNumber = $firstOrder->card_number ?? null;
                                        @endphp
                                        
                                        @if(!empty($cardNumber))
                                            <p class="mb-1">Card Number: **** **** **** {{ substr($cardNumber, -4) }}</p>
                                        @else
                                            <p class="mb-1">Card information not available</p>
                                        @endif
                                    @else
                                        <p class="mb-1">Card information not available</p>
                                    @endif
                                </div>

                                @if($orders->count() > 0)
                                    <div class="table-responsive">
                                        <table id="orderHistoryTable" class="table table-bordered table-hover w-100">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Order Date</th>
                                                    <th>Quantity</th>
                                                    <th>Price</th>
                                                    <th>Total</th>
                                                    <th>Payment Date</th>
                                                    <th>Shipping Method</th>
                                                    <th>Resi JNT</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($orders as $order)
                                                <tr>
                                                    <td>{{ $order->product_name }}</td>
                                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                                    <td>{{ $order->quantity }}</td>
                                                    <td>Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($order->price * $order->quantity, 0, ',', '.') }}</td>
                                                    <td>{{ $order->payment_date ? \Carbon\Carbon::parse($order->payment_date)->format('d M Y') : 'N/A' }}</td>
                                                    <td>{{ $order->shipping_method ?? 'N/A' }}</td>
                                                    <td>
                                                        @php
                                                            // Generate unique tracking number based on order ID and timestamp
                                                            $trackingPrefix = strtoupper(substr($order->shipping_method ?? 'SHIP', 0, 3));
                                                            $orderTimestamp = strtotime($order->created_at);
                                                            $uniqueId = substr(md5($order->id . $orderTimestamp), 0, 6);
                                                            $trackingNumber = $trackingPrefix . strtoupper($uniqueId);
                                                        @endphp
                                                        {{ $trackingNumber }}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <p class="mb-1">You haven't placed any orders yet</p>
                                        <p class="text-muted">Start shopping and your orders will appear here</p>
                                        <a href="{{ route('home') }}" class="btn btn-primary mt-3">Start Shopping</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Expense History Section -->
                        <div class="tab-pane fade" id="expense-history">
                            <div class="card-body pb-2">
                                <h6 class="mb-4">Expense History</h6>
                                
                                <!-- Expense Summary Cards -->
                                <div class="row mb-4">
                                    <div class="col-md-4 mb-3">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <p class="mb-1">Total Expenses</p>
                                                        <h3 class="mb-0">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</h3>
                                                    </div>
                                                    <div class="icon-circle">
                                                        <i class="fas fa-wallet fa-2x"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card bg-success text-white">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <p class="mb-1">Daily Average</p>
                                                        <h3 class="mb-0">Rp {{ number_format($expenses->count() ? $totalExpenses / $expenses->count() : 0, 0, ',', '.') }}</h3>
                                                    </div>
                                                    <div class="icon-circle">
                                                        <i class="fas fa-chart-line fa-2x"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card bg-info text-white">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <p class="mb-1">Transactions</p>
                                                        <h3 class="mb-0">{{ $expenses->count() }}</h3>
                                                    </div>
                                                    <div class="icon-circle">
                                                        <i class="fas fa-receipt fa-2x"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Expense Filters -->
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="expenseDate">Filter by Date</label>
                                        <input type="date" id="expenseDate" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="expenseMonth">Filter by Month</label>
                                        <select id="expenseMonth" class="form-control">
                                            <option value="">-- All Months --</option>
                                            @for ($m = 1; $m <= 12; $m++)
                                                <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="expenseYear">Filter by Year</label>
                                        <select id="expenseYear" class="form-control">
                                            <option value="">-- All Years --</option>
                                            @php
                                                $expenseYears = $expenses->pluck('created_at')->map(function($date) {
                                                    return \Carbon\Carbon::parse($date)->format('Y');
                                                })->unique()->sort();
                                            @endphp
                                            @foreach ($expenseYears as $year)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="expenseStartDate">Start Date</label>
                                        <input type="date" id="expenseStartDate" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="expenseEndDate">End Date</label>
                                        <input type="date" id="expenseEndDate" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <button class="btn btn-sm btn-primary mr-2" id="applyExpenseFilters">
                                        <i class="fas fa-filter mr-1"></i> Apply Filters
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" id="resetExpenseFilters">
                                        <i class="fas fa-sync-alt mr-1"></i> Reset Filters
                                    </button>
                                </div>
                                
                                <!-- Expense Table -->
                                @if($expenses->count() > 0)
                                    <div class="table-responsive">
                                        <table id="expenseHistoryTable" class="table table-bordered table-hover w-100">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Product</th>
                                                    <th>Quantity</th>
                                                    <th>Amount</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($expenses as $expense)
                                                <tr data-date="{{ $expense->created_at->format('Y-m-d') }}">
                                                    <td>{{ $expense->created_at->format('d M Y') }}</td>
                                                    <td>{{ $expense->product_name }}</td>
                                                    <td>{{ $expense->quantity }}</td>
                                                    <td class="text-right">Rp {{ number_format($expense->price * $expense->quantity, 0, ',', '.') }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#expenseDetailModal" 
                                                            data-date="{{ $expense->created_at->format('d M Y') }}"
                                                            data-product="{{ $expense->product_name }}"
                                                            data-quantity="{{ $expense->quantity }}"
                                                            data-amount="{{ $expense->price * $expense->quantity }}"
                                                            data-payment-method="{{ $expense->payment_method ?? 'Credit Card' }}">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="3" class="text-right">Total:</th>
                                                    <th class="text-right" id="expense-total">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                                        <p class="mb-1">No expense records found</p>
                                        <p class="text-muted">Your expenses will appear here once recorded</p>
                                    </div>
                                @endif
                                
                                <!-- Expense Chart dihapus dari sini -->
                            </div>
                        </div>
                    </div>  
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Expense Detail Modal -->
<div class="modal fade" id="expenseDetailModal" tabindex="-1" role="dialog" aria-labelledby="expenseDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expenseDetailModalLabel">Expense Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Date</label>
                    <p id="modal-date">-</p>
                </div>
                <div class="form-group">
                    <label>Product</label>
                    <p id="modal-product">-</p>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <p id="modal-quantity">-</p>
                </div>
                <div class="form-group">
                    <label>Amount</label>
                    <p class="text-success font-weight-bold" id="modal-amount">-</p>
                </div>
                <div class="form-group">
                    <label>Payment Method</label>
                    <p id="modal-payment-method">-</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Tentukan foto default
    const defaultProfilePhoto = "{{ $defaultProfilePhoto }}";
    
    /// Simpan nilai awal form saat halaman dimuat
    let initialFormState = {
        username: "{{ old('username', $user->username) }}",
        name: "{{ old('name', $user->name) }}",
        email: "{{ old('email', $user->email) }}",
        kota: "{{ old('kota', $user->kota) }}",
        address: "{{ old('address', $user->address) }}",
        phone: "{{ old('phone', $user->phone) }}",
        profilePhoto: "{{ $profilePhoto }}"
    };

    // Fungsi untuk reset foto profil ke default (template)
    function resetProfilePhoto() {
        const preview = document.getElementById('profilePhotoPreview');
        preview.src = defaultProfilePhoto;
        document.getElementById('profile_photo').value = '';
        document.getElementById('reset_photo_flag').value = '1';
        localStorage.removeItem('tempProfilePhoto');
    }

    // Fungsi untuk reset seluruh form ke nilai awal
    function resetFormToInitialState() {
        document.getElementById('username').value = initialFormState.username;
        document.getElementById('name').value = initialFormState.name;
        document.getElementById('email').value = initialFormState.email;
        document.getElementById('kota').value = initialFormState.kota;
        document.getElementById('address').value = initialFormState.address;
        document.getElementById('phone').value = initialFormState.phone;
        document.getElementById('reset_photo_flag').value = '0';
        
        // Reset foto profil ke nilai awal
        document.getElementById('profilePhotoPreview').src = initialFormState.profilePhoto;
        document.getElementById('profile_photo').value = '';
        localStorage.removeItem('tempProfilePhoto');
    }

    // Fungsi untuk preview foto profil yang dipilih
    function previewProfilePhoto(event) {
        if (event.target.files && event.target.files[0]) {
            const file = event.target.files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB
            
            if (file.size > maxSize) {
                alert('Ukuran file melebihi batas maksimal 2MB. Silakan pilih file yang lebih kecil.');
                event.target.value = '';
                return;
            }
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const preview = document.getElementById('profilePhotoPreview');
                preview.src = e.target.result;
                document.getElementById('reset_photo_flag').value = '0';
                localStorage.setItem('tempProfilePhoto', e.target.result);
            }
            
            reader.readAsDataURL(file);
        }
    }
    
    // Fungsi untuk toggle password visibility
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        const icon = input.nextElementSibling.querySelector('i');
        
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Handle temporary profile photo
        const tempPhoto = localStorage.getItem('tempProfilePhoto');
        if (tempPhoto) {
            document.getElementById('profilePhotoPreview').src = tempPhoto;
        }
        
        // 2. Cancel button handler
        document.getElementById('cancelButton').addEventListener('click', resetFormToInitialState);
        
        // 3. Logout confirmation
        document.getElementById('logoutBtn').addEventListener('click', function(event) {
            const confirmLogout = confirm('Are you sure you want to logout?');
            if (!confirmLogout) {
                event.preventDefault();
            } else {
                localStorage.removeItem('tempProfilePhoto');
            }
        });
        
        // 4. Clean up temporary photo
        window.addEventListener('beforeunload', function() {
            localStorage.removeItem('tempProfilePhoto');
        });
        
        // 5. Order history table filtering
        const dateInput = document.getElementById('filterDate');
        const monthSelect = document.getElementById('filterMonth');
        const yearSelect = document.getElementById('filterYear');
        const tableRows = document.querySelectorAll('#orderHistoryTable tbody tr');
        
        function filterOrders() {
            const selectedDate = dateInput.value;
            const selectedMonth = monthSelect.value;
            const selectedYear = yearSelect.value;
            let visibleRowCount = 0;

            tableRows.forEach(row => {
                const orderDateCell = row.cells[1];
                const orderDateStr = orderDateCell.textContent.trim();
                const orderDate = new Date(orderDateStr);

                let showRow = true;

                if (selectedDate) {
                    const selectedDateObj = new Date(selectedDate);
                    showRow = orderDate.toDateString() === selectedDateObj.toDateString();
                }

                if (selectedMonth) {
                    showRow = showRow && (orderDate.getMonth() + 1).toString() === selectedMonth;
                }

                if (selectedYear) {
                    showRow = showRow && orderDate.getFullYear().toString() === selectedYear;
                }

                if (showRow) {
                    row.style.display = '';
                    visibleRowCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Handle no data message
            const noDataMessage = document.getElementById('noDataMessage');
            const isFilterActive = selectedDate || selectedMonth || selectedYear;
            const noVisibleRows = visibleRowCount === 0;
            
            if (noVisibleRows && isFilterActive) {
                if (!noDataMessage) {
                    const tableBody = document.querySelector('#orderHistoryTable tbody');
                    const messageRow = document.createElement('tr');
                    messageRow.id = 'noDataMessage';
                    messageRow.innerHTML = `
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-search fa-2x text-muted mb-3"></i>
                            <p class="mb-1">No orders match your filters</p>
                            <p class="text-muted">Try adjusting your date or time filters</p>
                            <button class="btn btn-sm btn-outline-primary mt-2" onclick="resetFilters()">
                                Reset Filters
                            </button>
                        </td>
                    `;
                    tableBody.parentNode.insertBefore(messageRow, tableBody.nextSibling);
                }
            } else if (noDataMessage) {
                noDataMessage.remove();
            }
        }

        // Fungsi untuk reset filter
        window.resetFilters = function() {
            dateInput.value = '';
            monthSelect.value = '';
            yearSelect.value = '';
            filterOrders();
        }

        if (dateInput && monthSelect && yearSelect) {
            dateInput.addEventListener('change', filterOrders);
            monthSelect.addEventListener('change', filterOrders);
            yearSelect.addEventListener('change', filterOrders);
        }
        
        // 6. Auto-open order history tab
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('tab') === 'order-history') {
            document.querySelector('.list-group-item[href="#account-general"]').classList.remove('active');
            document.querySelector('#account-general').classList.remove('active', 'show');
            
            const historyTab = document.querySelector('.list-group-item[href="#order-history"]');
            const historyContent = document.querySelector('#order-history');
            
            if (historyTab && historyContent) {
                historyTab.classList.add('active');
                historyContent.classList.add('active', 'show');
            }
        }
        
        // 7. Expense History Functionality (tanpa chart)
        const expenseTable = document.getElementById('expenseHistoryTable');
        if (expenseTable) {
            const expenseRows = expenseTable.querySelectorAll('tbody tr');
            const expenseDateInput = document.getElementById('expenseDate');
            const expenseMonthSelect = document.getElementById('expenseMonth');
            const expenseYearSelect = document.getElementById('expenseYear');
            const expenseStartDate = document.getElementById('expenseStartDate');
            const expenseEndDate = document.getElementById('expenseEndDate');
            
            // Set default dates for range
            const today = new Date();
            expenseStartDate.value = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            expenseEndDate.value = today.toISOString().split('T')[0];
            
            // Apply filters button
            document.getElementById('applyExpenseFilters').addEventListener('click', filterExpenses);
            
            // Reset filters button
            document.getElementById('resetExpenseFilters').addEventListener('click', function() {
                expenseDateInput.value = '';
                expenseMonthSelect.value = '';
                expenseYearSelect.value = '';
                expenseStartDate.value = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                expenseEndDate.value = today.toISOString().split('T')[0];
                filterExpenses();
            });
            
            // Fungsi untuk filter expenses (tanpa chart)
            function filterExpenses() {
                const selectedDate = expenseDateInput.value;
                const selectedMonth = expenseMonthSelect.value;
                const selectedYear = expenseYearSelect.value;
                const startDate = expenseStartDate.value ? new Date(expenseStartDate.value) : null;
                const endDate = expenseEndDate.value ? new Date(expenseEndDate.value) : null;
                
                let visibleRowCount = 0;
                let totalAmount = 0;
                
                expenseRows.forEach(row => {
                    const expenseDate = new Date(row.getAttribute('data-date'));
                    const amountCell = row.cells[3];
                    const amount = parseFloat(amountCell.textContent.replace('Rp ', '').replace(/\./g, ''));
                    
                    let showRow = true;
                    
                    // Filter by exact date
                    if (selectedDate) {
                        const selectedDateObj = new Date(selectedDate);
                        showRow = expenseDate.toDateString() === selectedDateObj.toDateString();
                    }
                    
                    // Filter by month
                    if (selectedMonth && showRow) {
                        showRow = (expenseDate.getMonth() + 1).toString() === selectedMonth;
                    }
                    
                    // Filter by year
                    if (selectedYear && showRow) {
                        showRow = expenseDate.getFullYear().toString() === selectedYear;
                    }
                    
                    // Filter by date range
                    if (startDate && endDate && showRow) {
                        // Adjust end date to include entire day
                        const endOfDay = new Date(endDate);
                        endOfDay.setHours(23, 59, 59, 999);
                        showRow = expenseDate >= startDate && expenseDate <= endOfDay;
                    }
                    
                    if (showRow) {
                        row.style.display = '';
                        visibleRowCount++;
                        totalAmount += amount;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Update total in footer
                document.getElementById('expense-total').textContent = 'Rp ' + totalAmount.toLocaleString('id-ID');
                
                // Handle no data message
                const noDataMessage = document.getElementById('expenseNoDataMessage');
                const isFilterActive = selectedDate || selectedMonth || selectedYear || 
                                     (startDate && endDate);
                const noVisibleRows = visibleRowCount === 0;
                
                if (noVisibleRows && isFilterActive) {
                    if (!noDataMessage) {
                        const tableBody = expenseTable.querySelector('tbody');
                        const messageRow = document.createElement('tr');
                        messageRow.id = 'expenseNoDataMessage';
                        messageRow.innerHTML = `
                            <td colspan="5" class="text-center py-4">
                                <i class="fas fa-search fa-2x text-muted mb-3"></i>
                                <p class="mb-1">No expenses match your filters</p>
                                <p class="text-muted">Try adjusting your filters</p>
                            </td>
                        `;
                        tableBody.appendChild(messageRow);
                    }
                } else if (noDataMessage) {
                    noDataMessage.remove();
                }
            }
            
            // Initialize filtering
            filterExpenses();
        }
        
        // 8. Expense Detail Modal
        $('#expenseDetailModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const modal = $(this);
            
            modal.find('#modal-date').text(button.data('date'));
            modal.find('#modal-product').text(button.data('product'));
            modal.find('#modal-quantity').text(button.data('quantity'));
            modal.find('#modal-amount').text('Rp ' + button.data('amount').toLocaleString('id-ID'));
            modal.find('#modal-payment-method').text(button.data('payment-method'));
        });
    });
</script>

<style>
    /* Enhanced greeting style */
    .hello-text {
        font-size: 2rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
    }
    
    .password-toggle {
        cursor: pointer;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-left: none;
        border-radius: 0 0.25rem 0.25rem 0;
        padding: 0.375rem 0.75rem;
    }
    .table-sm th, .table-sm td {
        padding: 0.75rem;
        vertical-align: middle;
    }
    
    /* Full width tables */
    .table-responsive .table {
        width: 100% !important;
        margin-left: 0;
    }
    
    /* Remove left margin for expense history */
    #expense-history .card-body {
        padding-left: 0;
        padding-right: 0;
    }

    .alert-info {
        background-color: #e8f4ff;
        border-color: #b8daff;
        color: #004085;
    }
    
    .password-toggle:hover {
        background-color: #e9ecef;
    }
    
    .badge-success { background-color: #28a745; }
    .badge-warning { background-color: #ffc107; color: #212529; }
    .badge-danger { background-color: #dc3545; }
    .badge-secondary { background-color: #6c757d; }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.075);
    }
    
    .thead-light th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    /* Tambahan untuk preview foto */
    .ui-w-80 {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
        transition: transform 0.3s ease;
    }
    
    .ui-w-80:hover {
        transform: scale(1.05);
    }
    
    /* Style untuk tab aktif */
    .account-settings-links .list-group-item.active {
        background-color: #4361ee;
        color: white;
        border-color: #4361ee;
    }
    
    /* Perbaikan hover effect pada menu kiri */
    .account-settings-links .list-group-item:hover:not(.active) {
        background-color: #f0f2f5;
        color: #4361ee;
        transform: translateX(5px);
        transition: all 0.3s ease;
    }
    
    .account-settings-links .list-group-item.active:hover {
        background-color: #3a56d4;
    }
    
    /* Style untuk tombol */
    .btn-outline-primary {
        color: #4361ee;
        border-color: #4361ee;
    }
    
    .btn-outline-primary:hover {
        background-color: #4361ee;
        color: white;
    }
    
    .btn-primary {
        background-color: #4361ee;
        border-color: #4361ee;
    }
    
    .btn-primary:hover {
        background-color: #3a56d4;
        border-color: #3a56d4;
    }
    
    /* Style untuk form */
    .form-control:focus, .custom-select:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
    }
    
    /* Style untuk alert */
    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }
    
    /* Style untuk card */
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    /* Style untuk list group */
    .list-group-item {
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }
    
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    
    .list-group-item.text-danger:hover {
        background-color: #f8d7da;
        color: #dc3545;
    }
    
    /* Expense summary cards */
    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(255, 255, 255, 0.2);
    }
    
    /* Expense table */
    #expenseHistoryTable tbody tr {
        transition: all 0.2s ease;
    }
    
    #expenseHistoryTable tbody tr:hover {
        background-color: rgba(67, 97, 238, 0.05);
    }
    
    #expenseHistoryTable tfoot {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    
    /* Badges for categories */
    .badge-primary {
        background-color: #4361ee;
    }
    
    .badge-success {
        background-color: #28a745;
    }
    
    .badge-info {
        background-color: #17a2b8;
    }
    
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    /* Style untuk greeting */
    .card-header.bg-light {
        background-color: #f8f9fa !important;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection