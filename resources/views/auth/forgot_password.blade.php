<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - Booknest</title>
    
    <!-- Google Fonts (Inter & Outfit) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/auth.css') }}?v=1.0.3">
</head>
<body>

    <div class="auth-wrapper">
        <!-- Open Book Container -->
        <div class="book">
            
            <!-- LEFT PAGE: Branding/Welcome Info -->
            <div class="page left-page">
                <div class="branding-bg-circles">
                    <span></span>
                    <span></span>
                </div>
                <div class="branding-content">
                    <div class="brand-logo">
                        <i class="fa-solid fa-book-open"></i>
                        <span>Booknest</span>
                    </div>
                    <div class="branding-main">
                        <h1 class="branding-title">Reset Password</h1>
                        <p class="branding-desc">Verify your email and phone number to regain access to your account.</p>
                    </div>
                    <div class="branding-footer">
                        <p>&copy; 2026 Booknest Online Bookstore.</p>
                    </div>
                </div>
            </div>
            
            <!-- RIGHT PAGE: Reset Password Form -->
            <div class="page page-front">
                <div class="form-header">
                    <h2 class="form-title">Reset Password</h2>
                    <p class="form-subtitle">Enter registration details to reset your password</p>
                </div>
                
                @if(session('success'))
                    <div class="success-message" style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 0.75rem 1.25rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: .25rem;">
                        {{ session('success') }}
                    </div>
                @endif
                
                <form action="{{ route('customer.forgot_password.reset') }}" method="POST" id="forgot-password-form">
                    @csrf
                    
                    <!-- Email Address -->
                    <div class="form-group">
                        <label class="form-label" for="reset-email">Email Address</label>
                        <div class="input-wrapper">
                            <input class="form-input @error('email') is-invalid @enderror" type="email" id="reset-email" name="email" placeholder="Registered Email" value="{{ old('email') }}" required>
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        @error('email')
                            <span class="error-message error-message-inline">
                                <i class="fa-solid fa-circle-exclamation error-icon-margin"></i>{{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div class="form-group">
                        <label class="form-label" for="reset-phone">Phone Number</label>
                        <div class="input-wrapper">
                            <input class="form-input @error('phone') is-invalid @enderror" type="tel" id="reset-phone" name="phone" placeholder="Registered Phone" value="{{ old('phone') }}" required pattern="^[0-9]{9,11}$" minlength="9" maxlength="11" title="Phone number must be between 9 and 11 digits (numbers only)">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        @error('phone')
                            <span class="error-message error-message-inline">
                                <i class="fa-solid fa-circle-exclamation error-icon-margin"></i>{{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="form-group">
                        <label class="form-label" for="reset-password">New Password</label>
                        <div class="input-wrapper">
                            <input class="form-input password-field @error('password') is-invalid @enderror" type="password" id="reset-password" name="password" placeholder="At least 8 characters" required>
                            <i class="fa-solid fa-lock"></i>
                            <i class="fa-solid fa-eye-slash password-toggle-icon" data-target="reset-password"></i>
                        </div>
                        @error('password')
                            <span class="error-message error-message-inline">
                                <i class="fa-solid fa-circle-exclamation error-icon-margin"></i>{{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div class="form-group">
                        <label class="form-label" for="reset-password-confirm">Confirm Password</label>
                        <div class="input-wrapper">
                            <input class="form-input password-field" type="password" id="reset-password-confirm" name="password_confirmation" placeholder="Confirm your password" required>
                            <i class="fa-solid fa-lock"></i>
                            <i class="fa-solid fa-eye-slash password-toggle-icon" data-target="reset-password-confirm"></i>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        Reset Password <i class="fa-solid fa-key btn-icon-margin"></i>
                    </button>
                </form>
                
                <div class="form-switch" style="margin-top: 1.5rem;">
                    <a href="{{ route('login') }}" class="switch-trigger"><i class="fa-solid fa-arrow-left"></i> Back to Login</a>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Custom JS -->
    <script src="{{ asset('js/auth/auth.js') }}"></script>
</body>
</html>
