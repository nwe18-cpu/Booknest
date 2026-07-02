@extends('layouts.app')

@section('title', 'Checkout - Booknest')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/store.css') }}?v=1.1.6">
@endsection

@section('content')
<div class="checkout-page-wrapper container">
    <h1 class="cart-page-title">Checkout</h1>
    
    <form id="checkout-form" onsubmit="submitCheckout(event)">
        @csrf
        <div class="checkout-grid">
            <!-- Left Card: Shipping Details & Payment Methods -->
            <div class="checkout-card flex-column-gap">
                <h3 class="checkout-form-title">Shipping & Contact Details</h3>
                
                <div class="form-group">
                    <label for="receiver_name">Full Name</label>
                    <input type="text" id="receiver_name" name="receiver_name" class="form-control" value="{{ auth()->guard('customer')->user()->name }}" required>
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" class="form-control" required placeholder="e.g. 09123456789">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ auth()->guard('customer')->user()->email }}" required>
                </div>

                <div class="form-group">
                    <label for="address_line">Shipping Address</label>
                    <textarea id="address_line" name="address_line" class="form-control" rows="3" required placeholder="Enter house number, street, township, region..."></textarea>
                </div>

                <h3 class="checkout-form-title">Select Payment Method</h3>
                
                <div class="payment-methods-grid">
                    <!-- Cash on Delivery -->
                    <div class="payment-method-card selected" id="pay-method-cod" onclick="selectPaymentMethod('cod')">
                        <input type="radio" name="payment_method" value="cod" checked class="display-none">
                        <div class="payment-method-icon">
                            <i class="fa-solid fa-truck-ramp-box"></i>
                        </div>
                        <div class="payment-method-info">
                            <span class="payment-method-name">Cash on Delivery (COD)</span>
                            <span class="payment-method-desc">Pay upon delivery</span>
                        </div>
                    </div>

                    <!-- Stripe Cards -->
                    <div class="payment-method-card" id="pay-method-stripe" onclick="selectPaymentMethod('stripe')">
                        <input type="radio" name="payment_method" value="stripe" class="display-none">
                        <div class="payment-method-icon">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                        <div class="payment-method-info">
                            <span class="payment-method-name">Card Payment</span>
                            <span class="payment-method-desc">Stripe / Visa / MasterCard</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Card: Order Summary Card -->
            <div class="checkout-card">
                <h3 class="checkout-form-title">Order Summary</h3>
                
                <div class="checkout-items-list">
                    @foreach($cart as $id => $item)
                        <div class="checkout-item-row">
                            <div class="checkout-item-name-col">
                                <span class="checkout-item-name">{{ $item['name'] }}</span>
                                <span class="checkout-item-author">x{{ $item['quantity'] }} - By {{ $item['author'] }}</span>
                            </div>
                            <div class="checkout-item-price-col">
                                {{ number_format($item['price'] * $item['quantity']) }} Ks
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="checkout-totals">
                    <div class="checkout-totals-row">
                        <span>Total Quantity:</span>
                        <span>{{ $totalQuantity }} books</span>
                    </div>
                    <div class="checkout-totals-row">
                        <span>Shipping Fee:</span>
                        <span class="color-success">Free</span>
                    </div>
                    <div class="checkout-totals-row checkout-totals-total">
                        <span>Grand Total:</span>
                        <span>{{ number_format($totalAmount) }} Ks</span>
                    </div>
                </div>

                <button type="submit" id="btn-submit-order" class="btn-checkout">
                    <i class="fa-solid fa-lock"></i> Place Order (Order Now)
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/customer/store.js') }}?v=1.0.5"></script>
@endsection
