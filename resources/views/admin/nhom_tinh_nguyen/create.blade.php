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
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/dashboard') }}">Tổng quan</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/admin/nhom-tinh-nguyen') }}">Nhóm tình nguyện</a>
          </li>
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

<form action="{{ url('/admin/nhom-tinh-nguyen') }}" method="POST">
  @csrf

  <div class="row">
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-header">
          <h5 class="mb-0">Thông tin nhóm</h5>
        </div>

        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Tên nhóm <span class="text-danger">*</span></label>
            <input type="text" name="tenNhom" class="form-control"
              value="{{ old('tenNhom') }}"
              placeholder="Ví dụ: Nhóm cứu trợ Đà Nẵng">
          </div>

          <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="moTa" class="form-control" rows="4"
              placeholder="Mô tả ngắn về phạm vi hoạt động, mục tiêu hoặc đặc điểm của nhóm">{{ old('moTa') }}</textarea>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Thông tin quản lý</h5>
        </div>

        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Nhóm trưởng <span class="text-danger">*</span></label>
            <select name="idNhomTruong" class="form-control">
              <option value="">-- Chọn nhóm trưởng --</option>

              @foreach ($nguoiDungs as $nguoiDung)
                <option value="{{ $nguoiDung->idNguoiDung }}"
                  {{ old('idNhomTruong') == $nguoiDung->idNguoiDung ? 'selected' : '' }}>
                  {{ $nguoiDung->hoTen }} - {{ $nguoiDung->tenDangNhap }}
                </option>
              @endforeach
            </select>

            <small class="text-muted">
              Nhóm trưởng phải là người dùng đang hoạt động trong hệ thống.
            </small>
          </div>

          <div class="mb-3">
            <label class="form-label">Địa điểm <span class="text-danger">*</span></label>
            <select name="idDiaDiem" class="form-control">
              <option value="">-- Chọn địa điểm --</option>

              @foreach ($diaDiems as $diaDiem)
                <option value="{{ $diaDiem->idDiaDiem }}"
                  {{ old('idDiaDiem') == $diaDiem->idDiaDiem ? 'selected' : '' }}>
                  @if ($diaDiem->chiTietDiaDiem)
                    {{ $diaDiem->chiTietDiaDiem }},
                  @endif

                  @if ($diaDiem->phuongXa)
                    {{ $diaDiem->phuongXa }},
                  @endif

                  {{ $diaDiem->tinhThanh }}
                </option>
              @endforeach
            </select>

            <small class="text-muted">
              Nếu chưa có địa điểm phù hợp, hãy thêm địa điểm trước trong mục Danh mục hệ thống.
            </small>
          </div>

          <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="trangThai" class="form-control">
              <option value="Đang hoạt động" {{ old('trangThai', 'Đang hoạt động') == 'Đang hoạt động' ? 'selected' : '' }}>
                Đang hoạt động
              </option>
              <option value="Bị khóa" {{ old('trangThai') == 'Bị khóa' ? 'selected' : '' }}>
                Bị khóa
              </option>
            </select>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-primary">
          Lưu
        </button>

        <a href="{{ url('/admin/nhom-tinh-nguyen') }}" class="btn btn-secondary">
          Quay lại
        </a>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Ghi chú nghiệp vụ</h5>
        </div>

        <div class="card-body">
          <p class="text-muted mb-3">
            Nhóm tình nguyện là đơn vị tham gia tổ chức chiến dịch, tiếp nhận yêu cầu cứu trợ và thực hiện phân phối.
          </p>

          <div class="alert alert-info mb-0">
            Admin tạo và quản lý thông tin nhóm. Việc thêm thành viên nhóm sẽ được xử lý ở giao diện nhóm trưởng hoặc tình nguyện viên.
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection