<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login / Register - Booknest</title>
    
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
        <!-- The Open Book Container -->
        <div class="book" id="book-container">
            
            <!-- 1. LEFT STATIC PAGE: Branding/Welcome Info -->
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
                        <h1 class="branding-title">Welcome Back!</h1>
                        <p class="branding-desc">Log in to continue exploring and buying your favorite books.</p>
                    </div>
                    <div class="branding-footer">
                        <p>&copy; 2026 Booknest Online Bookstore.</p>
                    </div>
                </div>
            </div>
            
            <!-- 2. RIGHT STATIC PAGE: Underneath Register Form -->
            <!-- Visible only when the middle page flips to the left -->
            <div class="page right-page">
                <div class="form-header">
                    <h2 class="form-title">Create an Account</h2>
                    <p class="form-subtitle">Fill in the details below to register</p>
                </div>
                
                <form action="{{ route('customer.register') }}" method="POST" id="register-form">
                    @csrf
                    <!-- Full Name -->
                    <div class="form-group">
                        <label class="form-label" for="reg-name">Full Name</label>
                        <div class="input-wrapper">
                            <input class="form-input @error('reg_name') is-invalid @enderror" type="text" id="reg-name" name="reg_name" placeholder="Name" value="{{ old('reg_name') }}" required>
                            <i class="fa-solid fa-user"></i>
                        </div>
                        @error('reg_name')
                            <span class="error-message error-message-inline">
                                <i class="fa-solid fa-circle-exclamation error-icon-margin"></i>{{ $message }}
                            </span>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label" for="reg-email">Email Address</label>
                        <div class="input-wrapper">
                            <input class="form-input @error('reg_email') is-invalid @enderror" type="email" id="reg-email" name="reg_email" placeholder="example@gmail.com" value="{{ old('reg_email') }}" required>
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        @error('reg_email')
                            <span class="error-message" error-message-inline>
                                <i class="fa-solid fa-circle-exclamation error-icon-margin"></i>{{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div class="form-group">
                        <label class="form-label" for="reg-phone">Phone Number</label>
                        <div class="input-wrapper">
                            <input class="form-input @error('reg_phone') is-invalid @enderror" type="tel" id="reg-phone" name="reg_phone" placeholder="(09)*********" value="{{ old('reg_phone') }}" required pattern="^[0-9]{9,11}$" minlength="9" maxlength="11" title="Phone number must be between 9 and 11 digits (numbers only)">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        @error('reg_phone')
                            <span class="error-message error-message-inline">
                                <i class="fa-solid fa-circle-exclamation error-icon-margin"></i>{{ $message }}
                            </span>
                        @enderror
                    </div>
                    
                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label" for="reg-password">Password</label>
                        <div class="input-wrapper">
                            <input class="form-input password-field @error('reg_password') is-invalid @enderror" type="password" id="reg-password" name="reg_password" placeholder="At least 8 characters" required>
                            <i class="fa-solid fa-lock"></i>
                            <i class="fa-solid fa-eye-slash password-toggle-icon" data-target="reg-password"></i>
                        </div>
                        @error('reg_password')
                            <span class="error-message error-message-inline">
                                <i class="fa-solid fa-circle-exclamation error-icon-margin"></i>{{ $message }}
                            </span>
                        @enderror
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit">
                        Create Account <i class="fa-solid fa-user-plus btn-icon-margin"></i>
                    </button>
                    
                    <div class="form-switch form-switch-margin">
                        <span>Already have an account? </span>
                        <a class="switch-trigger switch-trigger-pointer" id="to-login-link">Login</a>
                    </div>
                </form>
            </div>
            
            <!-- 3. MIDDLE FLIPPABLE PAGE -->
            <!-- Hinged on the center spine. Flips left to cover Left Page -->
            <div class="page middle-page {{ session('form_type') === 'register' || $errors->hasAny(['reg_name', 'reg_email', 'reg_phone', 'reg_password']) ? 'flipped' : '' }}" id="middle-page">
                <!-- Front Side: Login Form -->
                <div class="page-side page-front">
                    <div class="form-header">
                        <h2 class="form-title">Customer Login</h2>
                        <p class="form-subtitle">Enter your account details below</p>
                    </div>
                    
                    <form action="{{ route('customer.login') }}" method="POST" id="login-form">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="login-email">Email Address</label>
                            <div class="input-wrapper">
                                <input class="form-input @error('email') is-invalid @enderror" type="email" id="login-email" name="email" placeholder="example@gmail.com" value="{{ old('email') }}" required>
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            @error('email')
                                <span class="error-message error-message-inline">
                                    <i class="fa-solid fa-circle-exclamation error-icon-margin"></i>{{ $message }}
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="login-password">Password</label>
                            <div class="input-wrapper">
                                <input class="form-input password-field @error('password') is-invalid @enderror" type="password" id="login-password" name="password" placeholder="••••••••" required>
                                <i class="fa-solid fa-lock"></i>
                                <i class="fa-solid fa-eye-slash password-toggle-icon" data-target="login-password"></i>
                            </div>
                            @error('password')
                                <span class="error-message error-message-inline">
                                    <i class="fa-solid fa-circle-exclamation error-icon-margin"></i>{{ $message }}
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-options">
                            <label class="checkbox-label">
                                <input type="checkbox" id="remember-me" name="remember">
                                <span>Remember me</span>
                            </label>
                            <a href="{{ route('customer.forgot_password') }}" class="forgot-link">Forgot Password?</a>
                        </div>
                        
                        <button type="submit" class="btn-submit">
                            Login <i class="fa-solid fa-arrow-right-to-bracket btn-icon-margin"></i>
                        </button>
                    </form>
                    
                    <div class="form-switch">
                        <span>Don't have an account? </span>
                        <a class="switch-trigger" id="to-register">Register</a>
                    </div>
                </div>
                
                <!-- Back Side: Action branding to go back to Login -->
                <div class="page-side page-back">
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
                            <h2 class="branding-title">Already have an account?</h2>
                            <p class="branding-desc branding-desc-margin">Click the button below to log back into your account.</p>
                            <button class="btn-submit btn-submit-login-back" id="to-login">
                                <i class="fa-solid fa-arrow-left btn-icon-back-margin"></i> Back to Login
                            </button>
                        </div>
                        <div class="branding-footer">
                            <p>&copy; 2026 Booknest Online Bookstore.</p>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Custom JS -->
    <script src="{{ asset('js/auth/auth.js') }}"></script>
</body>
</html>
