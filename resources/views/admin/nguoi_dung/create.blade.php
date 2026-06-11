@extends('layouts.admin')

@section('title', 'Thêm người dùng | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Thêm người dùng</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Tổng quan</a></li>
          <li class="breadcrumb-item"><a href="{{ url('/admin/nguoi-dung') }}">Người dùng</a></li>
          <li class="breadcrumb-item" aria-current="page">Thêm</li>
        </ul>
      </div>
    </div>
  </div>
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

<form action="{{ url('/admin/nguoi-dung') }}" method="POST" enctype="multipart/form-data">
  @csrf

  <div class="row">
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-header">
          <h5 class="mb-0">Thông tin cá nhân</h5>
        </div>

        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Họ tên <span class="text-danger">*</span></label>
            <input type="text" name="hoTen" class="form-control" value="{{ old('hoTen') }}"
              placeholder="Nhập họ tên người dùng" autocomplete="off">
          </div>

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
              <input type="date" name="ngaySinh" class="form-control" value="{{ old('ngaySinh') }}" autocomplete="off">
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                placeholder="Ví dụ: user@gmail.com" autocomplete="off">
              <small class="text-muted">Cần nhập Email hoặc SĐT, không được để trống cả hai.</small>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Số điện thoại</label>
              <input type="text" name="sdt" class="form-control" value="{{ old('sdt') }}"
                placeholder="Ví dụ: 0987654321" autocomplete="off">
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Thông tin tài khoản</h5>
        </div>

        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
            <input type="text" name="tenDangNhap" class="form-control" value="{{ old('tenDangNhap') }}"
              placeholder="Nhập tên đăng nhập" autocomplete="off">
          </div>

          <div class="mb-3">
            <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
            <input type="password" name="matKhau" class="form-control"
              placeholder="Mật khẩu tối thiểu 6 ký tự">
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Vai trò</label>
              <select name="vaiTro" class="form-control">
                <option value="Người dùng" {{ old('vaiTro', 'Người dùng') == 'Người dùng' ? 'selected' : '' }}>
                  Người dùng
                </option>
                <option value="Quản trị viên" {{ old('vaiTro') == 'Quản trị viên' ? 'selected' : '' }}>
                  Quản trị viên
                </option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Trạng thái</label>
              <input type="text" class="form-control" value="Hoạt động" readonly>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-primary">
          Lưu
        </button>

        <a href="{{ url('/admin/nguoi-dung') }}" class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Ảnh đại diện</h5>
        </div>

        <div class="card-body">
          <div class="text-center mb-3">
            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center"
              style="width: 120px; height: 120px;">
              <i class="ti ti-user fs-1 text-muted"></i>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Chọn ảnh</label>
            <input type="file" name="anhDaiDien" class="form-control" accept="image/*">
            <small class="text-muted">
              Chấp nhận jpg, jpeg, png, webp. Dung lượng tối đa 2MB.
            </small>
          </div>

          <div class="alert alert-info mb-0">
            Nếu không chọn ảnh, hệ thống sẽ dùng ảnh mặc định khi hiển thị.
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection