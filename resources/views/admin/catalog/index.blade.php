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
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Classifications</th>
                        <th>Pages</th>
                        <th>Price</th>
                        <th>Stock Left</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books ?? [] as $book)
                        <tr>
                            <td>
                                <img src="{{ $book->image ? asset('storage/' . $book->image) : asset('images/default-book.png') }}" alt="{{ $book->name }}" class="table-book-cover">
                            </td>
                            <td><strong>{{ $book->name }}</strong></td>
                            <td><span class="text-author">{{ $book->author?->name ?? 'Unknown Author' }}</span></td>
                            <td>
                                <div class="catalog-tags-container">
                                    @foreach($book->classifications ?? [] as $class)
                                        <span class="badge-tag-classification classification-{{ $class->color }}">{{ $class->name }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>{{ $book->pages }} pages</td>
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
