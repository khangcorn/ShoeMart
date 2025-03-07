


@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
<div class="container">
    <h2>Đăng Nhập</h2>
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        @error('email')
        <div class="text-danger">{{ $message }}</div>
    @enderror
        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        @error('password')
        <div class="text-danger">{{ $message }}</div>
    @enderror
        <button type="submit" class="btn btn-success">Đăng nhập</button>
    </form>
  <a href="{{route('register.form')}}">Đăng Ký </a>
  <a href="{{route('forget-password.form')}}">Quên Mật Khẩu </a>