{{-- resources/views/admin/products/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manajemen Produk')

@section('content')
<div class="row">
    <div class="col-lg-12">

        {{-- Flash Message --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary fw-bold">Daftar Buku</h5>
                {{-- Filter Kategori --}}
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterCategory" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-filter"></i>{{ request('category') ? $categories->firstWhere('slug', request('category'))->name : 'Semua Kategori' }}</button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="filterCategory">
                            <li><a class="dropdown-item" href="{{ route('admin.products.index') }}">Semua Kategori</a></li>
                            <li><hr class="dropdown-divider"></li>
                                @foreach($categories as $category)
                                    <li>
                                        <a class="dropdown-item {{ request('category') == $category->slug ? 'active' : '' }}" href="{{ route('admin.products.index', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                                    </li>
                                @endforeach
                        </ul>
                </div>  
                        

                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-lg"></i> Tambah Buku
                </button>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nama Produk</th>
                                <th class="text-center">Kategori</th>
                                <th class="text-center">Halaman</th>
                                <th class="text-center">Genre</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Stok</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            @if($product->primaryImage)
                                                <img src="{{ $product->primaryImage->image_url }}"
                                                     class="rounded me-2"
                                                     width="40" height="40"
                                                     style="object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center me-2"
                                                     style="width:40px;height:40px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif

                                            <div>
                                                <div class="fw-bold">{{ $product->name }}</div>
                                                <small class="text-muted">{{ $product->slug }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $product->category->name }}</span>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark">{{ $product->weight }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($product->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-danger">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $product->stock }}</span>
                                    </td>

                                    <td class="text-end pe-4">
                                        <div class="d-inline-flex gap-2">
                                            <button class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editProduk{{ $product->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <form action="{{ route('admin.products.destroy', $product) }}"
                                              method="POST"
                                              class="d-inline-flex"
                                              onsubmit="return confirm('Hapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Tidak ada produk tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- ===================== MODAL EDIT (DI LUAR TABLE) ===================== --}}
@foreach($products as $product)
<div class="modal fade" id="editProduk{{ $product->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content"
              action="{{ route('admin.products.update', $product) }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title">Edit Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Produk</label>
                    <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Kategori</label>
                    <select name="category_id" class="form-select">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="4">{{ $product->description }}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Harga (Rp)</label>
                        <input type="number" name="price" class="form-control" value="{{ $product->price }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Stok</label>
                        <input type="number" name="stock" class="form-control" value="{{ $product->stock }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Halaman</label>
                        <input type="number" name="weight" class="form-control" value="{{ $product->weight }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Gambar (Opsional)</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                        {{ $product->is_active ? 'checked' : '' }}>
                    <label class="form-check-label">Aktif</label>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach

{{-- ===================== MODAL CREATE ===================== --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg   ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Produk Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Produk</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Pilih Kategori...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Harga (Rp)</label>
                            <input type="number" name="price" class="form-control" value="{{ old('price') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Stok</label>
                            <input type="number" name="stock" class="form-control" value="{{ old('stock') }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Halaman</label>
                        <input type="number" name="weight" class="form-control" value="{{ old('weight') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Gambar (Opsional)</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    </div>
                    
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"{{ old('is_active') ? 'checked' : '' }}>
                        <label class="form-check-label">Aktif</label>
                    </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Produk</button>
            </div>
                </form>
        </div>
    </div>
</div>

@endsection