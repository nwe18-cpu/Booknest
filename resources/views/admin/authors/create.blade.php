@extends('admin.layouts.app')

@section('content')
<div class="form-container-modern">
    <div class="form-header-modern">
        <h2><i class="fa-solid fa-user-plus"></i> Add New Author</h2>
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

    <form action="{{ route('admin.authors.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group-modern">
            <label for="name">Author Name <span class="required">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Enter author name" required>
        </div>

        <div class="form-group-modern mt-25">
            <label for="image">Author Photo</label>
            <div class="file-drop-area">
                <input type="file" id="image" name="image" accept="image/*" class="file-input-pointer">
                <p class="file-help-text">Supported formats: JPG, PNG, JPEG. Max size: 2MB</p>
            </div>
        </div>

        <div class="form-actions-modern">
            <a href="{{ route('admin.authors.index') }}" class="btn-modern-secondary text-decoration-none">Cancel</a>
            <button type="submit" class="btn-modern-primary">
                <i class="fa-solid fa-floppy-disk"></i> Save Author
            </button>
        </div>
    </form>
</div>
@endsection
