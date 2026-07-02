@extends('layouts.app')

@section('title', 'Payment Success - Booknest')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/store.css') }}?v=1.1.6">
@endsection

@section('content')
<div class="success-page-wrapper container">
    <div class="success-card">
        <div class="success-icon-box">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        
        <h1 class="success-title">Payment Successful</h1>
        <p class="success-desc">Thank you for your purchase at Booknest. Your order has been successfully confirmed.</p>
        
        <div class="success-details-list">
            <div class="success-detail-row">
                <span>Order ID</span>
                <strong>#{{ $order->id }}</strong>
            </div>
            <div class="success-detail-row">
                <span>Total Amount</span>
                <strong>{{ number_format($order->total_amount) }} Ks</strong>
            </div>
            <div class="success-detail-row">
                <span>Payment Status</span>
                <span class="color-success">Paid</span>
            </div>
        </div>

        <div class="success-actions">
            <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">
                <i class="fa-solid fa-book-bookmark"></i> Go to Bookshelf
            </a>
            <a href="{{ route('customer.store.orders') }}" class="btn btn-outline">
                <i class="fa-solid fa-receipt"></i> Order History
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/customer/store.js') }}?v=1.0.5"></script>
@endsection
