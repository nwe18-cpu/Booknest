@extends('admin.layouts.app')



@section('content')
<div class="dashboard-wrapper-new">
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="data-table-card">
        <div class="card-header-flex">
            <div class="header-title-group">
                <h3><i class="fa-solid fa-book"></i> Book Catalog</h3>
                <p>Manage books, check stock levels and view book details</p>
            </div>
            <a href="{{ route('admin.authors.index') }}" class="btn-primary-submit text-decoration-none">
                <i class="fa-solid fa-plus"></i> Add New Book
            </a>
        </div>

        <div class="table-responsive">
            <table class="modern-table catalog-table">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Book Details</th>
                        <th class="tablet-hide">Pages</th>
                        <th>Price</th>
                        <th>Stock Left</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books ?? [] as $book)
                        <tr>
                            <td>
                                @if($book->image)
                                    <img src="{{ asset('storage/' . $book->image) }}" alt="{{ $book->name }}" class="table-book-cover">
                                @else
                                    <div class="table-book-cover-placeholder" title="{{ $book->name }}">
                                        <i class="fa-solid fa-book"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="catalog-details-cell" style="display: flex; flex-direction: column; gap: 4px;">
                                    <strong style="color: var(--text-main); font-size: 0.95rem;">{{ $book->name }}</strong>
                                    <span class="text-author" style="font-size: 0.8rem; color: var(--text-muted);">by {{ $book->author?->name ?? 'Unknown Author' }}</span>
                                    <div class="catalog-tags-container" style="margin-top: 2px;">
                                        @foreach($book->classifications ?? [] as $class)
                                            <span class="badge-tag-classification classification-{{ $class->color }}">{{ $class->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                            <td class="tablet-hide">{{ $book->pages }} pages</td>
                            <td><strong>{{ number_format($book->price) }} Ks</strong></td>
                            <td>
                                @if($book->stock_quantity < 5)
                                    <span class="badge-stock-danger">
                                        {{ $book->stock_quantity }} units (Low)
                                    </span>
                                @else
                                    <span class="staff-status-badge">
                                        {{ $book->stock_quantity }} units
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-empty-state">
                                📭 No books registered in the catalog yet. Add one to get started!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">
            {{ $books->links() }}
        </div>
    </div>
</div>
@endsection
