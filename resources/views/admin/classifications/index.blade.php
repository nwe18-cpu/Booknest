@extends('admin.layouts.app')




@section('content')
<div class="dashboard-wrapper-new">
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="split-workspace-container">
        
        <!-- Left Column: Classifications list table -->
        <div class="workspace-left-pane flex-1-3">
            <div class="card-header-flex margin-bottom-15">
                <div class="header-title-group">
                    <h3 class="font-size-1-25"><i class="fa-solid fa-tags"></i> Book Classifications</h3>
                    <p class="margin-top-2">Manage system classification tags & colors</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Tag Name</th>
                            <th>Status</th>
                            <th class="text-align-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classifications ?? [] as $class)
                            <tr>
                                <td>
                                    <span class="badge-tag-classification classification-{{ $class->color }}">
                                        {{ $class->name }}
                                    </span>
                                </td>
                                <td>
                                    <span class="staff-status-badge {{ $class->status == 'active' ? '' : 'badge-stock-danger' }} status-badge-pad">
                                        {{ ucfirst($class->status) }}
                                    </span>
                                </td>
                                <td class="text-align-right">
                                    <div class="actions-wrap-flex">
                                        <a href="{{ route('admin.classifications.edit', $class->id) }}" class="btn-csv-export btn-edit-actions" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('admin.classifications.destroy', $class->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this classification? Books linked to this tag will be detached.');" class="display-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-csv-export btn-delete-actions" title="Delete">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="table-empty-state empty-state-padding">
                                    📭 No classifications registered in the system yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrapper">
                {{ $classifications->links() }}
            </div>
        </div>

        <!-- Right Column: Add New Tag Form -->
        <div class="workspace-right-pane flex-1-0">
            <div class="creator-card creator-card-blue-glow">
                <div class="creator-card-header">
                    <h4><i class="fa-solid fa-circle-plus"></i> Add New Classification</h4>
                </div>

                @if ($errors->any())
                    <div class="alert alert-error error-alert-style">
                        <ul class="error-list-style">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.classifications.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-inner-wrapper">
                        <!-- Tag Name -->
                        <div class="frameless-group">
                            <label for="name">Classification Name</label>
                            <input type="text" name="name" id="name" class="frameless-input" placeholder="e.g. Manga, Poetry, Biography" value="{{ old('name') }}" required>
                        </div>
                        
                        <!-- Color Selector -->
                        <div class="frameless-group">
                            <label>Badge Color Theme</label>
                            <div class="color-radio-group">
                                @foreach(['green', 'blue', 'gold', 'red', 'purple', 'teal', 'orange', 'pink'] as $color)
                                    <label class="color-radio-label">
                                        <input type="radio" name="color" value="{{ $color }}" {{ (old('color') ?? 'green') == $color ? 'checked' : '' }}>
                                        <span class="color-pill-preview color-{{ $color }}">
                                            <i class="fa-solid fa-circle"></i> {{ ucfirst($color) }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="creator-card-actions form-actions-footer">
                        <button type="submit" class="btn-modern-primary btn-save-pad">
                            <i class="fa-solid fa-paper-plane"></i> Save Classification
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
