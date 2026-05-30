<!DOCTYPE html>
<html lang="vi">
<head>
  <title>Đăng ký | Cứu Trợ Việt</title>

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
              <p class="text-muted mb-0">Tạo tài khoản người dùng</p>
            </div>

            @if ($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form action="{{ url('/register') }}" method="POST">
              @csrf

              <div class="mb-3">
                <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                <input type="text" name="hoTen" class="form-control"
                  value="{{ old('hoTen') }}"
                  placeholder="Nhập họ tên">
              </div>

              <div class="mb-3">
                <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                <input type="text" name="tenDangNhap" class="form-control"
                  value="{{ old('tenDangNhap') }}"
                  placeholder="Nhập tên đăng nhập">
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                  <input type="password" name="matKhau" class="form-control"
                    placeholder="Tối thiểu 6 ký tự">
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                  <input type="password" name="matKhau_confirmation" class="form-control"
                    placeholder="Nhập lại mật khẩu">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" class="form-control"
                    value="{{ old('email') }}"
                    placeholder="Ví dụ: user@gmail.com">
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label">Số điện thoại</label>
                  <input type="text" name="sdt" class="form-control"
                    value="{{ old('sdt') }}"
                    placeholder="Ví dụ: 0901234567">
                </div>
              </div>

              <small class="text-muted d-block mb-3">
                Cần nhập ít nhất Email hoặc Số điện thoại để hệ thống có thông tin liên hệ.
              </small>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Giới tính</label>
                  <select name="gioiTinh" class="form-control">
                    <option value="">-- Chọn giới tính --</option>
                    <option value="Nam" {{ old('gioiTinh') == 'Nam' ? 'selected' : '' }}>Nam</option>
                    <option value="Nữ" {{ old('gioiTinh') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                    <option value="Khác" {{ old('gioiTinh') == 'Khác' ? 'selected' : '' }}>Khác</option>
                  </select>
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label">Ngày sinh</label>
                  <input type="date" name="ngaySinh" class="form-control"
                    value="{{ old('ngaySinh') }}">
                </div>
              </div>

              <button type="submit" class="btn btn-primary w-100">
                Đăng ký
              </button>
            </form>

            <div class="text-center mt-4">
              <span class="text-muted">Đã có tài khoản?</span>
              <a href="{{ url('/login') }}" class="link-primary">
                Đăng nhập
              </a>
            </div>

            <div class="text-center mt-2">
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