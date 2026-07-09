@extends('layouts.app')

@section('title', 'Membership Upgraded - Booknest')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/store.css') }}?v=1.4.3">
@endsection

@section('content')
<div class="success-page-wrapper container">
    <div class="success-card subscription-success-card">
        <div class="success-icon-box subscription-success-icon">
            <i class="fa-solid fa-crown"></i>
        </div>
        <h1 class="success-title">Membership Upgraded!</h1>
        <p class="success-desc">
            You have successfully upgraded to VIP Reader. From now on, you can download and read all books in the store as PDF without any limits.
        </p>
        
        <div class="success-details-list">
            <div class="success-detail-row">
                <span>Membership Type:</span>
                <strong>Monthly VIP Reader Pass</strong>
            </div>
            <div class="success-detail-row">
                <span>Payment Method:</span>
                <strong>{{ strtoupper($paymentMethod == 'kpay' ? 'KBZPay' : 'WavePay') }}</strong>
            </div>
            <div class="success-detail-row">
                <span>Expiry Date:</span>
                <strong>{{ $customer->subscription_expires_at->format('M d, Y \a\t h:i A') }}</strong>
            </div>
        </div>

        <div class="success-actions">
            <a href="{{ route('customer.store.home') }}" class="btn btn-primary"><i class="fa-solid fa-store"></i> Go to Bookstore</a>
            <a href="{{ route('customer.dashboard') }}" class="btn btn-outline"><i class="fa-solid fa-book-bookmark"></i> Go to My Bookshelf</a>
        </div>
    </div>
</div>
@endsection
