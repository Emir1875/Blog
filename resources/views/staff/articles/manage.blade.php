<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
    <title>
        Manage Articles - Admin
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
    @include('staff.partials.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
            navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark"
                                href="{{ route('staff.dashboard') }}">Pages</a></li>
                        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Manage Articles</li>
                    </ol>
                    <h6 class="font-weight-bolder mb-0">Manage Articles</h6>
                </nav>
            </div>
        </nav>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            @include('partials.alert')
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h6>My Articles</h6>
                            <div class="d-flex align-items-center">
                                <form action="{{ route('staff.articles.manage') }}" method="GET"
                                    class="d-flex me-3 align-items-center">
                                    <div class="input-group input-group-sm me-2">
                                        <span class="input-group-text text-body"><i class="fas fa-search"
                                                aria-hidden="true"></i></span>
                                        <input type="text" class="form-control" name="search" placeholder="Search..."
                                            value="{{ request('search') }}">
                                    </div>
                                    <select name="status" class="form-select form-select-sm" style="min-width: 120px;"
                                        onchange="this.form.submit()">
                                        <option value="">All Status</option>
                                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft
                                        </option>
                                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>
                                            Archived</option>
                                    </select>
                                </form>
                                <a href="{{ route('staff.articles.create') }}"
                                    class="btn btn-primary btn-sm mb-0">Create
                                    Article</a>
                            </div>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Article</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Author</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Status</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Category</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Created</th>
                                            <th class="text-secondary opacity-7"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($articles as $article)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div>
                                                            <img src="{{ $article->image ? asset('storage/' . $article->image) : asset('assets/img/home-decor-1.jpg') }}"
                                                                class="avatar avatar-sm me-3" alt="user1">
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ Str::limit($article->title, 40) }}
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $article->user->name }}</p>
                                                    <p class="text-xs text-secondary mb-0">
                                                        {{ ucfirst($article->user->role) }}
                                                    </p>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span
                                                        class="badge badge-sm bg-gradient-{{ $article->status == 'published' ? 'success' : ($article->status == 'draft' ? 'secondary' : 'warning') }}">{{ ucfirst($article->status) }}</span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">{{ $article->category->name }}</span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">{{ $article->created_at->format('d/m/y') }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <a href="{{ route('staff.articles.show', $article->slug) }}"
                                                        class="text-secondary font-weight-bold text-xs me-2"
                                                        data-toggle="tooltip" data-original-title="View article">
                                                        View
                                                    </a>
                                                    |
                                                    @if($article->status === 'draft')
                                                        <form action="{{ route('staff.articles.publish', $article->slug) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="border-0 bg-transparent text-success font-weight-bold text-xs mx-2 shadow-none"
                                                                data-toggle="tooltip" data-original-title="Publish article">
                                                                Publish
                                                            </button>
                                                        </form>
                                                        |
                                                    @elseif($article->status === 'published')
                                                        <form action="{{ route('staff.articles.unpublish', $article->slug) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="border-0 bg-transparent text-warning font-weight-bold text-xs mx-2 shadow-none"
                                                                data-toggle="tooltip" data-original-title="Unpublish article">
                                                                Unpublish
                                                            </button>
                                                        </form>
                                                        |
                                                    @endif
                                                    <a href="{{ route('staff.articles.edit', $article->slug) }}"
                                                        class="text-secondary font-weight-bold text-xs mx-2"
                                                        data-toggle="tooltip" data-original-title="Edit article">
                                                        Edit
                                                    </a>
                                                    |
                                                    <form action="{{ route('staff.articles.destroy', $article->slug) }}"
                                                        method="POST" class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="border-0 bg-transparent text-secondary font-weight-bold text-xs ms-2 shadow-none"
                                                            onclick="showDeleteModal(this.closest('form'), 'Artikel ini akan dihapus permanen!')">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center p-4">No articles found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        {{ $articles->links('pagination::bootstrap-5') }}
                    </div>
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

    @include('partials.delete-modal')

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