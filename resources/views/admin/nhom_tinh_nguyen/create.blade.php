@extends('layouts.admin')

@section('title', 'Thêm nhóm tình nguyện | Cứu Trợ Việt')

@section('content')
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Thêm nhóm tình nguyện</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Tổng quan</a></li>
          <li class="breadcrumb-item"><a href="{{ url('/admin/nhom-tinh-nguyen') }}">Nhóm tình nguyện</a></li>
          <li class="breadcrumb-item" aria-current="page">Thêm</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5>Thông tin nhóm tình nguyện</h5>
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

    <form action="{{ url('/admin/nhom-tinh-nguyen') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label class="form-label">Tên nhóm <span class="text-danger">*</span></label>
        <input type="text" name="tenNhom" class="form-control" value="{{ old('tenNhom') }}"
          placeholder="Ví dụ: Nhóm cứu trợ Đà Nẵng">
      </div>

      <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="moTa" class="form-control" rows="3" placeholder="Mô tả ngắn về nhóm">{{ old('moTa') }}</textarea>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Nhóm trưởng <span class="text-danger">*</span></label>
          <select name="idNhomTruong" class="form-control">
            <option value="">-- Chọn nhóm trưởng --</option>
            @foreach ($nguoiDungs as $nguoiDung)
              <option value="{{ $nguoiDung->idNguoiDung }}" {{ old('idNhomTruong') == $nguoiDung->idNguoiDung ? 'selected' : '' }}>
                {{ $nguoiDung->hoTen }} - {{ $nguoiDung->tenDangNhap }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Địa điểm <span class="text-danger">*</span></label>
          <select name="idDiaDiem" class="form-control">
            <option value="">-- Chọn địa điểm --</option>
            @foreach ($diaDiems as $diaDiem)
              <option value="{{ $diaDiem->idDiaDiem }}" {{ old('idDiaDiem') == $diaDiem->idDiaDiem ? 'selected' : '' }}>
                {{ $diaDiem->tinhThanh }}
                @if ($diaDiem->phuongXa)
                  - {{ $diaDiem->phuongXa }}
                @endif
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Trạng thái</label>
        <select name="trangThai" class="form-control">
          <option value="Đang hoạt động" {{ old('trangThai') == 'Đang hoạt động' ? 'selected' : '' }}>Đang hoạt động</option>
          <option value="Tạm ngưng" {{ old('trangThai') == 'Tạm ngưng' ? 'selected' : '' }}>Tạm ngưng</option>
          <option value="Chờ duyệt" {{ old('trangThai') == 'Chờ duyệt' ? 'selected' : '' }}>Chờ duyệt</option>
        </select>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Lưu</button>
        <a href="{{ url('/admin/nhom-tinh-nguyen') }}" class="btn btn-secondary">Quay lại danh sách</a>
      </div>
    </form>
  </div>
</div>
@endsection