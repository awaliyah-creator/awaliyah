{{-- ================================================
     FILE: resources/views/layouts/app.blade.php
     FUNGSI: Master layout halaman publik (Tailwind)
     ================================================ --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO --}}
    <title>@yield('title', 'Toko Online') - {{ config('app.name') }}</title>
    <meta name="description" content="@yield('meta_description', 'Toko online terpercaya dengan produk berkualitas')">

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- AOS --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    {{-- Vite (Tailwind) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Page Specific CSS --}}
    @stack('styles')
</head>

<body class="font-inter bg-gray-100 text-gray-800">

    {{-- =============================
         NAVBAR
         ============================= --}}
    @include('profile.partials.navbar')

    {{-- =============================
         FLASH MESSAGE
         ============================= --}}
    <div class="max-w-7xl mx-auto px-4 mt-4">
        @include('profile.partials.flash-messages')
    </div>

    {{-- =============================
         MAIN CONTENT
         ============================= --}}
    <main class="min-h-screen">
        @yield('content')
        @yield('head')
    </main>

    {{-- =============================
         FOOTER
         ============================= --}}
    @include('profile.partials.footer')

    {{-- =============================
         SCRIPTS
         ============================= --}}
         @stack('styles')
    @stack('scripts')

    {{-- AOS --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ once: true });
    </script>

    {{-- Wishlist Button --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.wishlist-btn').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();

                    const productId = this.dataset.productId;
                    const icon = this.querySelector('i');

                    fetch(`/wishlist/${productId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.added) {
                            icon.classList.replace('bi-heart', 'bi-heart-fill');
                            icon.classList.add('text-red-500');
                        } else {
                            icon.classList.replace('bi-heart-fill', 'bi-heart');
                            icon.classList.remove('text-red-500');
                        }
                    });
                });
            });
        });
    </script>

</body>
</html>