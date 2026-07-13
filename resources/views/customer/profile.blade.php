@extends('layouts.app')

@section('title', 'Edit Profile - Booknest')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/dashboard.css') }}?v=1.4.4">
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
                                <img id="avatar-preview" src="{{ $customer->image ? asset('storage/' . $customer->image) : 'https://ui-avatars.com/api/?name=' . urlencode($customer->name) . '&background=f1e4d8&color=5c3a21&bold=true' }}" alt="Avatar Preview">
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
                                <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required placeholder="Name">
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address <span class="required-star">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" required placeholder="example@gmail.com">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number <span class="required-star">*</span></label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" required placeholder="(09)*********" pattern="^[0-9]{9,11}$" minlength="9" maxlength="11" title="Phone number must be between 9 and 11 digits (numbers only)">
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

                    <!-- Saved Shipping Addresses (SRS Multiple Saved Addresses) -->
                    <div class="profile-card" id="saved-addresses-card">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap: wrap; gap: 10px;">
                            <h3 style="margin-bottom:0;"><i class="fa-solid fa-address-book"></i> Saved Shipping Addresses</h3>
                            <button type="button" class="btn-add-address" onclick="openAddressModal()">
                                <i class="fa-solid fa-plus"></i> Add New Address
                            </button>
                        </div>
                        
                        <div class="address-list-container" id="address-list">
                            @forelse($customer->addresses as $addr)
                                <div class="address-item-row" id="address-row-{{ $addr->id }}">
                                    <div style="flex-grow:1;">
                                        <div class="address-receiver-name">
                                            <span>{{ $addr->receiver_name }}</span>
                                            @if($addr->is_default)
                                                <span style="font-size:0.65rem; background:#e2f2e5; color:#2d7a43; padding:2px 6px; border-radius:4px; font-weight:700;">DEFAULT</span>
                                            @endif
                                        </div>
                                        <div class="address-details">
                                            <i class="fa-solid fa-phone" style="font-size:0.75rem;"></i> {{ $addr->phone_number }}
                                            @if($addr->email)
                                                | <i class="fa-solid fa-envelope" style="font-size:0.75rem;"></i> {{ $addr->email }}
                                            @endif
                                        </div>
                                        <div class="address-line-text">
                                            <i class="fa-solid fa-location-pin" style="font-size:0.75rem;"></i> {{ $addr->address_line }}
                                        </div>
                                    </div>
                                    <div style="display:flex; gap:6px; flex-shrink:0;">
                                        @if(!$addr->is_default)
                                            <button type="button" class="btn-address-action btn-default-set" onclick="setDefaultAddress({{ $addr->id }})">Set Default</button>
                                        @endif
                                        <button type="button" class="btn-address-action" onclick="editAddress({{ json_encode($addr) }})" title="Edit Address"><i class="fa-solid fa-pen"></i></button>
                                        <button type="button" class="btn-address-action btn-delete" onclick="deleteAddress({{ $addr->id }})" title="Delete Address"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-mute" id="no-addresses-msg" style="font-size: 0.85rem; padding: 10px 0; color: #a0aec0;">No saved shipping addresses found. Add one to quickly use it during checkout.</p>
                            @endforelse
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
        
        <!-- AJAX Address Modal (Placed outside to prevent nested forms) -->
        <div id="address-modal" class="settings-modal-overlay">
            <div class="address-modal-card">
                <h3 id="modal-title" class="address-modal-title"><i class="fa-solid fa-location-dot"></i> Save Address</h3>
                <button type="button" class="address-modal-close-btn" onclick="closeAddressModal()">&times;</button>
                
                <form id="address-form" onsubmit="saveAddress(event)">
                    <input type="hidden" id="address-id" name="id">
                    <div class="address-form-group">
                        <label for="addr_receiver_name">Receiver Name <span class="required-star">*</span></label>
                        <input type="text" id="addr_receiver_name" required placeholder="Name">
                    </div>
                    <div class="address-form-group">
                        <label for="addr_phone_number">Phone Number <span class="required-star">*</span></label>
                        <input type="text" id="addr_phone_number" required placeholder="(09)*********" pattern="^[0-9]{9,11}$" minlength="9" maxlength="11" title="Phone number must be between 9 and 11 digits (numbers only)">
                    </div>
                    <div class="address-form-group">
                        <label for="addr_email">Email Address (Optional)</label>
                        <input type="email" id="addr_email" placeholder="example@gmail.com">
                    </div>
                    <div class="address-form-group">
                        <label for="addr_address_line">Full Address <span class="required-star">*</span></label>
                        <textarea id="addr_address_line" required rows="3" placeholder="No, Street, Township, City..."></textarea>
                    </div>
                    <div class="address-checkbox-group">
                        <input type="checkbox" id="addr_is_default">
                        <label for="addr_is_default">Set as default address</label>
                    </div>
                    
                    <div class="address-modal-actions">
                        <button type="button" class="address-modal-btn btn-secondary" onclick="closeAddressModal()">Cancel</button>
                        <button type="submit" class="address-modal-btn btn-primary">Save Address</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const csrfToken = "{{ csrf_token() }}";

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

    const addressModal = document.getElementById('address-modal');
    if (addressModal) {
        addressModal.addEventListener('click', function (e) {
            if (e.target === addressModal) {
                closeAddressModal();
            }
        });
    }
});

// Saved Address Functions
function openAddressModal() {
    const modal = document.getElementById('address-modal');
    const form = document.getElementById('address-form');
    const modalTitle = document.getElementById('modal-title');
    const addrIdInput = document.getElementById('address-id');

    if (form) form.reset();
    if (addrIdInput) addrIdInput.value = '';
    if (modalTitle) modalTitle.innerHTML = '<i class="fa-solid fa-location-dot"></i> Save Address';
    if (modal) modal.style.display = 'flex';
}

function closeAddressModal() {
    const modal = document.getElementById('address-modal');
    if (modal) modal.style.display = 'none';
}

function editAddress(addr) {
    const modal = document.getElementById('address-modal');
    const modalTitle = document.getElementById('modal-title');
    const addrIdInput = document.getElementById('address-id');
    const nameInput = document.getElementById('addr_receiver_name');
    const phoneInput = document.getElementById('addr_phone_number');
    const emailInput = document.getElementById('addr_email');
    const lineInput = document.getElementById('addr_address_line');
    const defaultCheckbox = document.getElementById('addr_is_default');

    if (modalTitle) modalTitle.innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Edit Address';
    if (addrIdInput) addrIdInput.value = addr.id;
    if (nameInput) nameInput.value = addr.receiver_name;
    if (phoneInput) phoneInput.value = addr.phone_number;
    if (emailInput) emailInput.value = addr.email || '';
    if (lineInput) lineInput.value = addr.address_line;
    if (defaultCheckbox) defaultCheckbox.checked = addr.is_default;
    if (modal) modal.style.display = 'flex';
}

function saveAddress(e) {
    e.preventDefault();
    
    const idVal = document.getElementById('address-id').value;
    const url = idVal ? `/customer/addresses/${idVal}` : '/customer/addresses';
    const method = idVal ? 'PUT' : 'POST';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            _method: method,
            receiver_name: document.getElementById('addr_receiver_name').value,
            phone_number: document.getElementById('addr_phone_number').value,
            email: document.getElementById('addr_email').value,
            address_line: document.getElementById('addr_address_line').value,
            is_default: document.getElementById('addr_is_default').checked ? 1 : 0
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeAddressModal();
            location.reload(); // Reload to refresh list and default badge
        } else {
            alert(data.message || 'Error occurred while saving address.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('An error occurred. Please try again.');
    });
}

function deleteAddress(id) {
    showCustomConfirm('Are you sure you want to delete this shipping address?', function() {
        fetch(`/customer/addresses/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error occurred while deleting address.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred. Please try again.');
        });
    });
}

function setDefaultAddress(id) {
    fetch(`/customer/addresses/${id}/default`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error occurred while setting default address.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('An error occurred. Please try again.');
    });
}
</script>
@endsection
