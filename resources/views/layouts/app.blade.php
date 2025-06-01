<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description"
        content="Website rekomendasi smartphone terbaik menggunakan metode TOPSIS. Temukan smartphone yang sesuai dengan kebutuhan dan budget Anda.">
    <meta name="keywords"
        content="rekomendasi smartphone, smartphone terbaik, SPK smartphone, handphone terbaik, TOPSIS, sistem pendukung keputusan">
    <meta name="author" content="RecomHp">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="RecomHp - Website Rekomendasi Smartphone Terbaik">
    <meta property="og:description"
        content="Temukan smartphone terbaik sesuai dengan kebutuhan dan budget Anda menggunakan metode TOPSIS">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="canonical" href="{{ url()->current() }}">

    <title>@yield('title', 'Rekomendasi Smartphone Terbaik') - RecomHp</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

    <!-- Preload critical resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- AOS Animation -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <!-- Custom Dark Theme -->
    <link rel="stylesheet" href="{{ asset('css/spk-dark.css') }}">

    <style>
        .navbar-brand {
            font-weight: bold;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 1rem 0;
            margin-top: 2rem;
        }

        /* Lazy load images */
        .lazy-load {
            opacity: 0;
            transition: opacity 0.3s;
        }

        .lazy-load.loaded {
            opacity: 1;
        }
    </style>
    @yield('styles')

    <!-- Schema.org structured data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "RecomHp",
        "url": "{{ url('/') }}",
        "description": "Website rekomendasi smartphone terbaik menggunakan metode TOPSIS",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "{{ url('/list-hp') }}?q={search_term}",
            "query-input": "required name=search_term"
        }
    }
    </script>
</head>

<body>
    <!-- Preloader -->
    <div class="preloader" id="preloader"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-mobile-alt me-2"></i>RecomHp
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link {{ request()->is('smartphones*') ? 'active' : '' }}"
                            href="{{ route('smartphones.index') }}">Daftar Smartphone</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('recommendation*') ? 'active' : '' }}"
                            href="{{ route('recommendation.form') }}">Rekomendasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('list-hp.index') ? 'active' : '' }}"
                            href="{{ route('list-hp.index') }}">
                            <i class="fas fa-list me-2"></i>ListHp
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content with spacing for fixed navbar -->
    <div class="main-content" style="padding-top: 80px;">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" data-aos="fade-down">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert" data-aos="fade-down">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <footer class="footer mt-5">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-12">
                    <p class="mb-0">
                        <span class="text-gradient">RecomHp</span>
                        <span class="text-muted">Metode TOPSIS &copy; {{ date('Y') }}</span>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper - defer loading for performance -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" defer></script>
    <!-- AOS Animation Library - defer loading for performance -->
    <script src="https://unpkg.com/aos@next/dist/aos.js" defer></script>
    <script>
        // Initialize AOS
        document.addEventListener('DOMContentLoaded', function() {
            // Load AOS after page is fully loaded
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    easing: 'ease-in-out',
                    once: true
                });
            }

            // Remove preloader when page loaded
            setTimeout(function() {
                const preloader = document.getElementById('preloader');
                if (preloader) {
                    preloader.style.opacity = '0';
                    setTimeout(() => {
                        preloader.style.display = 'none';
                    }, 500);
                }
            }, 500);

            // Lazy load images
            const lazyImages = document.querySelectorAll('img.lazy-load');
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.add('loaded');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                lazyImages.forEach(img => imageObserver.observe(img));
            } else {
                // Fallback for browsers without IntersectionObserver support
                lazyImages.forEach(img => {
                    img.src = img.dataset.src;
                    img.classList.add('loaded');
                });
            }
        });
    </script>
    @yield('scripts')
    @stack('scripts')
</body>

</html>
