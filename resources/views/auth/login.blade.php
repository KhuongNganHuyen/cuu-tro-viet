<!DOCTYPE html>
<html lang="vi">
<head>
  <title>Đăng nhập | Cứu Trợ Việt</title>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

  <link rel="stylesheet" href="{{ asset('mantis/assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('mantis/assets/css/style-preset.css') }}">
</head>

<body>
  <div class="auth-main">
    <div class="auth-wrapper v1">
      <div class="auth-form">
        <div class="card my-5">
          <div class="card-body">
            <div class="text-center mb-4">
              <h3 class="mb-2">Cứu Trợ Việt</h3>
              <p class="text-muted mb-0">Đăng nhập vào hệ thống</p>
            </div>

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

            @if ($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form action="{{ url('/login') }}" method="POST">
              @csrf

              <div class="mb-3">
                <label class="form-label">Tên đăng nhập</label>
                <input type="text" name="tenDangNhap" class="form-control"
                  value="{{ old('tenDangNhap') }}"
                  placeholder="Nhập tên đăng nhập">
              </div>

              <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="matKhau" class="form-control"
                  placeholder="Nhập mật khẩu">
              </div>

              <div class="text-center mb-4">
                <a href="{{ url('/register') }}" class="text-muted">Đăng ký tài khoản</a>
              </div>

              <button type="submit" class="btn btn-primary w-100">
                Đăng nhập
              </button>
            </form>

            <div class="text-center mt-4">
              <a href="{{ url('/') }}" class="text-muted">
                Về trang chủ
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('mantis/assets/js/plugins/bootstrap.min.js') }}"></script>
</body>
</html>