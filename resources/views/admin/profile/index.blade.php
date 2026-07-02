@extends('admin.layouts.app')

@section('title', 'Booknest Admin - Staff Profile Settings')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/profile.css') }}?v=1.0.1">
@endsection

@section('content')
<div class="dashboard-wrapper-new">
    
    <div class="form-header-modern margin-bottom-20">
        <h2><i class="fa-solid fa-user-gear"></i> Staff Profile Settings</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn-back-dashboard">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-success-custom">
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
                        <h3 class="font-size-1-3-margin-top-5-0-0-0">{{ $staff->name }}</h3>
                        <div class="margin-top-5">
                            <span class="badge-status status-sub-{{ $staff->role?->name === 'admin' ? 'active' : 'inactive' }}">
                                {{ strtoupper($staff->role?->name ?? 'Staff') }}
                            </span>
                        </div>
                    </div>

                    <div class="info-list border-top-cream-pad-15">
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
                        <i class="fa-solid fa-address-card profile-card-icon"></i>
                        <h4>Personal Details</h4>
                    </div>
                    
                    <div class="status-form-group">
                        <label for="name">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $staff->name) }}" class="status-select-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <span class="form-error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="status-form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $staff->email) }}" class="status-select-control @error('email') is-invalid @enderror" required>
                        @error('email')
                            <span class="form-error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="status-form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $staff->phone) }}" class="status-select-control @error('phone') is-invalid @enderror" required>
                        @error('phone')
                            <span class="form-error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="status-form-group">
                        <label for="image">Profile Picture (Avatar)</label>
                        <input type="file" name="image" id="image" class="status-select-control @error('image') is-invalid @enderror" accept="image/*">
                        <small class="form-help-text">Max file size: 2MB. Allowed formats: JPG, PNG, JPEG, GIF.</small>
                        @error('image')
                            <span class="form-error-msg">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Password Changes Card -->
                <div class="detail-card">
                    <div class="detail-card-header">
                        <i class="fa-solid fa-key profile-card-icon"></i>
                        <h4>Security Settings (Password Update)</h4>
                    </div>

                    <div class="status-form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="status-select-control @error('current_password') is-invalid @enderror" placeholder="Enter current password to make changes">
                        @error('current_password')
                            <span class="form-error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="status-form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="status-select-control @error('new_password') is-invalid @enderror" placeholder="At least 8 characters">
                        @error('new_password')
                            <span class="form-error-msg">{{ $message }}</span>
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
