@extends('layouts.nhom')

@section('title', 'Thêm thành viên nhóm | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Thêm thành viên nhóm</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/user/nhom-cua-toi') }}">Nhóm của tôi</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/dashboard') }}">{{ $nhom->tenNhom }}</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/nhom/' . $nhom->idNhom . '/thanh-vien') }}">Thành viên</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Thêm</li>
        </ul>
      </div>
    </div>
  </div>
</div>

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

<form action="{{ url('/nhom/' . $nhom->idNhom . '/thanh-vien') }}" method="POST">
  @csrf

  <div class="row">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Thông tin thành viên</h5>
        </div>

        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Người dùng <span class="text-danger">*</span></label>
            <select name="idNguoiDung" class="form-control">
              <option value="">-- Chọn người dùng --</option>

              @foreach ($nguoiDungs as $nguoiDung)
                <option value="{{ $nguoiDung->idNguoiDung }}"
                  {{ old('idNguoiDung') == $nguoiDung->idNguoiDung ? 'selected' : '' }}>
                  {{ $nguoiDung->hoTen }} - {{ $nguoiDung->tenDangNhap }}
                </option>
              @endforeach
            </select>

            <small class="text-muted">
              Chỉ hiển thị người dùng đang hoạt động và chưa thuộc nhóm này.
            </small>
          </div>

          <div class="mb-3">
            <label class="form-label">Vai trò trong nhóm <span class="text-danger">*</span></label>
            <select name="vaiTro" class="form-control">
              <option value="Thành viên" {{ old('vaiTro', 'Thành viên') == 'Thành viên' ? 'selected' : '' }}>
                Thành viên
              </option>
              <option value="Tình nguyện viên" {{ old('vaiTro') == 'Tình nguyện viên' ? 'selected' : '' }}>
                Tình nguyện viên
              </option>
              <option value="Điều phối viên" {{ old('vaiTro') == 'Điều phối viên' ? 'selected' : '' }}>
                Điều phối viên
              </option>
            </select>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              Lưu
            </button>

            <a href="{{ url('/nhom/' . $nhom->idNhom . '/thanh-vien') }}" class="btn btn-secondary">
              Quay lại
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Ghi chú</h5>
        </div>

        <div class="card-body">
          <p class="text-muted mb-0">
            Nhóm trưởng có thể thêm người dùng đã có tài khoản vào nhóm.
            Chức năng người dùng tự gửi yêu cầu tham gia nhóm có thể phát triển ở phiên bản sau.
          </p>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection