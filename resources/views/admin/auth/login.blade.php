<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff Login - Booknest Admin</title>
    
    <!-- Google Fonts (Inter & Outfit) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/login.css') }}?v=1.0.1">
</head>
<body>

    <div class="auth-wrapper">
        <!-- The Open Book Container -->
        <div class="book" id="book-container">
            
            <!-- 1. LEFT STATIC PAGE: Admin Branding / Welcome -->
            <div class="page left-page">
                <div class="branding-bg-circles">
                    <span></span>
                    <span></span>
                </div>
                <div class="branding-content">
                    <div class="brand-logo">
                        <i class="fa-solid fa-book-open"></i>
                        <span>Booknest Admin</span>
                    </div>
                    <div class="branding-main">
                        <h1 class="branding-title portal-title">Staff Portal</h1>
                        <p class="branding-desc">Manage books, author catalog, classification categories, customer banners, reviews and platform operations.</p>
                    </div>
                    <div class="branding-footer">
                        <p class="portal-copyright">&copy; 2026 Booknest Administration Console.</p>
                    </div>
                </div>
            </div>
            
            <!-- 2. RIGHT STATIC PAGE: Login Form -->
            <div class="page right-page right-page-flex">
                <div class="form-header">
                    <h2 class="form-title login-title">Sign In</h2>
                    <p class="form-subtitle login-subtitle">Enter your administrative credentials</p>
                </div>
                
                @if(session('success'))
                    <div class="alert-login-success">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif
                
                <form action="{{ route('admin.login.submit') }}" method="POST" id="login-form">
                    @csrf
                    
                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label login-label" for="login-email">Email Address</label>
                        <div class="input-wrapper">
                            <input class="form-input @error('email') is-invalid @enderror" type="email" id="login-email" name="email" placeholder="staff@booknest.com" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        @error('email')
                            <span class="error-message error-message-inline">
                                <i class="fa-solid fa-circle-exclamation error-icon-margin"></i>{{ $message }}
                            </span>
                        @enderror
                    </div>
                    
                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label login-label" for="login-password">Password</label>
                        <div class="input-wrapper">
                            <input class="form-input @error('password') is-invalid @enderror" type="password" id="login-password" name="password" placeholder="••••••••" required autocomplete="current-password">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        @error('password')
                            <span class="error-message error-message-inline">
                                <i class="fa-solid fa-circle-exclamation error-icon-margin"></i>{{ $message }}
                            </span>
                        @enderror
                    </div>
                    
                    <!-- Remember Me -->
                    <div class="form-options">
                        <label class="checkbox-label login-checkbox-label">
                            <input type="checkbox" id="remember-me" name="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="{{ url('/') }}" class="forgot-link login-store-link">Go to Store <i class="fa-solid fa-store login-store-icon"></i></a>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit">
                        Secure Login <i class="fa-solid fa-key btn-icon-margin"></i>
                    </button>
                </form>
            </div>
            
        </div>
    </div>

    <!-- Custom JS -->
    <script src="{{ asset('js/auth/auth.js') }}"></script>
</body>
</html>
