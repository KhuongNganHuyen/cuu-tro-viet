@php
  $vaiTro = session('vaiTro');

  $layout = match ($vaiTro) {
      'Quản trị viên' => 'layouts.admin',
      'Người dùng' => 'layouts.user',
      default => 'layouts.user',
  };
@endphp

@extends($layout)

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12 col-lg-7 col-xl-6 mx-auto">

      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
              <h5 class="mb-1">Đổi mật khẩu</h5>
            </div>

            <a href="javascript:history.back()" class="btn btn-light border">
              Quay lại
            </a>
          </div>
        </div>

        <div class="card-body">
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
              <div class="fw-semibold mb-1">Vui lòng kiểm tra lại thông tin:</div>
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ url('/doi-mat-khau') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <label class="form-label">
                Mật khẩu hiện tại <span class="text-danger">*</span>
              </label>
              <input type="password"
                     name="matKhauCu"
                     class="form-control"
                     placeholder="Nhập mật khẩu hiện tại"
                     required>
            </div>

            <div class="mb-3">
              <label class="form-label">
                Mật khẩu mới <span class="text-danger">*</span>
              </label>
              <input type="password"
                     name="matKhauMoi"
                     class="form-control"
                     placeholder="Nhập mật khẩu mới"
                     required>
              <small class="text-muted">
                Mật khẩu mới phải có ít nhất 6 ký tự.
              </small>
            </div>

            <div class="mb-4">
              <label class="form-label">
                Xác nhận mật khẩu mới <span class="text-danger">*</span>
              </label>
              <input type="password"
                     name="matKhauMoi_confirmation"
                     class="form-control"
                     placeholder="Nhập lại mật khẩu mới"
                     required>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <a href="{{ url('/ho-so') }}" class="btn btn-light border">
                Hủy
              </a>

              <button type="submit" class="btn btn-primary">
                Đổi mật khẩu
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection