@extends('admin.layouts.app')

@section('title', 'Booknest Admin - Staff Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/staff.css') }}?v=1.0.3">
@endsection

@section('content')
<div class="dashboard-wrapper-new">
    
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <ul class="margin-0 padding-left-20">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="data-table-card">
        <div class="card-header-flex">
            <div class="header-title-group">
                <h3><i class="fa-solid fa-users-gear"></i> Staff Management</h3>
                <p>Manage console access permissions, add new administrative roles, and toggle account activation status.</p>
            </div>
            <button onclick="openAddModal()" class="btn-primary-submit">
                <i class="fa-solid fa-user-plus"></i> Add New Staff
            </button>
        </div>

        <!-- Filters Grid -->
        <form method="GET" action="{{ route('admin.staff.index') }}" class="filters-row-card-inline staff-filters-grid">
            <button type="submit" class="display-none"></button>
            <div>
                <input type="text" name="search" placeholder="Search by name, email, phone..." value="{{ request('search') }}" class="filter-input">
            </div>
            <div>
                <select name="role_id" class="filter-input" onchange="this.form.requestSubmit()">
                    <option value="">-- Select Role --</option>
                    @foreach($roles ?? [] as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="filter-input" onchange="this.form.requestSubmit()">
                    <option value="">-- Account Status --</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div>
                <a href="{{ route('admin.staff.index') }}" class="btn-filter-reset" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="modern-table staff-table">
                <thead>
                    <tr>
                        <th>Staff Member</th>
                        <th>Email</th>
                        <th class="tablet-hide">Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-align-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffs ?? [] as $staff)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="staff-avatar-wrapper">
                                        <div class="staff-avatar-initials">
                                            {{ substr($staff->name ?? 'S', 0, 1) }}
                                        </div>
                                    </div>
                                    <strong>{{ $staff->name ?? '-' }}</strong>
                                </div>
                            </td>
                            <td style="white-space: nowrap;">{{ $staff->email ?? '-' }}</td>
                            <td class="tablet-hide">{{ $staff->phone ?? '-' }}</td>
                            <td>
                                <span class="badge-role-staff {{ $staff->role?->name === 'admin' ? 'badge-role-admin' : 'badge-role-member' }}">
                                    {{ ucfirst($staff->role?->name ?? 'Staff') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-status-staff {{ $staff->status === 'active' ? 'badge-status-active' : 'badge-status-inactive' }}">
                                    {{ ucfirst($staff->status ?? 'active') }}
                                </span>
                            </td>
                            <td class="text-align-right">
                                <div class="actions-wrap-flex">
                                    <button onclick="openEditModal({{ json_encode($staff) }})" class="btn-csv-export btn-edit-actions" title="Edit Staff">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    
                                    @if($staff->id !== auth()->guard('staff')->id())
                                        <form action="{{ route('admin.staff.destroy', $staff->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this staff account? This cannot be undone.');" class="display-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-csv-export btn-delete-actions" title="Delete Staff">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn-csv-export btn-delete-actions opacity-50 cursor-not-allowed" title="Cannot delete yourself" onclick="alert('Access Denied: You cannot delete your own logged-in account.');">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-empty-state empty-state-padding">
                                📭 No staff members found matching the criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $staffs->links() }}
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal-overlay display-none" id="add-staff-modal">
    <div class="modal-card">
        <div class="modal-card-header">
            <h4><i class="fa-solid fa-user-plus"></i> Add New Staff Member</h4>
            <button onclick="closeAddModal()" class="close-modal-btn">&times;</button>
        </div>
        <div class="modal-card-body">
            <form action="{{ route('admin.staff.store') }}" method="POST">
                @csrf
                <div class="form-group-modern">
                    <label for="add-name">Full Name <span class="required-star">*</span></label>
                    <input type="text" name="name" id="add-name" placeholder="e.g. John Doe" required>
                </div>
                
                <div class="form-group-modern">
                    <label for="add-email">Email Address <span class="required-star">*</span></label>
                    <input type="email" name="email" id="add-email" placeholder="e.g. johndoe@booknest.com" required>
                </div>

                <div class="form-group-modern">
                    <label for="add-phone">Phone Number</label>
                    <input type="text" name="phone" id="add-phone" placeholder="e.g. 09123456789" pattern="^[0-9]{9,11}$" minlength="9" maxlength="11" title="Phone number must be between 9 and 11 digits (numbers only)">
                </div>

                <div class="form-group-modern">
                    <label for="add-role">System Role <span class="required-star">*</span></label>
                    <select name="role_id" id="add-role" required>
                        <option value="">-- Choose Role --</option>
                        @foreach($roles ?? [] as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-modern">
                    <label for="add-password">Login Password <span class="required-star">*</span></label>
                    <input type="password" name="password" id="add-password" placeholder="Minimum 6 characters" required>
                </div>

                <div class="form-group-modern">
                    <label for="add-status">Account Status <span class="required-star">*</span></label>
                    <select name="status" id="add-status" required>
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="modal-footer-modern">
                    <button type="button" onclick="closeAddModal()" class="btn-modern-cancel">Cancel</button>
                    <button type="submit" class="btn-modern-primary btn-save-pad">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay display-none" id="edit-staff-modal">
    <div class="modal-card">
        <div class="modal-card-header">
            <h4><i class="fa-solid fa-user-pen"></i> Edit Staff Account</h4>
            <button onclick="closeEditModal()" class="close-modal-btn">&times;</button>
        </div>
        <div class="modal-card-body">
            <form id="edit-staff-form" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group-modern">
                    <label for="edit-name">Full Name <span class="required-star">*</span></label>
                    <input type="text" name="name" id="edit-name" required>
                </div>
                
                <div class="form-group-modern">
                    <label for="edit-email">Email Address <span class="required-star">*</span></label>
                    <input type="email" name="email" id="edit-email" required>
                </div>

                <div class="form-group-modern">
                    <label for="edit-phone">Phone Number</label>
                    <input type="text" name="phone" id="edit-phone" pattern="^[0-9]{9,11}$" minlength="9" maxlength="11" title="Phone number must be between 9 and 11 digits (numbers only)">
                </div>

                <div class="form-group-modern">
                    <label for="edit-role">System Role <span class="required-star">*</span></label>
                    <select name="role_id" id="edit-role" required>
                        @foreach($roles ?? [] as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-modern">
                    <label for="edit-password">Change Password</label>
                    <input type="password" name="password" id="edit-password" placeholder="Leave empty to keep current password">
                    <p class="hint-text-xs">Only fill if you want to set a new login password.</p>
                </div>

                <div class="form-group-modern">
                    <label for="edit-status">Account Status <span class="required-star">*</span></label>
                    <select name="status" id="edit-status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="modal-footer-modern">
                    <button type="button" onclick="closeEditModal()" class="btn-modern-cancel">Cancel</button>
                    <button type="submit" class="btn-modern-primary btn-save-pad">Update Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Modal Selectors
    const addModal = document.getElementById('add-staff-modal');
    const editModal = document.getElementById('edit-staff-modal');
    const editForm = document.getElementById('edit-staff-form');

    // Add Modal Handlers
    function openAddModal() {
        if (addModal) {
            addModal.classList.remove('display-none');
            document.body.style.overflow = 'hidden'; // Lock background scroll
        }
    }

    function closeAddModal() {
        if (addModal) {
            addModal.classList.add('display-none');
            document.body.style.overflow = '';
        }
    }

    // Edit Modal Handlers
    function openEditModal(staff) {
        if (editModal && editForm) {
            // Populate fields
            document.getElementById('edit-name').value = staff.name || '';
            document.getElementById('edit-email').value = staff.email || '';
            document.getElementById('edit-phone').value = staff.phone || '';
            document.getElementById('edit-role').value = staff.role_id || '';
            document.getElementById('edit-status').value = staff.status || 'active';
            document.getElementById('edit-password').value = ''; // Reset password field

            // Set Form action URL dynamically
            editForm.action = `/admin/staff/${staff.id}`;

            editModal.classList.remove('display-none');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeEditModal() {
        if (editModal) {
            editModal.classList.add('display-none');
            document.body.style.overflow = '';
        }
    }

    // Close Modals on Overlay Click
    window.addEventListener('click', function (e) {
        if (e.target === addModal) closeAddModal();
        if (e.target === editModal) closeEditModal();
    });
</script>
@endsection
