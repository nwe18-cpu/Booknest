@extends('layouts.app')

@section('title', 'Edit Profile - Booknest')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/dashboard.css') }}?v=1.1.0">
@endsection

@section('content')
<div class="profile-settings-wrapper">
    <div class="container">
        <!-- Breadcrumb & Back Link -->
        <div class="profile-header-nav">
            <a href="{{ route('customer.dashboard') }}" class="back-dashboard-btn">
                <i class="fa-solid fa-arrow-left"></i> Back to My Bookshelf
            </a>
            <h1>Profile Settings</h1>
        </div>

        @if(session('success'))
            <div class="profile-alert profile-alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="profile-alert profile-alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-settings-form">
            @csrf
            
            <div class="profile-grid">
                <!-- Left Column: Avatar Settings -->
                <div class="profile-sidebar">
                    <div class="profile-card avatar-card">
                        <h3>Profile Picture</h3>
                        <div class="avatar-upload-container">
                            <div class="avatar-preview-wrapper">
                                <img id="avatar-preview" src="{{ $customer->image ? asset('storage/' . $customer->image) : asset('images/avatar-placeholder.png') }}" alt="Avatar Preview">
                            </div>
                            <div class="avatar-upload-btn-wrapper">
                                <label for="image-upload" class="avatar-upload-label">
                                    <i class="fa-solid fa-camera"></i> Upload New Picture
                                </label>
                                <input type="file" id="image-upload" name="image" accept="image/*">
                            </div>
                            <p class="avatar-upload-hint">Max file size 2MB (JPG, PNG, GIF)</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Settings Form -->
                <div class="profile-main-content">
                    <!-- General Settings -->
                    <div class="profile-card">
                        <h3><i class="fa-solid fa-user-gear"></i> Basic Information</h3>
                        <div class="form-group-grid">
                            <div class="form-group">
                                <label for="name">Full Name <span class="required-star">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required placeholder="e.g. John Doe">
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address <span class="required-star">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" required placeholder="example@domain.com">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number <span class="required-star">*</span></label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" required placeholder="e.g. 09xxxxxxxx">
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $customer->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $customer->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $customer->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="dob">Date of Birth</label>
                                <input type="date" id="dob" name="dob" value="{{ old('dob', $customer->dob ? \Illuminate\Support\Carbon::parse($customer->dob)->format('Y-m-d') : '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Address Settings -->
                    <div class="profile-card">
                        <h3><i class="fa-solid fa-location-dot"></i> Shipping Address</h3>
                        <div class="form-group">
                            <label for="address">Full Address</label>
                            <textarea id="address" name="address" rows="3" placeholder="e.g. No. 12, Bahan Road, Yangon.">{{ old('address', $customer->address) }}</textarea>
                        </div>
                    </div>

                    <!-- Password Settings -->
                    <div class="profile-card">
                        <h3><i class="fa-solid fa-lock"></i> Security & Password Change</h3>
                        <p class="form-section-desc">Fill in the details below only if you want to change your password.</p>
                        
                        <div class="form-group-grid">
                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" id="current_password" name="current_password" placeholder="Enter your current password">
                            </div>
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" id="new_password" name="new_password" placeholder="Min 8 characters">
                            </div>
                            <div class="form-group">
                                <label for="new_password_confirmation">Confirm New Password</label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Repeat new password">
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="profile-form-actions">
                        <button type="submit" class="profile-save-btn">
                            <i class="fa-solid fa-floppy-disk"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const imageUpload = document.getElementById('image-upload');
    const avatarPreview = document.getElementById('avatar-preview');

    if (imageUpload && avatarPreview) {
        imageUpload.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                // Check max size 2MB
                if (file.size > 2 * 1024 * 1024) {
                    alert('Profile image size must be 2MB or less.');
                    imageUpload.value = ''; // clear input
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    avatarPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endsection
