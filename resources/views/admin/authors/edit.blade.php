@extends('admin.layouts.app')

@section('content')
<div class="form-container-modern">
    <div class="form-header-modern">
        <h2><i class="fa-solid fa-user-pen"></i> Edit Author</h2>
        <a href="{{ route('admin.authors.index') }}" class="btn-back-modern">
            <i class="fa-solid fa-arrow-left"></i> Back to Authors
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

    <form action="{{ route('admin.authors.update', $author->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group-modern">
            <label for="name">Author Name <span class="required">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $author->name) }}" placeholder="Enter author name" required>
        </div>

        <div class="form-group-modern mt-25">
            <label>Current Photo</label>
            <div class="mb-15">
                @if($author->image)
                    <img src="{{ asset('storage/' . $author->image) }}" alt="{{ $author->name }}" class="table-author-photo author-edit-photo">
                @else
                    <div class="author-no-photo author-no-photo-edit">
                        {{ strtoupper(substr($author->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            
            <label for="image">Change Photo</label>
            <div class="file-drop-area">
                <input type="file" id="image" name="image" accept="image/*" class="file-input-pointer">
                <p class="file-help-text">Supported formats: JPG, PNG, JPEG. Max size: 2MB. Leave blank to keep current photo.</p>
            </div>
        </div>

        <div class="form-actions-modern">
            <a href="{{ route('admin.authors.index') }}" class="btn-modern-secondary text-decoration-none">Cancel</a>
            <button type="submit" class="btn-modern-primary">
                <i class="fa-solid fa-floppy-disk"></i> Update Author
            </button>
        </div>
    </form>
</div>
@endsection
