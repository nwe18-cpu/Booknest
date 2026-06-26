@extends('admin.layouts.app')

@section('content')
<div class="dashboard-wrapper-new">
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <div class="split-workspace-container">
        
        <!-- Left Column: Searchable Authors list -->
        <div class="workspace-left-pane">
            <div class="card-header-flex authors-header-actions">
                <div class="header-title-group">
                    <h3><i class="fa-solid fa-user-pen"></i> Authors</h3>
                    <p>Manage authors & add books</p>
                </div>
                <a href="{{ route('admin.authors.create') }}" class="btn-primary-submit text-decoration-none btn-add-author-full">
                    <i class="fa-solid fa-plus"></i> Add New Author
                </a>
            </div>

            <!-- Instant Search Box -->
            <div class="search-wrapper-modern">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="author-search" class="search-input-modern" placeholder="Search authors...">
            </div>

            <!-- Scrollable Authors list -->
            <div class="authors-list-scrollable" id="authors-list">
                @forelse($authors ?? [] as $author)
                    <div class="author-item-card" data-id="{{ $author->id }}" data-name="{{ $author->name }}" data-books-count="{{ $author->items_count }}">
                        <div class="author-info-left">
                            @if($author->image)
                                <img src="{{ asset('storage/' . $author->image) }}" alt="{{ $author->name }}" class="table-book-cover author-avatar-img">
                            @else
                                <div class="active-author-no-photo author-no-avatar-small">
                                    {{ strtoupper(substr($author->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h5>{{ $author->name }}</h5>
                                <p class="text-author-muted"><span class="books-count-badge">{{ $author->items_count }}</span> {{ $author->items_count == 1 ? 'book' : 'books' }}</p>
                            </div>
                        </div>
                        <button class="btn-add-book-inline-icon" title="Manage Books">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                @empty
                    <div class="table-empty-state empty-table-pad">
                        📭 No authors found.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Interactive Workspace Detail Pane -->
        <div class="workspace-right-pane" id="workspace-right">
            
            <!-- Default Empty State -->
            <div class="workspace-empty-state" id="empty-state">
                <div class="illustration-circle">
                    <i class="fa-solid fa-book-bookmark"></i>
                </div>
                <h4>No Author Selected</h4>
                <p>Select an author from the list on the left to manage their book catalog or add a new book directly under their name.</p>
            </div>

            <!-- Active Author Workspace (Hidden initially) -->
            <div id="active-workspace" class="active-workspace-layout">
                
                <!-- 1. Active Author Profile Header -->
                <div class="active-author-header-card">
                    <div id="author-header-avatar"></div>
                    <div class="active-author-details">
                        <h3 id="active-author-title">Author Name</h3>
                        <p id="active-author-subtitle">0 books in catalog</p>
                    </div>
                    <div class="active-author-actions-group">
                        <a href="#" id="edit-author-btn" class="btn-csv-export"><i class="fa-solid fa-pen-to-square"></i> Edit Profile</a>
                        <form id="delete-author-form" action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this author? All associated books must be deleted first.');" class="display-inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete-author" id="delete-author-btn">
                                <i class="fa-solid fa-trash-can"></i> Delete Profile
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 2. Premium Book Creator Card -->
                <div class="creator-card">
                    <div class="creator-card-header">
                        <h4 id="creator-card-title"><i class="fa-solid fa-plus-circle"></i> Add New Book</h4>
                    </div>

                    <form id="book-creator-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="author_id" id="form-author-id">
                        <input type="hidden" name="book_id" id="form-book-id">
                        
                        <!-- Frameless Input Grid -->
                        <div class="creator-form-grid">
                            <div class="frameless-group">
                                <label for="book-name">Book Title</label>
                                <input type="text" name="name" id="book-name" class="frameless-input" placeholder="e.g., The Midnight Library" required>
                            </div>
                            
                            <div class="frameless-group">
                                <label for="book-price">Price (Ks)</label>
                                <input type="number" name="price" id="book-price" step="1" min="0" class="frameless-input" placeholder="0" required>
                            </div>

                            <!-- Spacer to align Stock Quantity directly below Price -->
                            <div class="frameless-group grid-spacer"></div>

                            <div class="frameless-group stock-quantity-group">
                                <label for="book-stock">Stock Quantity</label>
                                <input type="number" name="stock_quantity" id="book-stock" min="0" class="frameless-input" placeholder="e.g., 50" required>
                            </div>

                            <div class="frameless-group pages-input-hidden">
                                <!-- Standard page length configuration default -->
                                <input type="hidden" name="pages" id="book-pages" value="250">
                            </div>
                        </div>

                        <!-- Classification Selection Pills -->
                        <div class="classification-pills-row classification-row">
                            <label class="classification-label">
                                Book Classifications
                                <button type="button" onclick="openClassificationModal()" class="btn-manage-classifications" title="Manage Classifications">
                                    <i class="fa-solid fa-circle-plus"></i>
                                </button>
                            </label>
                            <div id="parent-classifications-list" class="classification-pills-list">
                                @php
                                    $icons = [
                                        'Fiction' => 'fa-feather',
                                        'Non-Fiction' => 'fa-brain',
                                        'Translation' => 'fa-language',
                                        'Literature' => 'fa-feather',
                                        'Science' => 'fa-brain'
                                    ];
                                @endphp
                                @foreach($classifications ?? [] as $class)
                                    <label class="checkbox-pill-label">
                                        <input type="checkbox" name="classifications[]" value="{{ $class->id }}" class="hidden-checkbox">
                                        <span class="pill-badge-design badge-classification-{{ $class->color }}">
                                            <i class="fa-solid {{ $icons[$class->name] ?? 'fa-tag' }}"></i> {{ $class->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Split Uploaders Zone -->
                        <div class="split-uploaders-row">
                            <!-- Cover Dragzone -->
                            <div class="dropzone-container" id="cover-dropzone">
                                <input type="file" name="image" id="file-cover" accept="image/*">
                                <div class="dropzone-content" id="cover-zone-content">
                                    <i class="fa-solid fa-image dropzone-icon"></i>
                                    <div class="dropzone-text">Book Cover Image</div>
                                    <div class="dropzone-subtext">Drop or click (PNG, JPG)</div>
                                </div>
                            </div>

                            <!-- PDF Dragzone -->
                            <div class="dropzone-container" id="pdf-dropzone">
                                <input type="file" name="pdf_file" id="file-pdf" accept="application/pdf">
                                <div class="dropzone-content" id="pdf-zone-content">
                                    <i class="fa-solid fa-file-pdf dropzone-icon"></i>
                                    <div class="dropzone-text">PDF Book Content</div>
                                    <div class="dropzone-subtext">Drop or click (PDF format)</div>
                                </div>
                            </div>
                        </div>

                        <!-- Collapsible Synopsis Tray -->
                        <div class="synopsis-tray-container">
                            <div class="synopsis-tray-toggle" id="synopsis-toggle">
                                <i class="fa-solid fa-chevron-down"></i>
                                <span>Add Book Synopsis / Description</span>
                            </div>
                            <div class="synopsis-textarea-wrapper" id="synopsis-wrapper">
                                <textarea name="description" placeholder="Write book details, chapter brief or outline here..."></textarea>
                            </div>
                        </div>

                        <div class="creator-card-actions flex-align-center-gap-10">
                            <button type="button" class="btn-csv-export btn-cancel-edit-author-book" id="cancel-edit-book-btn">
                                <i class="fa-solid fa-xmark"></i> Cancel Edit
                            </button>
                            <button type="submit" class="btn-modern-primary" id="submit-book-btn">
                                <i class="fa-solid fa-paper-plane"></i> Save to Deck
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 3. Existing Books Stack List -->
                <div>
                    <div class="book-deck-header">
                        Existing Book Deck
                    </div>
                    <div class="book-deck-container book-deck-container-margin" id="books-deck-list">
                        <!-- Dynamically populated -->
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Classifications Manager Modal -->
    <div id="classification-modal" class="modal-overlay display-none">
        <div class="modal-card">
            <div class="modal-card-header">
                <h4><i class="fa-solid fa-tags"></i> Manage Classifications</h4>
                <button type="button" class="close-modal-btn" onclick="closeClassificationModal()">&times;</button>
            </div>
            <div class="modal-card-body modal-body-layout">
                <!-- Left Side: Table List of tags -->
                <div class="modal-left-table-col">
                    <div class="table-responsive">
                        <table class="modern-table" id="modal-classifications-table">
                            <thead>
                                <tr>
                                    <th>Tag Name</th>
                                    <th>Status</th>
                                    <th class="modal-action-header-width">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="modal-classifications-tbody">
                                <!-- Dynamically loaded via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Right Side: Create/Edit Form -->
                <div class="modal-right-form-col">
                    <h5 id="modal-form-title" class="modal-form-title-text">
                        <i class="fa-solid fa-circle-plus"></i> Add New Classification
                    </h5>
                    <form id="modal-classification-form">
                        <input type="hidden" id="modal-class-id">
                        
                        <div class="frameless-group modal-input-width">
                            <label for="modal-class-name">Classification Name</label>
                            <input type="text" id="modal-class-name" class="frameless-input" placeholder="e.g. Fiction, Non-Fiction, Poetry" required>
                        </div>
                        
                        <div class="frameless-group modal-input-width">
                            <label>Badge Color Theme</label>
                            <div class="color-radio-group">
                                @foreach(['green', 'blue', 'gold', 'red', 'purple', 'teal', 'orange', 'pink'] as $color)
                                    <label class="color-radio-label">
                                        <input type="radio" name="modal_color" value="{{ $color }}" {{ $color == 'green' ? 'checked' : '' }}>
                                        <span class="color-pill-preview color-{{ $color }}">
                                            <i class="fa-solid fa-circle"></i> {{ ucfirst($color) }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Status selector (only shown when editing) -->
                        <div class="frameless-group modal-input-width display-none" id="modal-class-status-group">
                            <label for="modal-class-status">Status</label>
                            <select id="modal-class-status" class="modal-select-status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="modal-actions-footer">
                            <button type="button" id="modal-cancel-edit-btn" class="btn-csv-export modal-btn-cancel" onclick="resetModalForm()">Cancel</button>
                            <button type="submit" id="modal-submit-btn" class="btn-modern-primary modal-btn-submit">
                                <i class="fa-solid fa-paper-plane"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin/authors.js') }}?v=1.0.4"></script>
@endsection

