<!--
=========================================================
* Soft UI Dashboard - v1.0.3
=========================================================
-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
    <title>
        {{ $article->title }} - Blog
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
                        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark"
                                href="{{ route('pelanggan.articles') }}">Articles</a>
                        </li>
                        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Detail</li>
                    </ol>
                    <h6 class="font-weight-bolder mb-0">Article Detail</h6>
                </nav>
            </div>
        </nav>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <span class="badge bg-gradient-primary">{{ $article->category->name }}</span>
                            <h3 class="mt-3">{{ $article->title }}</h3>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-user me-2"></i>
                                    <span class="text-sm me-3">{{ $article->user->name }}</span>
                                    <i class="fa fa-calendar me-2"></i>
                                    <span class="text-sm me-3">{{ $article->created_at->format('d M Y') }}</span>
                                    <i class="fa fa-eye me-2"></i>
                                    <span class="text-sm">{{ $article->views }} views</span>
                                </div>
                                <div class="d-flex gx-2">
                                    <form action="{{ route('pelanggan.articles.like', $article->slug) }}" method="POST"
                                        class="me-2">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-sm {{ $article->isLikedBy(auth()->user()) ? 'btn-danger' : 'btn-outline-danger' }} mb-0">
                                            <i class="fa fa-heart me-1"></i>
                                            {{ $article->isLikedBy(auth()->user()) ? 'Liked' : 'Like' }}
                                            ({{ $article->likes }})
                                        </button>
                                    </form>
                                    <form action="{{ route('pelanggan.articles.bookmark', $article->slug) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-sm {{ $article->isBookmarkedBy(auth()->user()) ? 'btn-dark' : 'btn-outline-dark' }} mb-0">
                                            <i class="fa fa-bookmark me-1"></i>
                                            {{ $article->isBookmarkedBy(auth()->user()) ? 'Saved' : 'Save' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($article->image)
                                <div class="text-center mb-4">
                                    <img src="{{ asset('storage/' . $article->image) }}"
                                        class="img-fluid border-radius-lg shadow"
                                        style="max-height: 500px; width: 100%; object-fit: cover;">
                                </div>
                            @endif

                            <div class="article-content" style="font-size: 1.1rem; line-height: 1.8;">
                                {!! nl2br(e($article->content)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Comments ({{ $comments->count() }})</h6>
                        </div>
                        <div class="card-body">
                            <!-- Comment Form -->
                            <form action="{{ route('pelanggan.articles.comments.store', $article->slug) }}"
                                method="POST" class="mb-4">
                                @csrf
                                <div class="form-group mb-2">
                                    <textarea name="content" class="form-control" rows="3"
                                        placeholder="Leave a comment..." required></textarea>
                                </div>
                                <button type="submit" class="btn bg-gradient-primary btn-sm">Post Comment</button>
                            </form>

                            <!-- Comments List -->
                            @forelse($comments as $comment)
                                <div class="d-flex mb-4">
                                    <div class="flex-shrink-0">
                                        <img src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : asset('assets/img/team-2.jpg') }}"
                                            class="avatar rounded-circle me-3">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mt-0 mb-1">{{ $comment->user->name }} <small
                                                class="text-muted ms-2">{{ $comment->created_at->diffForHumans() }}</small>
                                        </h6>
                                        <p class="text-sm mb-2">{{ $comment->content }}</p>

                                        <!-- Reply Button -->
                                        <button class="btn btn-link btn-sm text-primary p-0"
                                            onclick="toggleReplyForm({{ $comment->id }})">Reply</button>

                                        <!-- Reply Form -->
                                        <div id="reply-form-{{ $comment->id }}" class="mt-2 d-none">
                                            <form action="{{ route('pelanggan.articles.comments.store', $article->slug) }}"
                                                method="POST">
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <div class="form-group mb-2">
                                                    <textarea name="content" class="form-control" rows="2"
                                                        placeholder="Write a reply..." required></textarea>
                                                </div>
                                                <button type="submit" class="btn bg-gradient-secondary btn-sm">Submit
                                                    Reply</button>
                                            </form>
                                        </div>

                                        <!-- Replies -->
                                        @foreach($comment->replies as $reply)
                                            <div class="d-flex mt-3">
                                                <div class="flex-shrink-0">
                                                    <img src="{{ $reply->user->avatar ? asset('storage/' . $reply->user->avatar) : asset('assets/img/team-2.jpg') }}"
                                                        class="avatar rounded-circle me-3 text-sm"
                                                        style="width: 36px; height: 36px;">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mt-0 mb-1 text-sm">{{ $reply->user->name }} <small
                                                            class="text-muted ms-2">{{ $reply->created_at->diffForHumans() }}</small>
                                                    </h6>
                                                    <p class="text-sm mb-0">{{ $reply->content }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-muted my-3">No comments yet. Be the first to comment!</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function toggleReplyForm(commentId) {
                    var form = document.getElementById('reply-form-' + commentId);
                    if (form.classList.contains('d-none')) {
                        form.classList.remove('d-none');
                    } else {
                        form.classList.add('d-none');
                    }
                }
            </script>

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