@if (session('success'))
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success text-white" role="alert">
                <strong>Success!</strong> {{ session('success') }}
            </div>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="row">
        <div class="col-12">
            <div class="alert alert-danger text-white" role="alert">
                <strong>Error!</strong> {{ session('error') }}
            </div>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="row">
        <div class="col-12">
            <div class="alert alert-danger text-white" role="alert">
                <strong>Whoops!</strong> Ada beberapa masalah dengan input Anda.<br><br>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif