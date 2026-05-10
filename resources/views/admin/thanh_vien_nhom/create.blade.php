@extends('layouts.admin')

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
          <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Tổng quan</a></li>
          <li class="breadcrumb-item"><a href="{{ url('/admin/nhom-tinh-nguyen') }}">Nhóm tình nguyện</a></li>
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/nhom-tinh-nguyen/' . $nhom->idNhom . '/thanh-vien') }}">Thành viên</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Thêm</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5>Thêm thành viên cho: {{ $nhom->tenNhom }}</h5>
  </div>

  <div class="card-body">
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ url('/admin/nhom-tinh-nguyen/' . $nhom->idNhom . '/thanh-vien') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label class="form-label">Người dùng <span class="text-danger">*</span></label>
        <select name="idNguoiDung" class="form-control">
          <option value="">-- Chọn người dùng --</option>
          @foreach ($nguoiDungs as $nguoiDung)
            <option value="{{ $nguoiDung->idNguoiDung }}" {{ old('idNguoiDung') == $nguoiDung->idNguoiDung ? 'selected' : '' }}>
              {{ $nguoiDung->hoTen }} - {{ $nguoiDung->tenDangNhap }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Vai trò trong nhóm <span class="text-danger">*</span></label>
        <select name="vaiTro" class="form-control">
          <option value="Thành viên" {{ old('vaiTro') == 'Thành viên' ? 'selected' : '' }}>Thành viên</option>
          <option value="Nhóm trưởng" {{ old('vaiTro') == 'Nhóm trưởng' ? 'selected' : '' }}>Nhóm trưởng</option>
          <option value="Điều phối viên" {{ old('vaiTro') == 'Điều phối viên' ? 'selected' : '' }}>Điều phối viên</option>
        </select>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Lưu</button>
        <a href="{{ url('/admin/nhom-tinh-nguyen/' . $nhom->idNhom . '/thanh-vien') }}" class="btn btn-secondary">
          Quay lại danh sách
        </a>
      </div>
    </form>
  </div>
</div>
@endsection