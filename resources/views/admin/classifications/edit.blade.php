@extends('admin.layouts.app')



@section('content')
<div class="form-container-modern edit-form-container">
    <div class="form-header-modern">
        <h2><i class="fa-solid fa-pen-to-square"></i> Edit Classification</h2>
        <a href="{{ route('admin.classifications.index') }}" class="btn-back-modern">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.classifications.update', $classification->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="edit-form-card">
            
            <!-- Tag Name -->
            <div class="frameless-group">
                <label for="name">Classification Name</label>
                <input type="text" name="name" id="name" class="frameless-input" placeholder="e.g. Manga, Poetry, Biography" value="{{ old('name', $classification->name) }}" required>
            </div>
            
            <!-- Color Selector -->
            <div class="frameless-group">
                <label>Badge Color Theme</label>
                <div class="color-radio-group">
                     @foreach(['green', 'blue', 'gold', 'red', 'purple', 'teal', 'orange', 'pink'] as $color)
                        <label class="color-radio-label">
                            <input type="radio" name="color" value="{{ $color }}" {{ old('color', $classification->color) == $color ? 'checked' : '' }}>
                            <span class="color-pill-preview color-{{ $color }}">
                                <i class="fa-solid fa-circle"></i> {{ ucfirst($color) }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Status Selector -->
            <div class="frameless-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="frameless-select" required>
                    <option value="active" {{ old('status', $classification->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $classification->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="edit-form-actions">
                <a href="{{ route('admin.classifications.index') }}" class="btn-modern-secondary text-decoration-none">Cancel</a>
                <button type="submit" class="btn-modern-primary btn-update-pad">
                    <i class="fa-solid fa-check"></i> Update Classification
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
