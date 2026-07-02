@extends('admin.layouts.app')

@section('title', 'Booknest Admin - Order #' . $order->id)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/orders.css') }}?v=1.0.1">
@endsection

@section('content')
<div class="dashboard-wrapper-new">
    
    <div class="form-header-modern margin-bottom-20">
        <h2><i class="fa-solid fa-file-invoice-dollar"></i> Order Details #{{ $order->id }}</h2>
        <a href="{{ route('admin.orders.index') }}" class="btn-back-modern">
            <i class="fa-solid fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="order-details-grid">
        <!-- Left Column: Customer & Shipping Details -->
        <div>
            <!-- Customer Card -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fa-solid fa-user-circle color-brand-gold-font-size-1-25"></i>
                    <h4>Customer Profile</h4>
                </div>
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Name:</span>
                        <span class="info-value">{{ $order->customer?->name ?? 'Guest User' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $order->customer?->email ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">{{ $order->customer?->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Registered Status:</span>
                        <span class="info-value text-transform-capitalize">{{ $order->customer?->status ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Shipping Address Card -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fa-solid fa-truck color-brand-gold-font-size-1-25"></i>
                    <h4>Shipping Address</h4>
                </div>
                @if($order->shippingAddress)
                    <div class="info-list">
                        <div class="info-item">
                            <span class="info-label">Receiver Name:</span>
                            <span class="info-value">{{ $order->shippingAddress->receiver_name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">{{ $order->shippingAddress->phone_number }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email:</span>
                            <span class="info-value">{{ $order->shippingAddress->email }}</span>
                        </div>
                        <div class="info-item flex-column-align-start-gap-4">
                            <span class="info-label">Address Line:</span>
                            <span class="info-value text-align-left-font-weight-500-margin-top-2">{{ $order->shippingAddress->address_line }}</span>
                        </div>
                    </div>
                @else
                    <div class="table-empty-state padding-10">
                        ℹ️ No shipping details associated with this digital-only order.
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Order status and update form -->
        <div>
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fa-solid fa-gears color-brand-gold-font-size-1-25"></i>
                    <h4>Manage Status</h4>
                </div>
                
                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="status-select-form">
                    @csrf
                    
                    <div class="info-list margin-bottom-10">
                        <div class="info-item">
                            <span class="info-label">Order Total:</span>
                            <span class="info-value font-size-1-2-color-text-main">{{ number_format($order->total_amount) }} Ks</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Date Placed:</span>
                            <span class="info-value">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        @if($order->stripe_session_id)
                            <div class="info-item">
                                <span class="info-label">Stripe Session:</span>
                                <span class="info-value font-size-0-8-word-break">{{ $order->stripe_session_id }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="status-form-group">
                        <label for="payment_status">Payment Status</label>
                        <select name="payment_status" id="payment_status" class="status-select-control">
                            <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>

                    <div class="status-form-group">
                        <label for="status">Order Status</label>
                        <select name="status" id="status" class="status-select-control">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-update-status">
                        <i class="fa-solid fa-floppy-disk"></i> Update Status & Notify Customer
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Order items list -->
    <div class="data-table-card margin-top-10">
        <div class="detail-card-header">
            <i class="fa-solid fa-basket-shopping color-brand-gold-font-size-1-25"></i>
            <h4>Items Ordered</h4>
        </div>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                        <tr>
                            <td>
                                <img src="{{ $item->item->image ? asset('storage/' . $item->item->image) : asset('images/default-book.png') }}" alt="{{ $item->item->name }}" class="table-book-cover">
                            </td>
                            <td><strong>{{ $item->item->name }}</strong></td>
                            <td><span class="text-author">{{ $item->item->author?->name ?? 'Unknown Author' }}</span></td>
                            <td>{{ number_format($item->price) }} Ks</td>
                            <td>{{ $item->quantity }}</td>
                            <td><strong>{{ number_format($item->price * $item->quantity) }} Ks</strong></td>
                        </tr>
                    @endforeach
                    <tr class="bg-faf8f5">
                        <td colspan="4"></td>
                        <td class="text-align-right"><strong>Grand Total:</strong></td>
                        <td><strong class="font-size-1-1-color-text-main">{{ number_format($order->total_amount) }} Ks</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
