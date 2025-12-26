@extends('layouts.app')

@section('title', 'Checkout')

@section('content')

<style>
    /* Supaya konten tidak ketiban navbar */
    .page-offset {
        margin-top: 90px;
    }

    /* Sticky sidebar aman */
    .sticky-summary {
        position: sticky;
        top: 120px;
    }
</style>

<div class="container py-5 page-offset">
    <div class="row justify-content-center">
        <div class="col-lg-11">

            <!-- TITLE -->
            <div class="text-center mb-5">
                <h2 class="fw-bold text-primary">
                    <i class="bi bi-cart-check me-2"></i> Checkout Pesanan
                </h2>
                <p class="text-muted">
                    Lengkapi data pengiriman sebelum melanjutkan pembayaran
                </p>
            </div>

            @if($cart->items->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-cart-x display-1 text-muted"></i>
                    <h4 class="mt-3">Keranjang Masih Kosong</h4>
                    <p class="text-muted">Silakan pilih produk terlebih dahulu.</p>
                    <a href="{{ route('catalog.index') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-shop"></i> Mulai Belanja
                    </a>
                </div>
            @else

            @php
                $subtotal = $cart->items->sum(fn($item) => ($item->product?->price ?? 0) * $item->quantity);
                $shippingCost = 15000;
                $total = $subtotal + $shippingCost;
            @endphp

            <div class="row g-4">

                <!-- FORM PENGIRIMAN -->
                <div class="col-lg-7">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-primary text-white rounded-top-4">
                            <h5 class="mb-0">
                                <i class="bi bi-truck me-2"></i> Data Pengiriman
                            </h5>
                        </div>

                        <div class="card-body p-4">
                            <form action="{{ route('checkout.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nama Penerima</label>
                                    <input type="text" name="name" class="form-control form-control-lg"
                                        value="{{ old('name', auth()->user()->name ?? '') }}" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">No. HP</label>
                                        <input type="text" name="phone" class="form-control form-control-lg"
                                            value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Email (Opsional)</label>
                                        <input type="email" name="email" class="form-control form-control-lg"
                                            value="{{ old('email', auth()->user()->email ?? '') }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Alamat Lengkap</label>
                                    <textarea name="address" rows="4" class="form-control form-control-lg" required>{{ old('address', auth()->user()->address ?? '') }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Catatan (Opsional)</label>
                                    <textarea name="notes" rows="3" class="form-control"></textarea>
                                </div>

                                <button type="submit"
                                    class="btn btn-success btn-lg w-100 fw-bold shadow-sm">
                                    <i class="bi bi-credit-card-2-front me-2"></i>
                                    Buat Pesanan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- RINGKASAN PESANAN -->
                <div class="col-lg-5">
                    <div class="card shadow-lg border-0 rounded-4 sticky-summary">
                        <div class="card-header bg-dark text-white rounded-top-4">
                            <h5 class="mb-0">
                                <i class="bi bi-receipt-cutoff me-2"></i> Ringkasan Pesanan
                            </h5>
                        </div>

                        <div class="card-body p-4">

                            @foreach($cart->items as $item)
                                <div class="d-flex justify-content-between mb-3">
                                    <div>
                                        <strong>{{ $item->product?->name }}</strong><br>
                                        <small class="text-muted">
                                            {{ $item->quantity }} x Rp {{ number_format($item->product?->price) }}
                                        </small>
                                    </div>
                                    <span class="fw-semibold">
                                        Rp {{ number_format($item->quantity * $item->product?->price) }}
                                    </span>
                                </div>
                            @endforeach

                            <hr>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($subtotal) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Ongkir</span>
                                <span>Rp {{ number_format($shippingCost) }}</span>
                            </div>

                            <div class="d-flex justify-content-between bg-light p-3 rounded">
                                <strong>Total</strong>
                                <strong class="text-success">
                                    Rp {{ number_format($total) }}
                                </strong>
                            </div>

                            <div class="mt-3 text-muted small">
                                <i class="bi bi-shield-check"></i>
                                Pembayaran aman & terenkripsi
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            @endif
        </div>
    </div>
</div>

@endsection