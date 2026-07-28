@if (session('success'))
    <div class="alert alert-success" role="status">
        <div>
            <p class="alert-title">Success</p>
            <p>{{ session('success') }}</p>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-error" role="alert">
        <div>
            <p class="alert-title">Error</p>
            <p>{{ session('error') }}</p>
        </div>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning" role="status">
        <div>
            <p class="alert-title">Notice</p>
            <p>{{ session('warning') }}</p>
        </div>
    </div>
@endif

@if (session('status'))
    <div class="alert alert-info" role="status">
        <div>
            <p>{{ session('status') }}</p>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-error" role="alert">
        <div>
            <p class="alert-title">Please fix the following</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
