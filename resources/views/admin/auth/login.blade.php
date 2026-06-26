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

    <style>
        :root {
            --primary: #c89658; /* Warm Gold */
            --primary-dark: #8e6549; /* Dark Bronze/Wood */
            --primary-light: #faf4eb; /* Creamy White */
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        

        .left-page {
            background: linear-gradient(135deg, #221309 0%, #351f0f 50%, #0f172a 100%) !important;
            border-right: 1px solid #1a0f07 !important;
        }

        .brand-logo i {
            color: var(--primary) !important;
        }

        .btn-submit {
            background: var(--primary) !important;
            box-shadow: 0 4px 6px -1px rgba(200, 150, 88, 0.3) !important;
            transition: var(--transition-smooth);
        }

        .btn-submit:hover {
            background: var(--primary-dark) !important;
            box-shadow: 0 10px 15px -3px rgba(200, 150, 88, 0.4) !important;
        }

        .form-input:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(200, 150, 88, 0.2) !important;
        }

        .form-input:focus + i {
            color: var(--primary) !important;
        }

        .checkbox-label input:checked {
            accent-color: var(--primary) !important;
        }

        .branding-desc {
            color: #dcd6bc !important;
        }
    </style>
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
                        <h1 class="branding-title" style="color: #ffe0a3; text-shadow: 0 2px 4px rgba(0,0,0,0.4);">Staff Portal</h1>
                        <p class="branding-desc">Manage books, author catalog, classification categories, customer banners, reviews and platform operations.</p>
                    </div>
                    <div class="branding-footer">
                        <p style="color: #8e6549;">&copy; 2026 Booknest Administration Console.</p>
                    </div>
                </div>
            </div>
            
            <!-- 2. RIGHT STATIC PAGE: Login Form -->
            <div class="page right-page" style="display: flex; flex-direction: column; justify-content: center;">
                <div class="form-header">
                    <h2 class="form-title" style="color: #4C2D17;">Sign In</h2>
                    <p class="form-subtitle" style="color: #724E32;">Enter your administrative credentials</p>
                </div>
                
                @if(session('success'))
                    <div style="background-color: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 10px; border-radius: 8px; font-size: 0.82rem; margin-bottom: 12px; font-weight: 600;">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif
                
                <form action="{{ route('admin.login.submit') }}" method="POST" id="login-form">
                    @csrf
                    
                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label" for="login-email" style="color: #4C2D17;">Email Address</label>
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
                        <label class="form-label" for="login-password" style="color: #4C2D17;">Password</label>
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
                        <label class="checkbox-label" style="color: #724E32;">
                            <input type="checkbox" id="remember-me" name="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="{{ url('/') }}" class="forgot-link" style="color: var(--primary-dark);">Go to Store <i class="fa-solid fa-store" style="font-size: 0.75rem;"></i></a>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit">
                        Secure Login <i class="fa-solid fa-key btn-icon-margin"></i>
                    </button>
                </form>
            </div>
            
        </div>
    </div>

</body>
</html>
