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
    <div class="col-12 col-xl-10 mx-auto">

      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
              <h5 class="mb-1">Hồ sơ cá nhân</h5>
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

          <form action="{{ url('/ho-so') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">
              <div class="col-12 col-md-4">
                <div class="border rounded-3 p-3 text-center h-100">
                  @php
                    $anhDaiDien = $nguoiDung->anhDaiDien
                        ? asset('storage/' . $nguoiDung->anhDaiDien)
                        : asset('assets/images/user/avatar-1.jpg');
                  @endphp

                  <img src="{{ $anhDaiDien }}"
                       alt="Ảnh đại diện"
                       class="rounded-circle border mb-3"
                       style="width: 130px; height: 130px; object-fit: cover;">

                  <h6 class="mb-1">{{ $nguoiDung->hoTen ?? $nguoiDung->tenDangNhap }}</h6>

                  <div class="text-muted small mb-3">
                    {{ $nguoiDung->vaiTro ?? 'Người dùng' }}
                  </div>

                  <input type="file"
                         name="anhDaiDien"
                         class="form-control"
                         accept="image/*">

                  <small class="text-muted d-block mt-2">
                    Chấp nhận jpg, png, webp. Tối đa 2MB.
                  </small>
                </div>
              </div>

              <div class="col-12 col-md-8">
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <label class="form-label">Tên đăng nhập</label>
                    <input type="text"
                           class="form-control"
                           value="{{ $nguoiDung->tenDangNhap }}"
                           disabled>
                  </div>

                  <div class="col-12 col-md-6">
                    <label class="form-label">Vai trò</label>
                    <input type="text"
                           class="form-control"
                           value="{{ $nguoiDung->vaiTro }}"
                           disabled>
                  </div>

                  <div class="col-12">
                    <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                    <input type="text"
                           name="hoTen"
                           class="form-control"
                           value="{{ old('hoTen', $nguoiDung->hoTen) }}"
                           required>
                  </div>

                  <div class="col-12 col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           value="{{ old('email', $nguoiDung->email) }}">
                  </div>

                  <div class="col-12 col-md-6">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text"
                           name="sdt"
                           class="form-control"
                           value="{{ old('sdt', $nguoiDung->sdt) }}">
                  </div>

                  <div class="col-12 col-md-6">
                    <label class="form-label">Giới tính</label>
                    <select name="gioiTinh" class="form-select">
                      <option value="">-- Chọn giới tính --</option>
                      <option value="Nam" {{ old('gioiTinh', $nguoiDung->gioiTinh) == 'Nam' ? 'selected' : '' }}>Nam</option>
                      <option value="Nữ" {{ old('gioiTinh', $nguoiDung->gioiTinh) == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                      <option value="Khác" {{ old('gioiTinh', $nguoiDung->gioiTinh) == 'Khác' ? 'selected' : '' }}>Khác</option>
                    </select>
                  </div>

                  <div class="col-12 col-md-6">
                    <label class="form-label">Ngày sinh</label>
                    <input type="date"
                           name="ngaySinh"
                           class="form-control"
                           value="{{ old('ngaySinh', $nguoiDung->ngaySinh) }}">
                  </div>

                  <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    <button type="reset" class="btn btn-light border">
                      Làm mới
                    </button>

                    <button type="submit" class="btn btn-primary">
                      Lưu thay đổi
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection