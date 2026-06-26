@extends('admin.layouts.app')

@section('content')
<div class="dashboard-wrapper-new">
    
    @if(session('success'))
        <div class="alert alert-success banners-alert-success">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="data-table-card banners-table-card">
        <div class="card-header-flex banners-card-header">
            <div class="header-title-group">
                <h3 class="banners-header-title">
                    <i class="fa-solid fa-image"></i> Banners
                </h3>
                <p class="banners-header-desc">Manage home page sliding banners, scheduling, and order</p>
            </div>
            <button onclick="openAddModal()" class="btn-modern-primary">
                <i class="fa-solid fa-plus"></i> Add New Banner
            </button>
        </div>

        <div class="banners-grid-wrapper">
            @forelse($banners ?? [] as $banner)
                <div class="banner-admin-card">
                    <!-- Banner Image -->
                    <div class="banner-card-image" style="background-image: url('{{ asset('storage/' . $banner->image) }}');">
                        <div class="banner-card-badge">
                            Order: {{ $banner->order }}
                        </div>
                    </div>
                    
                    <!-- Banner Details -->
                    <div class="banner-card-details">
                        <h4 class="banner-card-title">{{ $banner->title ?? 'Untitled Banner' }}</h4>
                        <p class="banner-card-content">{{ $banner->content ?? '-' }}</p>
                        
                        <!-- Scheduling Information -->
                        <div class="banner-card-dates">
                            <div class="date-row">
                                <span class="date-label">
                                    <i class="fa-solid fa-calendar-plus banners-icon-green"></i> Start Date
                                </span>
                                <span class="date-value">{{ $banner->start_date ?? 'Immediate' }}</span>
                            </div>
                            <div class="date-row">
                                <span class="date-label">
                                    <i class="fa-solid fa-calendar-minus banners-icon-red"></i> End Date
                                </span>
                                <span class="date-value">{{ $banner->end_date ?? 'Infinite' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="banner-card-actions">
                        <button onclick="openEditModal({{ json_encode($banner) }})" class="btn-action-icon btn-action-edit">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </button>
                        <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this banner?');" class="banners-form-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action-icon btn-action-delete">
                                <i class="fa-solid fa-trash-can"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="banners-empty-state">
                    📭 No banners configured yet. Add one to get started!
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal-overlay display-none" id="add-modal">
    <div class="modal-card">
        <div class="modal-card-header">
            <h4><i class="fa-solid fa-plus-circle"></i> Add New Banner</h4>
            <button onclick="closeAddModal()" class="close-modal-btn">&times;</button>
        </div>
        <div class="modal-card-body">
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group-modern">
                    <label for="add-title">Title</label>
                    <input type="text" name="title" id="add-title" placeholder="e.g., Nurture Your Dreams with Books">
                </div>
                
                <div class="form-group-modern">
                    <label for="add-content">Content</label>
                    <textarea name="content" id="add-content" placeholder="e.g., Discover a wide range of premium books at affordable prices."></textarea>
                </div>

                <div class="form-group-modern">
                    <label for="add-image">Banner Image <span class="banners-red-required">*</span></label>
                    <input type="file" name="image" id="add-image" required class="banners-file-input">
                    <p class="banners-hint-text">Recommended size: 1920x800px. Max size: 4MB.</p>
                </div>

                <div class="form-group-modern">
                    <label for="add-order">Display Order</label>
                    <input type="number" name="order" id="add-order" value="0" min="0" required>
                </div>

                <div class="banners-flex-row-gap">
                    <div class="form-group-modern banners-flex-1">
                        <label for="add-start-date">Start Date</label>
                        <input type="date" name="start_date" id="add-start-date">
                        <p class="banners-hint-xs">Immediate if empty</p>
                    </div>

                    <div class="form-group-modern banners-flex-1">
                        <label for="add-end-date">End Date</label>
                        <input type="date" name="end_date" id="add-end-date">
                        <p class="banners-hint-xs">Infinite if empty</p>
                    </div>
                </div>

                <div class="banners-modal-footer">
                    <button type="button" onclick="closeAddModal()" class="btn-modern-secondary banners-btn-modal-action">Cancel</button>
                    <button type="submit" class="btn-modern-primary banners-btn-modal-action">Save Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay display-none" id="edit-modal">
    <div class="modal-card">
        <div class="modal-card-header">
            <h4><i class="fa-solid fa-pen-to-square"></i> Edit Banner</h4>
            <button onclick="closeEditModal()" class="close-modal-btn">&times;</button>
        </div>
        <div class="modal-card-body">
            <form id="edit-form" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group-modern">
                    <label for="edit-title">Title</label>
                    <input type="text" name="title" id="edit-title">
                </div>
                
                <div class="form-group-modern">
                    <label for="edit-content">Content</label>
                    <textarea name="content" id="edit-content"></textarea>
                </div>

                <div class="form-group-modern">
                    <label for="edit-image">Replace Image (Optional)</label>
                    <input type="file" name="image" id="edit-image" class="banners-file-input">
                    <p class="banners-hint-text">Leave empty to keep current image. Recommended size: 1920x800px.</p>
                </div>

                <div class="form-group-modern">
                    <label for="edit-order">Display Order</label>
                    <input type="number" name="order" id="edit-order" min="0" required>
                </div>

                <div class="banners-flex-row-gap">
                    <div class="form-group-modern banners-flex-1">
                        <label for="edit-start-date">Start Date</label>
                        <input type="date" name="start_date" id="edit-start-date">
                    </div>

                    <div class="form-group-modern banners-flex-1">
                        <label for="edit-end-date">End Date</label>
                        <input type="date" name="end_date" id="edit-end-date">
                    </div>
                </div>

                <div class="banners-modal-footer">
                    <button type="button" onclick="closeEditModal()" class="btn-modern-secondary banners-btn-modal-action">Cancel</button>
                    <button type="submit" class="btn-modern-primary banners-btn-modal-action">Update Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin/banners.js') }}?v=1.0.1"></script>
@endsection
