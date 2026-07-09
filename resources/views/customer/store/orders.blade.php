@extends('layouts.app')

@section('title', 'Order History - Booknest')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/store.css') }}?v=1.4.3">
@endsection

@section('content')
<div class="orders-page-wrapper container">
    <h1 class="orders-page-title"><i class="fa-solid fa-clock-rotate-left"></i> My Purchase History</h1>

    <!-- Date Filter Bar -->
    <div class="orders-filter-bar">
        <form action="{{ route('customer.store.orders') }}" method="GET" class="filter-form">
            <div class="filter-wrapper">
                <span class="filter-title"><i class="fa-solid fa-filter"></i> Filter:</span>
                <div class="filter-inputs">
                    <div class="filter-field">
                        <i class="fa-regular fa-calendar-days field-icon"></i>
                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="filter-input">
                    </div>
                    <span class="filter-separator"><i class="fa-solid fa-arrow-right-long"></i></span>
                    <div class="filter-field">
                        <i class="fa-regular fa-calendar-days field-icon"></i>
                        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="filter-input">
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-filter-submit">
                        Search
                    </button>
                    @if(request('start_date') || request('end_date'))
                        <a href="{{ route('customer.store.orders') }}" class="btn-filter-clear">
                            <i class="fa-solid fa-arrows-rotate"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    @if($orders->isNotEmpty())
        <div class="orders-table-wrapper">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Books</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td><strong>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                            <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                <div class="table-books-list">
                                    @foreach($order->orderItems as $item)
                                        <div class="table-book-item">
                                            {{ $item->item->name }} <span class="text-muted">(x{{ $item->quantity }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td><strong>{{ number_format($order->total_amount) }} Ks</strong></td>
                            <td>
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
                            </td>
                            <td>
                                <button class="btn-table-action" onclick="toggleOrderDetails(this, {{ $order->id }})">
                                    <i class="fa-solid fa-chevron-down"></i> Details
                                </button>
                            </td>
                        </tr>
                        
                        <!-- Collapsible Expanded Detail Row -->
                        <tr id="details-row-{{ $order->id }}" class="order-details-row" style="display: none;">
                            <td colspan="6">
                                <div class="order-expanded-details">
                                    <div class="expanded-grid">
                                        <!-- Shipping Column -->
                                        <div class="expanded-col">
                                            <h5><i class="fa-solid fa-truck-fast"></i> Delivery & Payment</h5>
                                            @if($order->shippingAddress)
                                                <p><strong>Receiver:</strong> {{ $order->shippingAddress->receiver_name }}</p>
                                                <p><strong>Phone:</strong> {{ $order->shippingAddress->phone_number }}</p>
                                                <p><strong>Email:</strong> {{ $order->shippingAddress->email }}</p>
                                                <p><strong>Address:</strong> {{ $order->shippingAddress->address_line }}</p>
                                            @else
                                                <p class="text-muted">No delivery details associated.</p>
                                            @endif
                                            <p style="margin-top: 8px;"><strong>Payment Method:</strong> 
                                                <span style="text-transform: uppercase; font-weight: 700; color: var(--brand-gold, #cca353);">
                                                    @if($order->payment_method === 'cod')
                                                        Cash on Delivery (COD)
                                                    @elseif($order->payment_method === 'kpay')
                                                        KBZPay (KPay)
                                                    @elseif($order->payment_method === 'wave')
                                                        WaveMoney
                                                    @elseif($order->payment_method === 'stripe')
                                                        Stripe (Credit Card)
                                                    @else
                                                        {{ strtoupper($order->payment_method ?? 'N/A') }}
                                                    @endif
                                                </span>
                                            </p>
                                        </div>
                                        
                                        <!-- Items Summary Column -->
                                        <div class="expanded-col">
                                            <h5><i class="fa-solid fa-receipt"></i> Items Summary</h5>
                                            <div class="expanded-books-grid">
                                                @foreach($order->orderItems as $item)
                                                    <div class="expanded-book-row">
                                                        <img src="{{ $item->item->image ? asset('storage/' . $item->item->image) : asset('images/default-book.png') }}" class="expanded-cover" alt="{{ $item->item->name }}">
                                                        <div class="expanded-info">
                                                            <span class="expanded-name">{{ $item->item->name }}</span>
                                                            <span class="expanded-meta">
                                                                By {{ $item->item->author?->name ?? 'Unknown Author' }} | {{ number_format($item->price) }} Ks x {{ $item->quantity }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-orders-state">
            <i class="fa-solid fa-folder-open"></i>
            @if(request('start_date') || request('end_date'))
                <h3>No orders found</h3>
                <p>No orders were found matching your selected date range.</p>
                <a href="{{ route('customer.store.orders') }}" class="btn-primary">
                    <i class="fa-solid fa-rotate-left"></i> Reset Filter
                </a>
            @else
                <h3>No purchase history yet</h3>
                <p>Browse and buy your favorite books on Booknest.</p>
                <a href="{{ route('customer.store.home') }}" class="btn-primary">
                    <i class="fa-solid fa-store"></i> Go to Bookstore
                </a>
            @endif
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/customer/store.js') }}?v=1.1.3"></script>
<script>
    function toggleOrderDetails(btn, orderId) {
        const detailsRow = document.getElementById('details-row-' + orderId);
        if (detailsRow) {
            if (detailsRow.style.display === 'none') {
                detailsRow.style.display = 'table-row';
                btn.classList.add('active');
                btn.innerHTML = '<i class="fa-solid fa-chevron-up"></i> Hide';
            } else {
                detailsRow.style.display = 'none';
                btn.classList.remove('active');
                btn.innerHTML = '<i class="fa-solid fa-chevron-down"></i> Details';
            }
        }
    }
</script>
@endsection
