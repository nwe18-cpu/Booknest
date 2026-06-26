@extends('admin.layouts.app')

@section('title', 'Booknest Admin - Staff Profile Settings')

@section('styles')
<style>
    .customer-profile-grid {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 25px;
        margin-top: 20px;
    }
    
    @media (max-width: 1024px) {
        .customer-profile-grid {
            grid-template-columns: 1fr;
        }
    }
    
    .detail-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 8px 30px rgba(76, 45, 23, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.6);
        margin-bottom: 25px;
        position: relative;
    }
    
    .detail-card-header {
        border-bottom: 1px solid #EDE8D0;
        padding-bottom: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .detail-card-header h4 {
        margin: 0;
        font-size: 1.15rem;
        color: var(--text-main);
        font-weight: 700;
    }

    .profile-hero {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        margin-bottom: 20px;
    }

    .profile-avatar-large {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        border: 4px solid var(--border-color);
        object-fit: cover;
        box-shadow: 0 4px 15px rgba(76, 45, 23, 0.12);
        margin-bottom: 10px;
    }

    .profile-avatar-placeholder-large {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background-color: var(--sidebar-bg);
        color: var(--sidebar-text);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 2.2rem;
        box-shadow: 0 4px 15px rgba(76, 45, 23, 0.12);
        margin-bottom: 10px;
        border: 4px solid var(--border-color);
    }
    
    .info-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        font-size: 0.95rem;
        line-height: 1.5;
    }
    
    .info-label {
        color: var(--text-muted);
        font-weight: 600;
    }
    
    .info-value {
        color: var(--text-main);
        font-weight: 700;
        text-align: right;
    }
    
    .status-select-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .status-form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 15px;
    }
    
    .status-form-group label {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-muted);
    }
    
    .status-select-control {
        padding: 10px 14px;
        border: 1px solid #DCD6BC;
        border-radius: 8px;
        background-color: #FAFAFA;
        color: #1A2E3B;
        font-size: 0.9rem;
        outline: none;
        width: 100%;
        box-sizing: border-box;
    }
    
    .status-select-control:focus {
        border-color: #4C2D17;
        background-color: #FFFFFF;
    }
    
    .btn-update-status {
        background-color: #4C2D17;
        color: #EDE8D0;
        border: none;
        border-radius: 8px;
        padding: 12px 20px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        margin-top: 10px;
        width: 100%;
    }
    
    .btn-update-status:hover {
        background-color: #351f0f;
    }
    
    .badge-status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    .status-sub-active { color: #1e3a8a; background-color: #dbeafe; border: 1px solid #bfdbfe; }
    .status-sub-inactive { color: #374151; background-color: #f3f4f6; border: 1px solid #e5e7eb; }
</style>
@endsection

@section('content')
<div class="dashboard-wrapper-new">
    
    <div class="form-header-modern" style="margin-bottom: 20px;">
        <h2><i class="fa-solid fa-user-gear"></i> Staff Profile Settings</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn-back-modern" style="text-decoration: none; color: var(--text-main); font-weight: 600; display: inline-flex; align-items: center; gap: 6px; border: 1px solid #DCD6BC; padding: 8px 16px; border-radius: 8px; background: #FFF;">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background-color: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="status-select-form">
        @csrf
        
        <div class="customer-profile-grid">
            <!-- Left Column: Staff Info Card -->
            <div>
                <div class="detail-card">
                    <div class="profile-hero">
                        @if($staff->image)
                            <img src="{{ asset('storage/' . $staff->image) }}" alt="avatar" class="profile-avatar-large">
                        @else
                            <div class="profile-avatar-placeholder-large">
                                {{ strtoupper(substr($staff->name, 0, 1)) }}
                            </div>
                        @endif
                        <h3 style="margin: 5px 0 0 0; font-size: 1.3rem;">{{ $staff->name }}</h3>
                        <div style="margin-top: 5px;">
                            <span class="badge-status status-sub-{{ $staff->role?->name === 'admin' ? 'active' : 'inactive' }}">
                                {{ strtoupper($staff->role?->name ?? 'Staff') }}
                            </span>
                        </div>
                    </div>

                    <div class="info-list" style="border-top: 1px solid #EDE8D0; padding-top: 15px;">
                        <div class="info-item">
                            <span class="info-label">Staff ID:</span>
                            <span class="info-value">#{{ $staff->id }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email Address:</span>
                            <span class="info-value">{{ $staff->email }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Phone Number:</span>
                            <span class="info-value">{{ $staff->phone }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Member Since:</span>
                            <span class="info-value">{{ $staff->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Personal & Security Details -->
            <div>
                <!-- Personal Details Card -->
                <div class="detail-card">
                    <div class="detail-card-header">
                        <i class="fa-solid fa-address-card" style="color: #c89658; font-size: 1.25rem;"></i>
                        <h4>Personal Details</h4>
                    </div>
                    
                    <div class="status-form-group">
                        <label for="name">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $staff->name) }}" class="status-select-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="status-form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $staff->email) }}" class="status-select-control @error('email') is-invalid @enderror" required>
                        @error('email')
                            <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="status-form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $staff->phone) }}" class="status-select-control @error('phone') is-invalid @enderror" required>
                        @error('phone')
                            <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="status-form-group">
                        <label for="image">Profile Picture (Avatar)</label>
                        <input type="file" name="image" id="image" class="status-select-control @error('image') is-invalid @enderror" accept="image/*">
                        <small style="color: var(--text-muted); font-size: 0.8rem; margin-top: 4px; display: block;">Max file size: 2MB. Allowed formats: JPG, PNG, JPEG, GIF.</small>
                        @error('image')
                            <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Password Changes Card -->
                <div class="detail-card">
                    <div class="detail-card-header">
                        <i class="fa-solid fa-key" style="color: #c89658; font-size: 1.25rem;"></i>
                        <h4>Security Settings (Password Update)</h4>
                    </div>

                    <div class="status-form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="status-select-control @error('current_password') is-invalid @enderror" placeholder="Enter current password to make changes">
                        @error('current_password')
                            <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="status-form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="status-select-control @error('new_password') is-invalid @enderror" placeholder="At least 8 characters">
                        @error('new_password')
                            <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="status-form-group">
                        <label for="new_password_confirmation">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="status-select-control" placeholder="Re-type new password">
                    </div>
                    
                    <button type="submit" class="btn-update-status">
                        <i class="fa-solid fa-floppy-disk"></i> Save Profile Settings
                    </button>
                </div>
            </div>
        </div>
        
    </form>
</div>
@endsection
