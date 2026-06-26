@extends('layouts.app')

@section('title', 'Order History - Booknest')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/store.css') }}?v=1.1.6">
@endsection

@section('content')
<div class="orders-page-wrapper container">
    <h1 class="orders-page-title"><i class="fa-solid fa-clock-rotate-left"></i> My Purchase History</h1>

    @if($orders->isNotEmpty())
        <div class="orders-list-premium">
            @foreach($orders as $order)
                <div class="order-premium-card">
                    <!-- Card Header: Order Metadata -->
                    <div class="order-card-header">
                        <div class="order-meta-info">
                            <span class="order-id-label">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                            <span class="order-date-label">
                                <i class="fa-regular fa-calendar-days"></i> {{ $order->created_at->format('M d, Y \a\t h:i A') }}
                            </span>
                        </div>
                        <div class="order-status-badges">
                            <!-- Order Status Badge -->
                            <span class="status-badge status-{{ $order->status }}">
                                <i class="fa-solid @if($order->status == 'pending') fa-hourglass-half @elseif($order->status == 'processing') fa-spinner @elseif($order->status == 'completed') fa-circle-check @else fa-circle-xmark @endif"></i>
                                {{ ucfirst($order->status) }}
                            </span>
                            <!-- Payment Status Badge -->
                            <span class="payment-badge payment-{{ $order->payment_status }}">
                                <i class="fa-solid {{ $order->payment_status == 'paid' ? 'fa-credit-card' : 'fa-receipt' }}"></i>
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Card Body: Grid Content -->
                    <div class="order-card-body">
                        <!-- Left: Itemized Books List -->
                        <div class="order-items-column">
                            <h4 class="section-sub-title"><i class="fa-solid fa-book-bookmark"></i> Ordered Books</h4>
                            <div class="order-items-list">
                                @foreach($order->orderItems as $item)
                                    <div class="order-item-row-new">
                                        <!-- Book Cover Thumbnail -->
                                        <div class="order-item-cover">
                                            <img src="{{ $item->item->image ? asset('storage/' . $item->item->image) : asset('images/default-book.png') }}" alt="{{ $item->item->name }}">
                                        </div>
                                        <!-- Book Details -->
                                        <div class="order-item-details">
                                            <h5 class="item-title">{{ $item->item->name }}</h5>
                                            <p class="item-author">By {{ $item->item->author?->name ?? 'Unknown Author' }}</p>
                                            <div class="item-price-qty">
                                                <span class="item-unit-price">{{ number_format($item->price) }} Ks</span>
                                                <span class="item-qty-badge">x{{ $item->quantity }}</span>
                                            </div>
                                        </div>
                                        <!-- Subtotal -->
                                        <div class="order-item-subtotal">
                                            {{ number_format($item->price * $item->quantity) }} Ks
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Right: Shipping / Delivery Info -->
                        <div class="order-shipping-column">
                            <div class="shipping-info-card">
                                <h4 class="shipping-title"><i class="fa-solid fa-truck-fast"></i> Delivery Information</h4>
                                @if($order->shippingAddress)
                                    <div class="shipping-detail-grid">
                                        <div class="shipping-detail-item">
                                            <span class="detail-label"><i class="fa-solid fa-user"></i> Receiver</span>
                                            <span class="detail-val">{{ $order->shippingAddress->receiver_name }}</span>
                                        </div>
                                        <div class="shipping-detail-item">
                                            <span class="detail-label"><i class="fa-solid fa-phone"></i> Phone</span>
                                            <span class="detail-val">{{ $order->shippingAddress->phone_number }}</span>
                                        </div>
                                        <div class="shipping-detail-item">
                                            <span class="detail-label"><i class="fa-solid fa-envelope"></i> Email</span>
                                            <span class="detail-val">{{ $order->shippingAddress->email }}</span>
                                        </div>
                                        <div class="shipping-detail-item">
                                            <span class="detail-label"><i class="fa-solid fa-location-dot"></i> Address</span>
                                            <span class="detail-val">{{ $order->shippingAddress->address_line }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="no-shipping-details">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        <p>No delivery details associated.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer: Total Summary -->
                    <div class="order-card-footer">
                        <div class="footer-summary-left">
                            Total Items: <span class="summary-qty">{{ $order->orderItems->sum('quantity') }} books</span>
                        </div>
                        <div class="footer-summary-right">
                            <span class="grand-total-lbl">Grand Total:</span>
                            <span class="grand-total-val">{{ number_format($order->total_amount) }} Ks</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-orders-state">
            <i class="fa-solid fa-folder-open"></i>
            <h3>No purchase history yet</h3>
            <p>Browse and buy your favorite books on Booknest.</p>
            <a href="{{ route('customer.store.home') }}" class="btn-primary">
                <i class="fa-solid fa-store"></i> Go to Bookstore
            </a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/customer/store.js') }}"></script>
@endsection
