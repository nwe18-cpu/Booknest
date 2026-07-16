@extends('layouts.app')

@section('title', 'Payment Success - Booknest')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/store.css') }}?v=1.4.3">
@endsection

@section('content')
<div class="success-page-wrapper container">
    <div class="success-card">
        <div class="success-icon-box">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        
        <h1 class="success-title">Order Confirmed!</h1>
        <p class="success-desc">Thank you for your purchase. Your order has been successfully placed.</p>
        
        <div class="success-details-list" style="margin-top: 20px; border-bottom: 1px dashed rgba(0,0,0,0.1); padding-bottom: 20px; text-align: left; display: grid; gap: 8px;">
            <div class="success-detail-row">
                <span>Order ID:</span>
                <strong>#{{ $order->id }}</strong>
            </div>
            <div class="success-detail-row">
                <span>Order Date:</span>
                <span>{{ $order->created_at->format('M d, Y h:i A') }}</span>
            </div>
            <div class="success-detail-row">
                <span>Payment Method:</span>
                <span style="font-weight: 700; color: var(--primary);">{{ strtoupper($order->payment_method) }}</span>
            </div>
            <div class="success-detail-row">
                <span>Payment Status:</span>
                <span class="{{ $order->payment_status === 'paid' ? 'color-success' : 'color-danger' }}" style="font-weight: 700;">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </div>
        </div>

        <div style="margin-top: 20px; text-align: left;">
            <h4 style="color: #5c3a21; margin-bottom: 8px;"><i class="fa-solid fa-location-dot"></i> Delivery Address</h4>
            <div style="background: rgba(0,0,0,0.02); padding: 12px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.05); font-size: 0.85rem; line-height: 1.5; color: #4a5568;">
                <div><strong>{{ $order->shippingAddress->receiver_name }}</strong></div>
                <div>Phone: {{ $order->shippingAddress->phone_number }}</div>
                @if($order->shippingAddress->email)
                    <div>Email: {{ $order->shippingAddress->email }}</div>
                @endif
                <div style="margin-top: 4px; color: #718096;">Address: {{ $order->shippingAddress->address_line }}</div>
            </div>
        </div>

        <div style="margin-top: 20px; text-align: left;">
            <h4 style="color: #5c3a21; margin-bottom: 8px;"><i class="fa-solid fa-receipt"></i> Items Purchased</h4>
            <div style="border: 1px solid rgba(0,0,0,0.08); border-radius: 8px; overflow: hidden; background: #fff;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <thead>
                        <tr style="background: rgba(0,0,0,0.03); border-bottom: 1px solid rgba(0,0,0,0.08);">
                            <th style="padding: 10px 12px; text-align: left; color: #5c3a21;">Item Name</th>
                            <th style="padding: 10px 12px; text-align: center; color: #5c3a21; width: 60px;">Qty</th>
                            <th style="padding: 10px 12px; text-align: right; color: #5c3a21; width: 100px;">Price</th>
                            <th style="padding: 10px 12px; text-align: right; color: #5c3a21; width: 100px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                            <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <td style="padding: 10px 12px; font-weight: 700; color: #2d3748; text-align: left;">{{ $item->item->name }}</td>
                                <td style="padding: 10px 12px; text-align: center; color: #4a5568;">{{ $item->quantity }}</td>
                                <td style="padding: 10px 12px; text-align: right; color: #4a5568;">{{ number_format($item->price) }} Ks</td>
                                <td style="padding: 10px 12px; text-align: right; font-weight: 700; color: #2d3748;">{{ number_format($item->price * $item->quantity) }} Ks</td>
                            </tr>
                        @endforeach
                        <tr style="background: rgba(0,0,0,0.01); font-weight: 700;">
                            <td colspan="3" style="padding: 12px; text-align: right; color: #5c3a21;">Grand Total:</td>
                            <td style="padding: 12px; text-align: right; color: #5c3a21; font-size: 0.95rem;">{{ number_format($order->total_amount) }} Ks</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="success-actions" style="margin-top: 25px;">
            <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">
                <i class="fa-solid fa-house-chimney"></i> Go to Dashboard
            </a>
            <a href="{{ route('customer.store.orders') }}" class="btn btn-outline">
                <i class="fa-solid fa-receipt"></i> Order History
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/customer/store.js') }}?v=1.2.3"></script>
@endsection
