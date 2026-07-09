@extends('layouts.app')

@section('title', 'Membership Plan - Booknest')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/store.css') }}?v=1.4.3">
@endsection

@section('content')
<div class="subscription-page-wrapper container">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="premium-alert-box alert-success parchment-alert">
            <div class="alert-icon-box">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="alert-message-box">
                <span class="alert-title">Success</span>
                <p class="alert-desc">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="premium-alert-box alert-danger parchment-alert">
            <div class="alert-icon-box">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="alert-message-box">
                <span class="alert-title">Membership Required</span>
                <p class="alert-desc">{{ session('error') }}</p>
            </div>
        </div>
    @endif



    @if($customer->hasActiveSubscription())
        <!-- Active Subscription View -->
        <div class="active-subscription-card parchment-scroll">
            <div class="scroll-wood-top"></div>
            <div class="scroll-body-wrapper">
                <div class="active-sub-header">
                    <div class="active-badge"><i class="fa-solid fa-crown"></i> Active VIP Member</div>
                    <div class="active-plan-name">Monthly VIP Reader Pass</div>
                </div>
                <div class="active-sub-body">
                    <div class="active-details-grid">
                        <div class="active-detail-item">
                            <span class="lbl">Start Date</span>
                            <span class="val">{{ $customer->subscription_expires_at->subDays(30)->format('M d, Y') }}</span>
                        </div>
                        <div class="active-detail-item">
                            <span class="lbl">Expiry Date</span>
                            <span class="val">{{ $customer->subscription_expires_at->format('M d, Y') }}</span>
                        </div>
                        <div class="active-detail-item highlight">
                            <span class="lbl">Days Left</span>
                            <span class="val">{{ $customer->getSubscriptionDaysLeft() }} days</span>
                        </div>
                    </div>
                    <div class="active-benefits-section">
                        <h4>Membership Benefits</h4>
                        <ul class="benefits-list">
                            <li>
                                <div class="benefit-icon-wrapper"><i class="fa-solid fa-check"></i></div>
                                <span>Unlimited PDF downloads for all books in the store.</span>
                            </li>
                            <li>
                                <div class="benefit-icon-wrapper"><i class="fa-solid fa-check"></i></div>
                                <span>Automatic reading progress tracking on the 3D bookshelf.</span>
                            </li>
                            <li>
                                <div class="benefit-icon-wrapper"><i class="fa-solid fa-check"></i></div>
                                <span>Access to all premium VIP reading features.</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="active-sub-footer text-center">
                    <a href="{{ route('customer.store.home') }}" class="btn-primary btn-parchment"><i class="fa-solid fa-store"></i> Go to Bookstore</a>
                </div>
            </div>
            <div class="scroll-wood-bottom"></div>
        </div>
    @else
        <!-- Unsubscribed View / Pricing Table -->
        <div class="subscription-plans-grid">
            <div class="plan-card premium parchment-scroll">
                <div class="scroll-wood-top"></div>
                
                <div class="scroll-body-wrapper">
                    <div class="plan-crown"><i class="fa-solid fa-crown"></i></div>
                    <h3 class="plan-name">Monthly VIP Reader Pass</h3>
                    <div class="plan-price">
                        <span class="currency">Ks</span>
                        <span class="amount">5,000</span>
                        <span class="period">/ 30 Days</span>
                    </div>
                    <p class="plan-desc">The ultimate plan for book lovers to download and read without limits.</p>
                    
                    <div class="plan-benefits">
                        <div class="benefit-item">
                            <div class="benefit-icon-wrapper">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <span>Download any PDF book in the store</span>
                        </div>
                        <div class="benefit-item">
                            <div class="benefit-icon-wrapper">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <span>Save and track your reading progress</span>
                        </div>
                        <div class="benefit-item">
                            <div class="benefit-icon-wrapper">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <span>Read seamlessly on mobile and desktop</span>
                        </div>
                    </div>

                    <form action="{{ route('customer.subscription.checkout') }}" method="POST" class="payment-method-form">
                        @csrf
                        <h4 class="payment-title"><i class="fa-solid fa-wallet"></i> Select Payment Method</h4>
                        
                        <div class="subscription-payment-methods">
                            <label class="pay-method-option selected">
                                <input type="radio" name="payment_method" value="kpay" checked class="display-none">
                                <div class="pay-method-card-wrapper">
                                    <div class="pay-badge-selected wax-seal"><i class="fa-solid fa-stamp"></i></div>
                                    <div class="pay-method-content">
                                        <div class="pay-logo-wrapper">
                                            <div class="pay-logo kpay">K</div>
                                        </div>
                                        <div class="pay-info">
                                            <span class="pay-name">KBZPay</span>
                                            <span class="pay-subtext">Fast and secure payment</span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="pay-method-option">
                                <input type="radio" name="payment_method" value="wave" class="display-none">
                                <div class="pay-method-card-wrapper">
                                    <div class="pay-badge-selected wax-seal"><i class="fa-solid fa-stamp"></i></div>
                                    <div class="pay-method-content">
                                        <div class="pay-logo-wrapper">
                                            <div class="pay-logo wave">W</div>
                                        </div>
                                        <div class="pay-info">
                                            <span class="pay-name">WavePay</span>
                                            <span class="pay-subtext">Fast and secure payment</span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <button type="submit" class="btn-upgrade-now btn-parchment">
                            <i class="fa-solid fa-credit-card"></i> Pay Now (Upgrade to VIP)
                        </button>
                    </form>
                </div>

                <div class="scroll-wood-bottom"></div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const options = document.querySelectorAll('.pay-method-option');
        options.forEach(opt => {
            opt.addEventListener('click', function() {
                options.forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
                const radio = this.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;
            });
        });
    });
</script>
@endsection


