<!--
=========================================================
* Soft UI Dashboard - v1.0.3
=========================================================

* Product Page: https://www.creative-tim.com/product/soft-ui-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)

* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
    <title>
        Blog Articles
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('assets/css/soft-ui-dashboard.css?v=1.0.3') }}" rel="stylesheet" />
</head>

<body class="g-sidenav-show  bg-gray-100">
    @include('pelanggan.partials.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
            navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark"
                                href="{{ route('pelanggan.dashboard') }}">Pages</a>
                        </li>
                        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Articles</li>
                    </ol>
                    <h6 class="font-weight-bolder mb-0">Articles</h6>
                </nav>
            </div>
        </nav>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <!-- Filter Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-3">
                            <form action="{{ route('pelanggan.articles') }}" method="GET">
                                <div class="row">
                                    <div class="col-md-5 mb-3 mb-md-0">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Search articles..." value="{{ request('search') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <select name="category" class="form-select">
                                            <option value="">All Categories</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn bg-gradient-primary mb-0">
                                                <i class="fas fa-filter me-1"></i> Filter
                                            </button>
                                            @if(request()->filled('search') || request()->filled('category'))
                                                <a href="{{ route('pelanggan.articles') }}"
                                                    class="btn bg-gradient-secondary mb-0 px-3" title="Reset Filter">
                                                    <i class="fas fa-redo me-1"></i> Reset
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if(request()->filled('search') || request()->filled('category'))
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <small class="text-secondary">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Filter aktif:
                                                @if(request()->filled('search'))
                                                    <span class="badge badge-sm bg-gradient-info">Search:
                                                        "{{ request('search') }}"</span>
                                                @endif
                                                @if(request()->filled('category'))
                                                    <span class="badge badge-sm bg-gradient-info">Kategori:
                                                        "{{ $categories->where('id', request('category'))->first()->name ?? request('category') }}"</span>
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                @forelse ($articles as $article)
                    <div class="col-xl-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1">
                                <a href="{{ route('pelanggan.articles.show', $article->slug) }}" class="d-block">
                                    <img src="{{ $article->image ? asset('storage/' . $article->image) : asset('assets/img/home-decor-1.jpg') }}"
                                        class="img-fluid border-radius-lg shadow">
                                </a>
                            </div>
                            <div class="card-body pt-2">
                                <span class="text-gradient text-primary text-uppercase text-xs font-weight-bold my-2">
                                    {{ $article->category->name ?? 'Uncategorized' }}
                                </span>
                                <a href="{{ route('pelanggan.articles.show', $article->slug) }}"
                                    class="card-title h5 d-block text-darker">
                                    {{ Str::limit($article->title, 50) }}
                                </a>
                                <p class="card-description mb-4">
                                    {{ Str::limit($article->excerpt, 100) }}
                                </p>
                                <div class="author align-items-center">
                                    <div class="name ps-3">
                                        <span>{{ $article->user->name }}</span>
                                        <div class="stats">
                                            <small>Posted on {{ $article->created_at->format('d M Y') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-white" role="alert">
                            <strong>Info!</strong> Tidak ada artikel yang ditemukan.
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="row mt-4">
                <div class="col-12 d-flex justify-content-center">
                    {{ $articles->links('pagination::bootstrap-5') }}
                </div>
            </div>

            <footer class="footer pt-3  ">
                <div class="container-fluid">
                    <div class="row align-items-center justify-content-lg-between">
                        <div class="col-lg-6 mb-lg-0 mb-4">
                            <div class="copyright text-center text-sm text-muted text-lg-start">
                                ©
                                <script>
                                    document.write(new Date().getFullYear())
                                </script>,
                                made with <i class="fa fa-heart"></i> by
                                <a href="https://www.creative-tim.com" class="font-weight-bold" target="_blank">Creative
                                    Tim</a>
                                for a better web.
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </main>
    <!--   Core JS Files   -->
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('assets/js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>
</body>

</html>