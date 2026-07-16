@extends('layouts.app')

@section('title', 'Shopping Cart - Booknest')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/store.css') }}?v=1.4.3">
@endsection

@section('content')
<div class="cart-page-wrapper container">
    <h1 class="cart-page-title">My Shopping Cart</h1>
    <div id="cart-page-content" data-home-url="{{ route('customer.store.home') }}" data-checkout-url="{{ route('customer.store.checkout') }}">
        <!-- Dynamically rendered by store.js -->
        <div class="cart-loading-container">
            <i class="fa-solid fa-spinner fa-spin cart-loading-spinner"></i>
            <p class="cart-loading-text">Fetching cart details...</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/customer/store.js') }}?v=1.2.3"></script>
@endsection
