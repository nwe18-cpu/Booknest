@extends('layouts.app')

@section('title', 'Checkout - Booknest')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/store.css') }}?v=1.5.7">
@endsection

@section('content')
<div class="checkout-page-wrapper container">
    <h1 class="cart-page-title">Checkout</h1>
    
    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 1.5rem; padding: 1rem; border-radius: 8px; background-color: var(--red-light); color: var(--red); border: 1px solid var(--red);">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif
    
    <form id="checkout-form" onsubmit="submitCheckout(event)">
        @csrf
        <div class="checkout-grid">
            <!-- Left Card: Shipping Details & Payment Methods -->
            <div class="checkout-card flex-column-gap">
                <h3 class="checkout-form-title">Shipping & Contact Details</h3>
                
                @if(count($addresses) > 0)
                    <div class="form-group" style="background: rgba(0,0,0,0.02); padding: 12px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.05); margin-bottom: 12px;">
                        <label for="select-saved-address" style="font-weight: 700; color: #5c3a21; margin-bottom: 6px; display: block;">Choose Saved Address</label>
                        <select id="select-saved-address" class="form-control" onchange="useSavedAddress(this)" style="width: 100%; padding: 8px; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px;">
                            <option value="">-- Add New Shipping Address --</option>
                            @foreach($addresses as $addr)
                                <option value="{{ $addr->id }}" 
                                        data-name="{{ $addr->receiver_name }}" 
                                        data-phone="{{ $addr->phone_number }}" 
                                        data-email="{{ $addr->email }}" 
                                        data-line="{{ $addr->address_line }}"
                                        {{ $addr->is_default ? 'selected' : '' }}>
                                    {{ $addr->receiver_name }} ({{ Str::limit($addr->address_line, 45) }}) {{ $addr->is_default ? '[Default]' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="form-group">
                    <label for="receiver_name">Full Name</label>
                    <input type="text" id="receiver_name" name="receiver_name" class="form-control" value="{{ auth()->guard('customer')->user()->name }}" required>
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" class="form-control" required placeholder="(09)*********" pattern="^[0-9]{9,11}$" minlength="9" maxlength="11" title="Phone number must be between 9 and 11 digits (numbers only)">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ auth()->guard('customer')->user()->email }}" required>
                </div>

                <div class="form-group">
                    <label for="address_line">Shipping Address</label>
                    <textarea id="address_line" name="address_line" class="form-control" rows="3" required placeholder="Enter house number, street, township, region..."></textarea>
                </div>

                <div class="form-group">
                    <label for="note">Order Note (Optional)</label>
                    <textarea id="note" name="note" class="form-control" rows="2" placeholder="Write any instructions for delivery or notes for your order here..."></textarea>
                </div>

                <h3 class="checkout-form-title">Select Payment Method</h3>
                
                <div class="payment-methods-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 10px;">
                    <!-- Cash on Delivery -->
                    <div class="payment-method-card selected" id="pay-method-cod" onclick="selectPaymentMethod('cod')">
                        <input type="radio" name="payment_method" value="cod" checked class="display-none">
                        <div class="payment-method-icon"><i class="fa-solid fa-truck-ramp-box"></i></div>
                        <div class="payment-method-info">
                            <strong class="payment-method-name" style="display: block; font-size: 0.85rem;">Cash on Delivery</strong>
                            <span class="payment-method-desc" style="font-size: 0.75rem; color: #718096;">COD (Pay at door)</span>
                        </div>
                    </div>

                    <!-- KBZPay (Kpay) -->
                    <div class="payment-method-card" id="pay-method-kpay" onclick="selectPaymentMethod('kpay')">
                        <input type="radio" name="payment_method" value="kpay" class="display-none">
                        <div class="payment-method-icon" style="color: #1877f2;"><i class="fa-solid fa-mobile-screen-button"></i></div>
                        <div class="payment-method-info">
                            <strong class="payment-method-name" style="display: block; font-size: 0.85rem;">KBZPay (KPay)</strong>
                            <span class="payment-method-desc" style="font-size: 0.75rem; color: #718096;">Instant mobile pay</span>
                        </div>
                    </div>

                    <!-- Wave Money -->
                    <div class="payment-method-card" id="pay-method-wave" onclick="selectPaymentMethod('wave')">
                        <input type="radio" name="payment_method" value="wave" class="display-none">
                        <div class="payment-method-icon" style="color: #e53e3e;"><i class="fa-solid fa-wallet"></i></div>
                        <div class="payment-method-info">
                            <strong class="payment-method-name" style="display: block; font-size: 0.85rem;">Wave Money</strong>
                            <span class="payment-method-desc" style="font-size: 0.75rem; color: #718096;">WavePay Mobile</span>
                        </div>
                    </div>

                    <!-- Card Payment (Stripe) -->
                    <div class="payment-method-card" id="pay-method-stripe" onclick="selectPaymentMethod('stripe')">
                        <input type="radio" name="payment_method" value="stripe" class="display-none">
                        <div class="payment-method-icon" style="color: #6772e5;"><i class="fa-solid fa-credit-card"></i></div>
                        <div class="payment-method-info">
                            <strong class="payment-method-name" style="display: block; font-size: 0.85rem;">Card Payment</strong>
                            <span class="payment-method-desc" style="font-size: 0.75rem; color: #718096;">Stripe / Credit Card</span>
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
<script src="{{ asset('js/customer/store.js') }}?v=1.1.3"></script>
<script>
function useSavedAddress(selectEl) {
    if (selectEl.value === "") {
        document.getElementById('receiver_name').value = "{{ auth()->guard('customer')->user()->name }}";
        document.getElementById('phone_number').value = "";
        document.getElementById('email').value = "{{ auth()->guard('customer')->user()->email }}";
        document.getElementById('address_line').value = "";
        return;
    }
    const option = selectEl.options[selectEl.selectedIndex];
    document.getElementById('receiver_name').value = option.getAttribute('data-name');
    document.getElementById('phone_number').value = option.getAttribute('data-phone');
    document.getElementById('email').value = option.getAttribute('data-email') || "{{ auth()->guard('customer')->user()->email }}";
    document.getElementById('address_line').value = option.getAttribute('data-line');
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAddr = document.getElementById('select-saved-address');
    if (selectAddr && selectAddr.value !== '') {
        useSavedAddress(selectAddr);
    }
});
</script>
@endsection
