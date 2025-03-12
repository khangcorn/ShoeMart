@if (session('status'))
<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('password.email') }}" method="POST">
@csrf
<div class="mb-3">
    <label for="email" class="form-label">Email Address</label>
    <input type="email" id="email" name="email" class="form-control" required autofocus>
</div>

<button type="submit" class="btn btn-primary w-100">Send Password Reset Link</button>

<div class="text-center mt-3">
    <a href="{{ route('login') }}">Back to Login</a>
</div>
</form>